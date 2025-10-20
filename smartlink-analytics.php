<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$analyticsPath = __DIR__ . '/storage/smartlink-analytics.json';
$smartlinksPath = __DIR__ . '/storage/smartlinks.json';
$platformConfigs = require __DIR__ . '/smartlink-platforms.php';

/**
 * Decode JSON payload from the request body.
 *
 * @return array<string, mixed>
 */
function read_json_payload(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

function sanitize_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9\-]/', '', $value);

    return $value !== null ? $value : '';
}

function sanitize_platform_id(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9_\-]/', '', $value);

    return $value !== null ? $value : '';
}

function normalize_client_id(?string $value): string
{
    $value = trim((string) $value);
    $value = preg_replace('/[^A-Za-z0-9_\-]/', '', $value);

    return $value !== null ? $value : '';
}

function hash_client_id(string $clientId): string
{
    return hash('sha256', $clientId);
}

/**
 * @return array<string, array<string, mixed>>
 */
function load_json_storage(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return [];
    }

    if (!flock($handle, LOCK_SH)) {
        fclose($handle);

        return [];
    }

    $contents = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    if ($contents === false || $contents === '') {
        return [];
    }

    $decoded = json_decode($contents, true);

    return is_array($decoded) ? $decoded : [];
}

function ensure_storage_directory(string $path): void
{
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

function save_json_storage(string $path, array $data): void
{
    ensure_storage_directory($path);

    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        throw new RuntimeException('Unable to encode analytics payload.');
    }

    $tmpPath = $path . '.tmp';
    $bytes = file_put_contents($tmpPath, $encoded);
    if ($bytes === false) {
        throw new RuntimeException('Unable to write analytics payload.');
    }

    if (!rename($tmpPath, $path)) {
        throw new RuntimeException('Unable to persist analytics payload.');
    }
}

/**
 * @return array<string, mixed>|null
 */
function load_smartlink_entry(string $path, string $slug): ?array
{
    if (!is_file($path)) {
        return null;
    }

    $contents = file_get_contents($path);
    if ($contents === false || $contents === '') {
        return null;
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded) || !isset($decoded[$slug]) || !is_array($decoded[$slug])) {
        return null;
    }

    return $decoded[$slug];
}

