<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

const USER_COUNTRY = 'FR';
const ALLOW_SEARCH_FALLBACK = false;
const API_TIMEOUT = 10;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed.',
    ]);
    exit;
}

/**
 * Safely decode the request payload.
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
    if (is_array($decoded)) {
        return $decoded;
    }

    return [];
}

/**
 * Convert a string into a slug.
 */
function slugify(string $value): string
{
    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    if ($normalized === false) {
        $normalized = $value;
    }
    $normalized = preg_replace('/[^A-Za-z0-9]+/u', '-', $normalized ?? '');
    $normalized = trim((string) $normalized, '-');

    return strtolower($normalized ?: '');
}

/**
 * Create a canonical slug for the smartlink.
 */
function create_slug(string $upc, string $slugInput): string
{
    $slugInput = trim($slugInput);
    if ($slugInput !== '') {
        $candidate = slugify($slugInput);
        if ($candidate !== '') {
            return $candidate;
        }
    }

    $trimmedUpc = preg_replace('/[^0-9A-Za-z]/', '', $upc);
    if ($trimmedUpc === null) {
        $trimmedUpc = '';
    }
    $fallback = $trimmedUpc !== '' ? 'release-' . $trimmedUpc : 'release-' . time();

    return slugify($fallback) ?: $fallback;
}

/**
 * Fetch JSON from a remote endpoint.
 *
 * @throws RuntimeException
 *
 * @return array<string, mixed>
 */
