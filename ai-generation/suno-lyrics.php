<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
$originHeader = isset($_SERVER['HTTP_ORIGIN']) ? trim((string) $_SERVER['HTTP_ORIGIN']) : '';

$resolveAllowedOrigin = static function (string $origin) {
    if ($origin === '') {
        return null;
    }

    $components = @parse_url($origin);
    if (!is_array($components)) {
        return null;
    }

    $scheme = isset($components['scheme']) ? strtolower((string) $components['scheme']) : '';
    $hostComponent = isset($components['host']) ? strtolower((string) $components['host']) : '';
    $portComponent = isset($components['port']) ? (int) $components['port'] : null;

    if ($scheme === '' || $hostComponent === '') {
        return null;
    }

    $normalizedOrigin = $scheme . '://' . $hostComponent;
    if ($portComponent !== null) {
        $normalizedOrigin .= ':' . $portComponent;
    }

    $originHost = $hostComponent;

    $host = isset($_SERVER['HTTP_HOST']) ? strtolower(trim((string) $_SERVER['HTTP_HOST'])) : '';
    $hosts = [];

    if ($host !== '') {
        $hosts[] = $host;
        if (str_starts_with($host, 'www.')) {
            $hosts[] = substr($host, 4);
        } else {
            $hosts[] = 'www.' . $host;
        }
    }

    $appUrl = getenv('APP_URL');
    if (is_string($appUrl) && trim($appUrl) !== '') {
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            $hosts[] = strtolower($appHost);
        }
    }

    $hosts = array_values(array_unique(array_filter($hosts)));

    if ($hosts === []) {
        return null;
    }

    if (in_array($originHost, $hosts, true)) {
        return $normalizedOrigin;
    }

    return null;
};

$allowedOrigin = $resolveAllowedOrigin($originHeader);

if ($allowedOrigin !== null) {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
}

header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_authentication();

header('Content-Type: application/json; charset=utf-8');

if ($method !== 'POST') {
    http_response_code(405);
    header('Allow: POST, OPTIONS');
    echo json_encode(['ok' => false, 'status' => 405, 'error' => 'Method not allowed.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Normalize .env style assignments by trimming whitespace and removing wrapping quotes.
 */
function normalize_env_value($value): string
{
    $value = trim((string) $value);

    if ($value === '') {
        return $value;
    }

    $first = $value[0];
    $last = substr($value, -1);

    if (($first === '"' || $first === "'") && $last === $first) {
        $value = substr($value, 1, -1);
    }

    return trim($value);
}

/**
 * Load environment variables from .env files when running on shared hosting.
 */
function load_local_env(array $paths): void
{
    foreach ($paths as $path) {
        if (!is_string($path) || $path === '') {
            continue;
        }

        if (!file_exists($path) || !is_readable($path)) {
            continue;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            continue;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$key, $value] = $parts;
            $key = trim($key);
            if ($key === '') {
                continue;
            }

            $value = normalize_env_value($value);

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

load_local_env([
    __DIR__ . '/../.env.local',
    __DIR__ . '/../.env',
]);

$apiKey = normalize_env_value(getenv('SUNO_API_KEY'));
if (!is_string($apiKey) || trim($apiKey) === '') {
    $storedApiKey = get_setting('suno_api_key');
    if (is_string($storedApiKey) && trim($storedApiKey) !== '') {
        $apiKey = normalize_env_value($storedApiKey);
    }
}

$apiKey = is_string($apiKey) ? normalize_env_value($apiKey) : '';

if ($apiKey === '') {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'status' => 500,
        'error' => 'Suno API key is missing. Add it in the admin AI settings.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$rawBody = file_get_contents('php://input');
$decoded = json_decode($rawBody, true);
if (!is_array($decoded)) {
    $decoded = [];
}

$normalizeString = static function ($value): ?string {
    if (is_string($value)) {
        $trimmed = trim($value);
        if ($trimmed !== '') {
            return $trimmed;
        }
    }
    return null;
};

$story = $normalizeString($decoded['story'] ?? null);
$style = $normalizeString($decoded['style'] ?? null);
$voice = $normalizeString($decoded['voice'] ?? null);

if ($story === null) {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'status' => 422,
        'error' => 'A creative brief is required to generate lyrics.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$baseUrl = normalize_env_value(getenv('SUNO_API_BASE_URL'));
$baseUrl = is_string($baseUrl) && trim($baseUrl) !== '' ? rtrim(trim($baseUrl), '/') : 'https://api.sunoapi.com/api/v1';

$path = normalize_env_value(getenv('SUNO_API_LYRICS_PATH'));
$path = is_string($path) && trim($path) !== '' ? trim($path) : '/lyrics/generate';
if (!str_starts_with($path, 'http')) {
    $path = '/' . ltrim($path, '/');
    $targetUrl = $baseUrl . $path;
} else {
    $targetUrl = $path;
}

$requestPayload = array_filter([
    'story' => $story,
    'style' => $style,
    'voice' => $voice,
], static fn ($value) => $value !== null && $value !== '');

$curl = curl_init($targetUrl);
if ($curl === false) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'status' => 500,
        'error' => 'Unable to initialize request to Suno lyric API.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $apiKey,
    'X-API-Key: ' . $apiKey,
    'api-key: ' . $apiKey,
];

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($requestPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT_MS => 45000,
    CURLOPT_CONNECTTIMEOUT_MS => 10000,
]);

$responseBody = curl_exec($curl);
$curlErrNo = curl_errno($curl);
$curlError = curl_error($curl);
$statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($curlErrNo !== 0) {
    http_response_code(502);
    echo json_encode([
        'ok' => false,
        'status' => 502,
        'error' => $curlError !== '' ? $curlError : curl_strerror($curlErrNo),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!is_string($responseBody) || $responseBody === '') {
    http_response_code(502);
    echo json_encode([
        'ok' => false,
        'status' => 502,
        'error' => 'Empty response from Suno lyric service.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$decodedResponse = json_decode($responseBody, true);
if (!is_array($decodedResponse)) {
    http_response_code($statusCode >= 400 ? $statusCode : 502);
    echo json_encode([
        'ok' => false,
        'status' => $statusCode,
        'error' => 'Unexpected response from Suno lyric service.',
        'details' => $responseBody,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($statusCode < 200 || $statusCode >= 300) {
    http_response_code($statusCode);
    echo json_encode([
        'ok' => false,
        'status' => $statusCode,
        'error' => $decodedResponse['error'] ?? $decodedResponse['message'] ?? 'Lyric generation failed.',
        'details' => $decodedResponse,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$lyrics = null;
if (isset($decodedResponse['lyrics']) && is_string($decodedResponse['lyrics'])) {
    $lyrics = trim($decodedResponse['lyrics']);
} elseif (isset($decodedResponse['data']['lyrics']) && is_string($decodedResponse['data']['lyrics'])) {
    $lyrics = trim($decodedResponse['data']['lyrics']);
} elseif (isset($decodedResponse['data']['text']) && is_string($decodedResponse['data']['text'])) {
    $lyrics = trim($decodedResponse['data']['text']);
}

if ($lyrics === null || $lyrics === '') {
    $lyrics = json_encode($decodedResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

echo json_encode([
    'ok' => true,
    'provider' => 'sunoapi.com',
    'lyrics' => $lyrics,
    'raw' => $decodedResponse,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