function detect_country_code(array $payload): string
{
    $candidates = [
        $payload['country'] ?? '',
        $_SERVER['HTTP_CF_IPCOUNTRY'] ?? '',
        $_SERVER['HTTP_X_APPENGINE_COUNTRY'] ?? '',
        $_SERVER['HTTP_X_FORWARDED_COUNTRY'] ?? '',
    ];

    foreach ($candidates as $candidate) {
        $code = strtoupper(trim((string) $candidate));
        if ($code !== '' && preg_match('/^[A-Z]{2}$/', $code)) {
            return $code;
        }
    }

    $acceptLanguage = (string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    if ($acceptLanguage !== '') {
        foreach (explode(',', $acceptLanguage) as $segment) {
            $segment = trim($segment);
            if ($segment === '' || $segment === '*') {
                continue;
            }

            $parts = explode(';', $segment);
            $locale = (string) array_shift($parts);
            if ($locale === '') {
                continue;
            }

            $locale = str_replace('_', '-', $locale);
            if (strpos($locale, '-') !== false) {
                $fragments = explode('-', $locale);
                $candidate = strtoupper(end($fragments));
                if ($candidate !== '' && preg_match('/^[A-Z]{2}$/', $candidate)) {
                    return $candidate;
                }
            }
        }
    }

    return '';
}

function detect_city(array $payload): string
{
    $candidates = [
        $payload['city'] ?? '',
        $_SERVER['HTTP_CF_IPCITY'] ?? '',
        $_SERVER['HTTP_X_APPENGINE_CITY'] ?? '',
    ];

    foreach ($candidates as $candidate) {
        $candidate = trim((string) $candidate);
        if ($candidate === '') {
            continue;
        }

        $sanitized = preg_replace('/[^\p{L}\p{N}\s\-\.,]/u', '', $candidate);
        if ($sanitized === null) {
            $sanitized = preg_replace('/[^A-Za-z0-9 \-\.,]/', '', $candidate);
        }
        if ($sanitized !== null && $sanitized !== '') {
            return $sanitized;
        }
    }

    return '';
}

/**
 * @return array{x: float, y: float}
 */
function country_coordinates(string $code): array
{
    static $map = [
        'US' => ['x' => 22.0, 'y' => 42.0],
        'CA' => ['x' => 24.0, 'y' => 30.0],
        'MX' => ['x' => 27.0, 'y' => 55.0],
        'BR' => ['x' => 34.0, 'y' => 72.0],
        'AR' => ['x' => 38.0, 'y' => 82.0],
        'GB' => ['x' => 43.0, 'y' => 32.0],
        'FR' => ['x' => 46.0, 'y' => 36.0],
        'DE' => ['x' => 50.0, 'y' => 34.0],
        'ES' => ['x' => 44.0, 'y' => 40.0],
        'IT' => ['x' => 48.0, 'y' => 44.0],
        'NL' => ['x' => 49.0, 'y' => 32.0],
        'BE' => ['x' => 47.0, 'y' => 34.0],
        'SE' => ['x' => 52.0, 'y' => 24.0],
        'NO' => ['x' => 52.0, 'y' => 20.0],
        'DK' => ['x' => 51.0, 'y' => 28.0],
        'PL' => ['x' => 53.5, 'y' => 34.5],
        'IE' => ['x' => 40.0, 'y' => 32.0],
        'PT' => ['x' => 41.0, 'y' => 43.5],
        'RU' => ['x' => 60.0, 'y' => 28.0],
        'UA' => ['x' => 56.0, 'y' => 36.0],
        'TR' => ['x' => 56.0, 'y' => 44.0],
        'IL' => ['x' => 59.0, 'y' => 46.0],
        'IN' => ['x' => 66.0, 'y' => 56.0],
        'CN' => ['x' => 72.0, 'y' => 44.0],
        'JP' => ['x' => 78.0, 'y' => 40.0],
        'KR' => ['x' => 76.0, 'y' => 42.0],
        'AU' => ['x' => 82.0, 'y' => 78.0],
        'NZ' => ['x' => 88.0, 'y' => 82.0],
        'ZA' => ['x' => 53.0, 'y' => 82.0],
        'NG' => ['x' => 50.0, 'y' => 60.0],
        'KE' => ['x' => 56.0, 'y' => 64.0],
        'EG' => ['x' => 54.0, 'y' => 52.0],
        'MA' => ['x' => 44.0, 'y' => 48.0],
        'SA' => ['x' => 60.0, 'y' => 52.0],
        'AE' => ['x' => 63.0, 'y' => 52.0],
        'CL' => ['x' => 36.0, 'y' => 88.0],
        'CO' => ['x' => 32.0, 'y' => 66.0],
        'PE' => ['x' => 33.5, 'y' => 76.0],
        'VE' => ['x' => 34.0, 'y' => 62.0],
        'SG' => ['x' => 71.5, 'y' => 66.0],
        'TH' => ['x' => 70.0, 'y' => 58.0],
        'PH' => ['x' => 76.0, 'y' => 56.0],
        'ID' => ['x' => 74.0, 'y' => 68.0],
    ];

    $code = strtoupper($code);
    if (isset($map[$code])) {
        return $map[$code];
    }

    return ['x' => 50.0, 'y' => 50.0];
}

function country_name(string $code): string
{
    static $names = [
        'US' => 'United States',
        'CA' => 'Canada',
        'MX' => 'Mexico',
        'BR' => 'Brazil',
        'AR' => 'Argentina',
        'GB' => 'United Kingdom',
        'FR' => 'France',
        'DE' => 'Germany',
        'ES' => 'Spain',
        'IT' => 'Italy',
        'NL' => 'Netherlands',
        'BE' => 'Belgium',
        'SE' => 'Sweden',
        'NO' => 'Norway',
        'DK' => 'Denmark',
        'PL' => 'Poland',
        'IE' => 'Ireland',
        'PT' => 'Portugal',
        'RU' => 'Russia',
        'UA' => 'Ukraine',
        'TR' => 'Türkiye',
        'IL' => 'Israel',
        'IN' => 'India',
        'CN' => 'China',
        'JP' => 'Japan',
        'KR' => 'South Korea',
        'AU' => 'Australia',
        'NZ' => 'New Zealand',
        'ZA' => 'South Africa',
        'NG' => 'Nigeria',
        'KE' => 'Kenya',
        'EG' => 'Egypt',
        'MA' => 'Morocco',
        'SA' => 'Saudi Arabia',
        'AE' => 'United Arab Emirates',
        'CL' => 'Chile',
        'CO' => 'Colombia',
        'PE' => 'Peru',
        'VE' => 'Venezuela',
        'SG' => 'Singapore',
        'TH' => 'Thailand',
        'PH' => 'Philippines',
        'ID' => 'Indonesia',
    ];

    $code = strtoupper($code);

    return $names[$code] ?? $code;
}

function prune_timeline(array $timeline, int $maxDays = 180): array
{
    if (count($timeline) <= $maxDays) {
        ksort($timeline);

        return $timeline;
    }

    ksort($timeline);
    $slice = array_slice($timeline, -$maxDays, null, true);

    return $slice;
}

function record_unique(array &$bucket, string $hash, string $timestamp, int $limit = 5000): void
{
    $bucket[$hash] = $timestamp;
    if (count($bucket) > $limit) {
        asort($bucket);
        $bucket = array_slice($bucket, -$limit, null, true);
    }
}

function build_timeline_series(array $timeline, int $days = 14): array
{
    $series = [];
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    for ($offset = $days - 1; $offset >= 0; $offset--) {
        $date = $now->sub(new DateInterval('P' . $offset . 'D'));
        $key = $date->format('Y-m-d');
        $entry = $timeline[$key] ?? [];
        $series[] = [
            'date' => $key,
            'views' => (int) ($entry['views'] ?? 0),
            'clicks' => (int) ($entry['clicks'] ?? 0),
            'copies' => (int) ($entry['copies'] ?? 0),
        ];
    }

    return $series;
}

function transform_entry(string $slug, array $entry, array $smartlink, array $platformConfigs): array
{
    $views = (int) ($entry['views'] ?? 0);
    $clicks = (int) ($entry['clicks'] ?? 0);
    $copies = (int) ($entry['copies'] ?? 0);
    $uniqueClicks = isset($entry['unique_clicks']) && is_array($entry['unique_clicks'])
        ? count($entry['unique_clicks'])
        : 0;
    $uniqueCopies = isset($entry['unique_copies']) && is_array($entry['unique_copies'])
        ? count($entry['unique_copies'])
        : 0;

    $platformTotals = [];
    if (isset($entry['platforms']) && is_array($entry['platforms'])) {
        foreach ($entry['platforms'] as $platformId => $data) {
            $platformId = (string) $platformId;
            $clickCount = (int) ($data['clicks'] ?? 0);
            $unique = isset($data['unique']) && is_array($data['unique']) ? count($data['unique']) : 0;
            $platformTotals[$platformId] = [
                'clicks' => $clickCount,
                'unique_clicks' => $unique,
            ];
        }
    }

    $totalPlatformClicks = array_sum(array_column($platformTotals, 'clicks')) ?: 0;
    $platformList = [];
    foreach ($platformTotals as $platformId => $data) {
        $config = null;
        foreach ($platformConfigs as $item) {
            if (($item['id'] ?? '') === $platformId) {
                $config = $item;
                break;
            }
        }
        $label = $config['label'] ?? ucfirst(str_replace('_', ' ', $platformId));
        $color = $config['color'] ?? '#6366f1';
        $percentage = $totalPlatformClicks > 0 ? ($data['clicks'] / $totalPlatformClicks) * 100 : 0.0;
        $platformList[] = [
            'id' => $platformId,
            'label' => $label,
            'color' => $color,
            'clicks' => $data['clicks'],
            'unique_clicks' => $data['unique_clicks'],
            'percentage' => $percentage,
        ];
    }
    usort($platformList, static function (array $a, array $b): int {
        return $b['clicks'] <=> $a['clicks'];
    });

    $timeline = isset($entry['timeline']) && is_array($entry['timeline']) ? $entry['timeline'] : [];
    $timelineSeries = build_timeline_series($timeline);

    $countriesRaw = isset($entry['countries']) && is_array($entry['countries']) ? $entry['countries'] : [];
    arsort($countriesRaw);
    $countryList = [];
    foreach ($countriesRaw as $code => $count) {
        $code = strtoupper((string) $code);
        $count = (int) $count;
        if ($count <= 0) {
            continue;
        }
        $coords = country_coordinates($code);
        $countryList[] = [
            'code' => $code,
            'name' => country_name($code),
            'count' => $count,
            'percentage' => $views > 0 ? ($count / $views) * 100 : 0.0,
            'coords' => $coords,
        ];
        if (count($countryList) >= 10) {
            break;
        }
    }

    $citiesRaw = isset($entry['cities']) && is_array($entry['cities']) ? $entry['cities'] : [];
    arsort($citiesRaw);
    $cityList = [];
    foreach ($citiesRaw as $key => $count) {
        $count = (int) $count;
        if ($count <= 0) {
            continue;
        }
        $parts = explode('|', (string) $key, 2);
        $countryCode = strtoupper($parts[0] ?? '');
        $cityName = trim($parts[1] ?? '');
        if ($cityName === '') {
            continue;
        }
        $cityList[] = [
            'city' => $cityName,
            'country_code' => $countryCode,
            'country' => country_name($countryCode),
            'count' => $count,
            'percentage' => $views > 0 ? ($count / $views) * 100 : 0.0,
        ];
        if (count($cityList) >= 10) {
            break;
        }
    }

    return [
        'id' => (string) ($entry['id'] ?? $smartlink['id'] ?? ''),
        'slug' => $slug,
        'title' => (string) ($smartlink['title'] ?? ''),
        'artist' => (string) ($smartlink['artist'] ?? ''),
        'upc' => (string) ($smartlink['upc'] ?? ''),
        'share_url' => (string) ($smartlink['share_url'] ?? ''),
        'created_at' => (string) ($smartlink['created_at'] ?? ''),
        'updated_at' => (string) ($entry['updated_at'] ?? $smartlink['updated_at'] ?? ''),
        'stats' => [
            'views' => $views,
            'clicks' => $clicks,
            'copies' => $copies,
            'unique_clicks' => $uniqueClicks,
            'unique_copies' => $uniqueCopies,
            'platforms' => $platformList,
            'timeline' => $timelineSeries,
            'countries' => $countryList,
            'cities' => $cityList,
        ],
    ];
}

if ($method === 'POST') {
    $payload = read_json_payload();
    $slug = sanitize_slug((string) ($payload['slug'] ?? ''));
    if ($slug === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Missing smartlink slug.']);
        exit;
    }

    $smartlink = load_smartlink_entry($smartlinksPath, $slug);
    if ($smartlink === null) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Smartlink not found.']);
        exit;
    }

    $analytics = load_json_storage($analyticsPath);
    $entry = isset($analytics[$slug]) && is_array($analytics[$slug]) ? $analytics[$slug] : [];
    $entry['id'] = (string) ($smartlink['id'] ?? ($entry['id'] ?? ''));

    $event = strtolower(trim((string) ($payload['event'] ?? 'view')));
    if (!in_array($event, ['view', 'click', 'copy'], true)) {
        $event = 'view';
    }

    $platformId = $event === 'click' ? sanitize_platform_id((string) ($payload['platformId'] ?? '')) : '';
    $clientId = normalize_client_id($payload['clientId'] ?? null);
    $clientHash = $clientId !== '' ? hash_client_id($clientId) : '';

    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $timestamp = $now->format(DateTimeInterface::ATOM);
    $dateKey = $now->format('Y-m-d');

    if (!isset($entry['timeline']) || !is_array($entry['timeline'])) {
        $entry['timeline'] = [];
    }
    if (!isset($entry['timeline'][$dateKey]) || !is_array($entry['timeline'][$dateKey])) {
        $entry['timeline'][$dateKey] = ['views' => 0, 'clicks' => 0, 'copies' => 0];
    }

    if ($event === 'view') {
        $entry['views'] = (int) ($entry['views'] ?? 0) + 1;
        $entry['timeline'][$dateKey]['views']++;
        $country = detect_country_code($payload);
        if ($country !== '') {
            if (!isset($entry['countries']) || !is_array($entry['countries'])) {
                $entry['countries'] = [];
            }
            $entry['countries'][$country] = (int) ($entry['countries'][$country] ?? 0) + 1;
            $city = detect_city($payload);
            if ($city !== '') {
                if (!isset($entry['cities']) || !is_array($entry['cities'])) {
                    $entry['cities'] = [];
                }
                $key = $country . '|' . $city;
                $entry['cities'][$key] = (int) ($entry['cities'][$key] ?? 0) + 1;
            }
        }
    } elseif ($event === 'click') {
        $entry['clicks'] = (int) ($entry['clicks'] ?? 0) + 1;
        $entry['timeline'][$dateKey]['clicks']++;
        if (!isset($entry['platforms']) || !is_array($entry['platforms'])) {
            $entry['platforms'] = [];
        }
        if ($platformId !== '') {
            if (!isset($entry['platforms'][$platformId]) || !is_array($entry['platforms'][$platformId])) {
                $entry['platforms'][$platformId] = ['clicks' => 0, 'unique' => []];
            }
            $entry['platforms'][$platformId]['clicks'] = (int) ($entry['platforms'][$platformId]['clicks'] ?? 0) + 1;
            if ($clientHash !== '') {
                if (!isset($entry['platforms'][$platformId]['unique']) || !is_array($entry['platforms'][$platformId]['unique'])) {
                    $entry['platforms'][$platformId]['unique'] = [];
                }
                record_unique($entry['platforms'][$platformId]['unique'], $clientHash, $timestamp);
            }
        }
        if ($clientHash !== '') {
            if (!isset($entry['unique_clicks']) || !is_array($entry['unique_clicks'])) {
                $entry['unique_clicks'] = [];
            }
            record_unique($entry['unique_clicks'], $clientHash, $timestamp);
        }
    } elseif ($event === 'copy') {
        $entry['copies'] = (int) ($entry['copies'] ?? 0) + 1;
        $entry['timeline'][$dateKey]['copies']++;
        if ($clientHash !== '') {
            if (!isset($entry['unique_copies']) || !is_array($entry['unique_copies'])) {
                $entry['unique_copies'] = [];
            }
            record_unique($entry['unique_copies'], $clientHash, $timestamp);
        }
    }

    $entry['timeline'] = prune_timeline($entry['timeline']);
    $entry['updated_at'] = $timestamp;

    $analytics[$slug] = $entry;

    try {
        save_json_storage($analyticsPath, $analytics);
    } catch (Throwable $exception) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Unable to persist analytics.']);
        exit;
    }

    $response = transform_entry($slug, $entry, $smartlink, $platformConfigs);
    echo json_encode(['success' => true, 'data' => $response]);
    exit;
}

if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$slug = sanitize_slug((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Missing smartlink slug.']);
    exit;
}

$smartlink = load_smartlink_entry($smartlinksPath, $slug);
if ($smartlink === null) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Smartlink not found.']);
    exit;
}

$analytics = load_json_storage($analyticsPath);
$entry = isset($analytics[$slug]) && is_array($analytics[$slug]) ? $analytics[$slug] : [];
$response = transform_entry($slug, $entry, $smartlink, $platformConfigs);

echo json_encode(['success' => true, 'data' => $response]);