function fetch_json(string $url, int $timeout = API_TIMEOUT): array
{
    $handle = curl_init($url);
    if ($handle === false) {
        throw new RuntimeException('Unable to initialise request.');
    }

    curl_setopt_array($handle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_USERAGENT => 'MusicDistroSmartlink/1.0 (+https://musicdistro.io)',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($handle);
    $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($response === false) {
        $error = curl_error($handle);
        curl_close($handle);
        throw new RuntimeException($error !== '' ? $error : 'Request failed.');
    }
    curl_close($handle);

    if ($status < 200 || $status >= 300) {
        throw new RuntimeException('Unexpected response status: ' . $status);
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Invalid JSON response.');
    }

    return $decoded;
}

/**
 * Resolve smartlink metadata via Odesli using a canonical URL.
 *
 * @return array{title?:string, artist?:string, artwork?:string, platforms?: array<string, array{url:string, source:string}>}
 */
function resolve_with_odesli_by_url(string $canonicalUrl, string $country = USER_COUNTRY): array
{
    $result = [
        'title' => null,
        'artist' => null,
        'artwork' => null,
        'platforms' => [],
    ];

    if ($canonicalUrl === '') {
        return $result;
    }

    $override = isset($GLOBALS['ODESLI_COUNTRY_OVERRIDE'])
        ? strtoupper(trim((string) $GLOBALS['ODESLI_COUNTRY_OVERRIDE']))
        : '';
    if ($override !== '') {
        $country = $override;
    }

    $endpoint = sprintf(
        'https://api.song.link/v1-alpha.1/links?url=%s&userCountry=%s',
        rawurlencode($canonicalUrl),
        rawurlencode($country)
    );

    try {
        $data = fetch_json($endpoint, API_TIMEOUT);
    } catch (Throwable $exception) {
        return $result;
    }

    if (!is_array($data)) {
        return $result;
    }

    $entityId = $data['entityUniqueId'] ?? null;
    $entities = $data['entitiesByUniqueId'] ?? [];
    if (is_string($entityId) && is_array($entities) && isset($entities[$entityId]) && is_array($entities[$entityId])) {
        $entity = $entities[$entityId];
        $result['title'] = isset($entity['title']) ? (string) $entity['title'] : null;
        $result['artist'] = isset($entity['artistName']) ? (string) $entity['artistName'] : null;
        $result['artwork'] = isset($entity['thumbnailUrl']) ? (string) $entity['thumbnailUrl'] : null;
    }

    $links = $data['linksByPlatform'] ?? [];
    if (is_array($links)) {
        $platformKeyMap = [
            'spotify' => ['spotify'],
            'apple_music' => ['appleMusic', 'itunes'],
            'deezer' => ['deezer'],
            'amazon_music' => ['amazonMusic'],
            'youtube_music' => ['youtubeMusic', 'youtube'],
            'tidal' => ['tidal'],
            'tiktok' => ['tiktok'],
        ];
        foreach ($platformKeyMap as $platformId => $candidates) {
            foreach ($candidates as $candidate) {
                if (!isset($links[$candidate], $links[$candidate]['url'])) {
                    continue;
                }
                $url = filter_var((string) $links[$candidate]['url'], FILTER_VALIDATE_URL);
                if ($url) {
                    $result['platforms'][$platformId] = [
                        'url' => $url,
                        'source' => 'odesli',
                    ];
                    break;
                }
            }
        }
    }

    return $result;
}

/**
 * Fallback metadata lookup via Apple iTunes API.
 *
 * @return array{title?: string, artist?: string, artwork?: string, platforms?: array<string, array<string, string>>}
 */
function resolve_with_itunes(string $upc): array
{
    $result = [
        'title' => null,
        'artist' => null,
        'artwork' => null,
        'platforms' => [],
    ];

    try {
        $data = fetch_json('https://itunes.apple.com/lookup?country=FR&upc=' . rawurlencode($upc), API_TIMEOUT);
    } catch (Throwable $exception) {
        return $result;
    }

    $results = $data['results'] ?? [];
    if (!is_array($results) || count($results) === 0) {
        return $result;
    }

    $primary = $results[0] ?? [];
    if (is_array($primary)) {
        if (!empty($primary['collectionName'])) {
            $result['title'] = (string) $primary['collectionName'];
        } elseif (!empty($primary['trackName'])) {
            $result['title'] = (string) $primary['trackName'];
        }
        if (!empty($primary['artistName'])) {
            $result['artist'] = (string) $primary['artistName'];
        }
        if (!empty($primary['artworkUrl100'])) {
            $img = (string) $primary['artworkUrl100'];
            $result['artwork'] = preg_replace('/\/\d+x\d+bb\./', '/1000x1000bb.', $img) ?: $img;
        }
        if (!empty($primary['collectionViewUrl'])) {
            $result['platforms']['apple_music'] = [
                'url' => (string) $primary['collectionViewUrl'],
                'source' => 'itunes',
            ];
        } elseif (!empty($primary['trackViewUrl'])) {
            $result['platforms']['apple_music'] = [
                'url' => (string) $primary['trackViewUrl'],
                'source' => 'itunes',
            ];
        }
    }

    return $result;
}

/**
 * Resolve release metadata using MusicBrainz as a fallback.
 *
 * @return array{title?: string|null, artist?: string|null, artwork?: string|null, platforms?: array<string, array<string, string>>, canonical_url?: string|null}
 */
function resolve_with_musicbrainz(string $upc): array
{
    $result = [
        'title' => null,
        'artist' => null,
        'artwork' => null,
        'platforms' => [],
        'canonical_url' => null,
    ];

    if ($upc === '') {
        return $result;
    }

    $queryUrl = sprintf(
        'https://musicbrainz.org/ws/2/release?query=barcode:%s&fmt=json&inc=artist-credits+release-groups+url-rels',
        rawurlencode($upc)
    );

    try {
        $data = fetch_json($queryUrl);
    } catch (Throwable $exception) {
        return $result;
    }

    $releases = $data['releases'] ?? [];
    if (!is_array($releases) || $releases === []) {
        return $result;
    }

    usort($releases, static function ($a, $b): int {
        $scoreA = isset($a['score']) ? (int) $a['score'] : 0;
        $scoreB = isset($b['score']) ? (int) $b['score'] : 0;
        return $scoreB <=> $scoreA;
    });

    $release = $releases[0] ?? null;
    if (!is_array($release)) {
        return $result;
    }

    if (!empty($release['title'])) {
        $result['title'] = (string) $release['title'];
    }

    $artistCredits = $release['artist-credit'] ?? [];
    if (is_array($artistCredits) && $artistCredits !== []) {
        $names = [];
        foreach ($artistCredits as $credit) {
            if (is_array($credit)) {
                if (isset($credit['name']) && is_string($credit['name'])) {
                    $names[] = $credit['name'];
                } elseif (isset($credit['artist']['name']) && is_string($credit['artist']['name'])) {
                    $names[] = $credit['artist']['name'];
                }
            }
        }
        if ($names !== []) {
            $result['artist'] = implode(', ', array_unique($names));
        }
    }

    $releaseId = isset($release['id']) ? (string) $release['id'] : '';
    if ($releaseId !== '') {
        try {
            $coverData = fetch_json('https://coverartarchive.org/release/' . rawurlencode($releaseId));
            if (isset($coverData['images'][0]['image'])) {
                $imageUrl = filter_var((string) $coverData['images'][0]['image'], FILTER_VALIDATE_URL);
                if ($imageUrl) {
                    $result['artwork'] = $imageUrl;
                }
            }
        } catch (Throwable $exception) {
            // Ignore cover art failures.
        }
    }

    $relations = $release['relations'] ?? [];
    $relationSources = is_array($relations) ? $relations : [];
    if (isset($release['release-group']['relations']) && is_array($release['release-group']['relations'])) {
        $relationSources = array_merge($relationSources, $release['release-group']['relations']);
    }

    $platformDomainPatterns = [
        'apple_music' => ['/music\.apple\.com\//i', '/itunes\.apple\.com\//i'],
        'spotify' => ['/open\.spotify\.com\//i', '/play\.spotify\.com\//i'],
        'deezer' => ['/deezer\.com\//i'],
        'amazon_music' => ['/music\.amazon\.[^\/]+\//i', '/amazonmusic\.[^\/]+\//i'],
        'youtube_music' => ['/music\.youtube\.com\//i'],
        'tiktok' => ['/tiktok\.com\//i'],
    ];

    $preferredOrder = ['apple_music', 'spotify', 'deezer', 'amazon_music', 'youtube_music'];
    $fallbackCanonicalPatterns = [
        '/tidal\.com\//i',
        '/youtube\.com\//i',
    ];
    $candidateUrl = null;

    foreach ($relationSources as $relation) {
        if (!is_array($relation) || !isset($relation['url']['resource'])) {
            continue;
        }

        $resource = (string) $relation['url']['resource'];
        $url = filter_var($resource, FILTER_VALIDATE_URL);
        if ($url === false) {
            continue;
        }

        $normalizedUrl = $url;
        if (preg_match('/amazon\.[^\/]+\/(?:gp\/product|dp)\/([A-Z0-9]{10})/i', $url, $matches)) {
            $asin = strtoupper($matches[1]);
            $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
            $hostParts = array_values(array_filter(explode('.', $host)));
            $amazonIndex = array_search('amazon', $hostParts, true);
            $regionHost = 'amazon.com';
            if ($amazonIndex !== false) {
                $regionParts = array_slice($hostParts, $amazonIndex);
                if ($regionParts !== []) {
                    $regionHost = implode('.', $regionParts);
                }
            }
            $normalizedUrl = sprintf('https://music.%s/albums/%s', $regionHost, $asin);
        }

        foreach ($platformDomainPatterns as $platformId => $patterns) {
            if (isset($result['platforms'][$platformId])) {
                continue;
            }
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $normalizedUrl)) {
                    $result['platforms'][$platformId] = [
                        'url' => $normalizedUrl,
                        'source' => 'musicbrainz',
                    ];
                    if ($candidateUrl === null && in_array($platformId, $preferredOrder, true)) {
                        $candidateUrl = $normalizedUrl;
                    }
                    continue 2;
                }
            }
        }

        if ($candidateUrl === null) {
            foreach ($fallbackCanonicalPatterns as $pattern) {
                if (preg_match($pattern, $normalizedUrl)) {
                    $candidateUrl = $normalizedUrl;
                    break;
                }
            }
        }
    }

    if ($candidateUrl !== null) {
        $result['canonical_url'] = $candidateUrl;
    }

    return $result;
}

/**
 * Merge metadata results, preferring the first non-empty value.
 */
function merge_metadata(array $primary, array $fallback): array
{
    $merged = $primary;
    foreach (['title', 'artist', 'artwork'] as $key) {
        if (empty($merged[$key]) && !empty($fallback[$key])) {
            $merged[$key] = $fallback[$key];
        }
    }

    if (!isset($merged['platforms']) || !is_array($merged['platforms'])) {
        $merged['platforms'] = [];
    }
    if (isset($fallback['platforms']) && is_array($fallback['platforms'])) {
        foreach ($fallback['platforms'] as $platformId => $platformData) {
            if (!isset($merged['platforms'][$platformId]) && isset($platformData['url'])) {
                $merged['platforms'][$platformId] = $platformData;
            }
        }
    }

    return $merged;
}

/**
 * Build the final platform list using resolved URLs with search fallbacks.
 *
 * @param array<string, array<string, string>> $resolved
 * @param array<int, array<string, mixed>> $platformConfigs
 * @param array<int, string> $platformIds
 *
 * @return array<int, array<string, mixed>>
 */
function build_platforms(array $resolved, array $platformConfigs, array $platformIds, string $slug, string $upc): array
{
    $index = [];
    foreach ($platformConfigs as $config) {
        if (!isset($config['id'])) {
            continue;
        }
        $index[(string) $config['id']] = $config;
    }

    $searchTerm = rawurlencode($upc !== '' ? $upc : $slug);
    $platforms = [];
    foreach ($platformIds as $platformId) {
        $platformId = (string) $platformId;
        $config = $index[$platformId] ?? [
            'id' => $platformId,
            'label' => $platformId,
            'logo' => '',
            'color' => '#6366f1',
            'url_prefix' => '',
        ];
        $resolvedUrl = $resolved[$platformId]['url'] ?? '';
        $source = $resolved[$platformId]['source'] ?? '';
        if ($resolvedUrl === '' && !empty($config['url_prefix'])) {
            if (ALLOW_SEARCH_FALLBACK) {
                $resolvedUrl = (string) $config['url_prefix'] . $searchTerm;
                $source = 'search';
            } else {
                $resolvedUrl = '';
                $source = '';
            }
        }
        // Do not filter out valid YouTube Music playlist URLs (e.g. music.youtube.com/playlist?list=OLAK5...).
        $platforms[] = [
            'id' => (string) ($config['id'] ?? $platformId),
            'label' => (string) ($config['label'] ?? $platformId),
            'logo' => (string) ($config['logo'] ?? ''),
            'color' => (string) ($config['color'] ?? '#6366f1'),
            'url' => $resolvedUrl,
            'source' => $source,
        ];
    }

    return $platforms;
}

/**
 * Load stored smartlink entries from disk.
 *
 * @return array<string, array<string, mixed>>
 */
function load_smartlink_storage(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $contents = file_get_contents($path);
    if ($contents === false || $contents === '') {
        return [];
    }
    $decoded = json_decode($contents, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Persist smartlink entries to disk with file locking.
 */
function save_smartlink_storage(string $path, array $entries): void
{
    $directory = dirname($path);
    if (!is_dir($directory)) {
        if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create storage directory.');
        }
    }

    $tmpPath = $path . '.tmp';
    $encoded = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        throw new RuntimeException('Unable to encode smartlink storage.');
    }

    $bytes = file_put_contents($tmpPath, $encoded);
    if ($bytes === false) {
        throw new RuntimeException('Unable to write smartlink storage.');
    }

    if (!rename($tmpPath, $path)) {
        throw new RuntimeException('Unable to persist smartlink storage.');
    }
}

$payload = read_json_payload();
$odesliCountryOverride = isset($payload['odesliCountry'])
    ? strtoupper(trim((string) $payload['odesliCountry']))
    : '';
if ($odesliCountryOverride !== '') {
    $GLOBALS['ODESLI_COUNTRY_OVERRIDE'] = $odesliCountryOverride;
}
$upc = isset($payload['upc']) ? trim((string) $payload['upc']) : '';
$slugInput = isset($payload['slug']) ? (string) $payload['slug'] : '';
$platformIds = $payload['platformIds'] ?? [];
if (!is_array($platformIds)) {
    $platformIds = [];
}
$platformIds = array_values(array_unique(array_filter(array_map(static function ($value) {
    return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $value) ?? '';
}, $platformIds), static fn ($value) => $value !== '')));

if ($upc === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error' => 'UPC is required.',
    ]);
    exit;
}

if (empty($platformIds)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error' => 'At least one platform is required.',
    ]);
    exit;
}

$slug = create_slug($upc, $slugInput);
$smartlinkPlatforms = require __DIR__ . '/smartlink-platforms.php';

$metadataItunes = resolve_with_itunes($upc);
$metadata = $metadataItunes;

$canonicalUrl = '';
if (isset($metadataItunes['platforms']['apple_music']['url'])) {
    $candidate = (string) $metadataItunes['platforms']['apple_music']['url'];
    if ($candidate !== '') {
        $canonicalUrl = $candidate;
    }
}

if ($canonicalUrl !== '') {
    $metadata = merge_metadata(resolve_with_odesli_by_url($canonicalUrl, USER_COUNTRY), $metadata);
} else {
    $metadataMb = resolve_with_musicbrainz($upc);
    $metadata = merge_metadata($metadata, $metadataMb);
    if (!isset($metadata['canonical_url']) && isset($metadataMb['canonical_url'])) {
        $metadata['canonical_url'] = $metadataMb['canonical_url'];
    }

    $mbCanonical = isset($metadataMb['canonical_url']) ? (string) $metadataMb['canonical_url'] : '';
    if ($mbCanonical !== '') {
        $metadata = merge_metadata(resolve_with_odesli_by_url($mbCanonical, USER_COUNTRY), $metadata);
    }
}

$resolvedPlatforms = isset($metadata['platforms']) && is_array($metadata['platforms'])
    ? $metadata['platforms']
    : [];

$shareBase = rtrim(APP_URL, '/') . '/musiclink/';
$storagePath = __DIR__ . '/storage/smartlinks.json';
$existingEntries = load_smartlink_storage($storagePath);
$existing = $existingEntries[$slug] ?? null;
$createdAt = is_array($existing) && isset($existing['created_at']) ? (string) $existing['created_at'] : date('c');
$id = is_array($existing) && isset($existing['id']) ? (string) $existing['id'] : ('sm-' . bin2hex(random_bytes(6)));
$updatedAt = date('c');

$title = isset($metadata['title']) ? (string) $metadata['title'] : '';
$artist = isset($metadata['artist']) ? (string) $metadata['artist'] : '';
$artwork = isset($metadata['artwork']) ? (string) $metadata['artwork'] : '';

$platforms = build_platforms($resolvedPlatforms, $smartlinkPlatforms, $platformIds, $slug, $upc);

$hasResolvablePlatform = false;
foreach ($platforms as $platform) {
    if (($platform['url'] ?? '') !== '' && ($platform['source'] ?? '') !== 'search') {
        $hasResolvablePlatform = true;
        break;
    }
}

if (!$hasResolvablePlatform) {
    http_response_code(424);
    echo json_encode([
        'success' => false,
        'error' => 'UPC could not be resolved to a canonical URL (no YouTube Music or DSP links from Odesli).',
        'code' => 'UPC_NOT_RESOLVED',
    ]);
    exit;
}

$entry = [
    'id' => $id,
    'slug' => $slug,
    'upc' => $upc,
    'title' => $title,
    'artist' => $artist,
    'artwork' => $artwork,
    'share_url' => $shareBase . $slug,
    'platforms' => $platforms,
    'platform_ids' => $platformIds,
    'created_at' => $createdAt,
    'updated_at' => $updatedAt,
];

$existingEntries[$slug] = $entry;

try {
    save_smartlink_storage($storagePath, $existingEntries);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unable to persist smartlink data.',
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => [
        'id' => $id,
        'slug' => $slug,
        'upc' => $upc,
        'title' => $title,
        'artist' => $artist,
        'artwork' => $artwork,
        'share_url' => $shareBase . $slug,
        'platforms' => $platforms,
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ],
]);
