<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

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
 * Load environment variables from the provided .env-style files if not already defined.
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
    __DIR__ . '/.env.local',
    __DIR__ . '/.env',
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
    echo json_encode(
        [
            'ok' => false,
            'status' => 500,
            'error' => 'Suno API key is not configured. Add it in the admin settings or set SUNO_API_KEY in your environment.',
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

$rawInput = file_get_contents('php://input');
if ($rawInput === false) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'status' => 400, 'error' => 'Unable to read request body.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$decoded = json_decode($rawInput, true);
if (!is_array($decoded)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'status' => 400, 'error' => 'Invalid JSON payload.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$normalizeString = static function ($value): ?string {
    if (!is_string($value)) {
        return null;
    }
    $trimmed = trim($value);
    return $trimmed !== '' ? $trimmed : null;
};

$normalizeDuration = static function ($value): ?int {
    if (is_int($value) && $value > 0) {
        return $value;
    }
    if (is_float($value) && $value > 0) {
        return (int) round($value);
    }
    if (is_string($value)) {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        $parsed = (int) $trimmed;
        if ($parsed > 0) {
            return $parsed;
        }
    }
    return null;
};

$mode = $normalizeString($decoded['mode'] ?? null);
if ($mode !== 'custom' && $mode !== 'no-custom') {
    $mode = 'custom';
}

$style = $normalizeString($decoded['style'] ?? null) ?? 'electro';
$duration = $normalizeDuration($decoded['duration'] ?? null) ?? 60;
$lyrics = $normalizeString($decoded['lyrics'] ?? null);
$webhookUrl = $normalizeString($decoded['webhook_url'] ?? null);
$webhookSecret = $normalizeString($decoded['webhook_secret'] ?? null);
$title = $normalizeString($decoded['title'] ?? null);

if ($webhookUrl !== null && filter_var($webhookUrl, FILTER_VALIDATE_URL) === false) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'status' => 400, 'error' => 'Invalid webhook_url provided.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$baseUrl = normalize_env_value(getenv('SUNO_API_BASE_URL'));
$baseUrl = is_string($baseUrl) && trim($baseUrl) !== '' ? rtrim(trim($baseUrl), '/') : 'https://api.sunoapi.com/api/v1';

$path = normalize_env_value(getenv('SUNO_API_GENERATE_PATH'));
$path = is_string($path) && trim($path) !== '' ? trim($path) : '/suno/create';
if (!str_starts_with($path, 'http')) {
    $path = '/' . ltrim($path, '/');
    $targetUrl = $baseUrl . $path;
} else {
    $targetUrl = $path;
}

$timeoutMsEnv = normalize_env_value(getenv('SUNO_API_TIMEOUT_MS'));
$timeoutMs = is_string($timeoutMsEnv) && trim($timeoutMsEnv) !== '' ? (int) $timeoutMsEnv : 60000;
if ($timeoutMs <= 0) {
    $timeoutMs = 60000;
}

$requestPayload = array_filter(
    [
        'action' => 'create',
        'mode' => $mode,
        'lyrics' => $lyrics,
        'style' => $style,
        'duration' => $duration,
        'webhook_url' => $webhookUrl,
        'webhook_secret' => $webhookSecret,
        'title' => $title,
    ],
    static fn ($value) => $value !== null && $value !== ''
);

$attempts = 0;
$maxAttempts = 3;
$retryStatuses = [502, 503, 504];
$isDev = in_array(strtolower((string) getenv('APP_ENV')), ['local', 'development', 'dev'], true) || getenv('APP_DEBUG') === 'true';

$lastError = null;
$lastStatus = null;
$lastBody = null;

while ($attempts < $maxAttempts) {
    $attempts++;

    $curl = curl_init($targetUrl);
    if ($curl === false) {
        $lastError = 'Unable to initialize cURL.';
        break;
    }

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $apiKey,
        'X-API-Key: ' . $apiKey,
        'api-key: ' . $apiKey,
    ];

    $userAgent = 'MusicDistroAIComposer/1.0';
    if (defined('APP_URL')) {
        $userAgent .= ' (+ ' . APP_URL . ')';
    }

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_USERAGENT => $userAgent,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POSTFIELDS => json_encode($requestPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT_MS => $timeoutMs,
        CURLOPT_CONNECTTIMEOUT_MS => min($timeoutMs, 10000),
    ]);

    $responseBody = curl_exec($curl);
    $curlErrNo = curl_errno($curl);
    $curlError = curl_error($curl);
    $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $lastStatus = $statusCode;
    $lastBody = $responseBody;

    if ($curlErrNo !== 0) {
        $lastError = $curlError !== '' ? $curlError : curl_strerror($curlErrNo);
        if ($attempts < $maxAttempts) {
            usleep((300 + random_int(0, 500)) * 1000);
            continue;
        }
        break;
    }

    if (!is_string($responseBody)) {
        $lastError = 'Empty response from upstream service.';
        if ($attempts < $maxAttempts) {
            usleep((300 + random_int(0, 500)) * 1000);
            continue;
        }
        break;
    }

    $decodedResponse = json_decode($responseBody, true);
    if (!is_array($decodedResponse)) {
        $decodedResponse = ['message' => $responseBody];
    }

    if ($statusCode < 200 || $statusCode >= 300) {
        $lastError = $decodedResponse['error'] ?? $decodedResponse['message'] ?? 'Upstream request failed.';
        if (in_array($statusCode, $retryStatuses, true) && $attempts < $maxAttempts) {
            if ($isDev) {
                error_log('[SunoAPI] Retryable HTTP status ' . $statusCode . ' received. Attempt ' . $attempts);
            }
            usleep((300 + random_int(0, 500)) * 1000);
            continue;
        }

        http_response_code($statusCode);
        echo json_encode(
            [
                'ok' => false,
                'status' => $statusCode,
                'error' => is_string($lastError) ? $lastError : 'Request to Suno API failed.',
                'details' => $decodedResponse,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    $requestId = null;
    if (isset($decodedResponse['id']) && is_string($decodedResponse['id'])) {
        $requestId = $decodedResponse['id'];
    } elseif (isset($decodedResponse['task_id']) && is_string($decodedResponse['task_id'])) {
        $requestId = $decodedResponse['task_id'];
    } elseif (isset($decodedResponse['request_id']) && is_string($decodedResponse['request_id'])) {
        $requestId = $decodedResponse['request_id'];
    }

    $status = isset($decodedResponse['status']) && is_string($decodedResponse['status']) ? $decodedResponse['status'] : null;
    $message = isset($decodedResponse['message']) && is_string($decodedResponse['message']) ? $decodedResponse['message'] : null;
    $lyricsResponse = isset($decodedResponse['lyrics']) && is_string($decodedResponse['lyrics']) ? $decodedResponse['lyrics'] : null;

    $audioUrls = [];
    $addUrl = static function ($value) use (&$audioUrls): void {
        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed !== '' && str_starts_with($trimmed, 'http')) {
                $audioUrls[$trimmed] = true;
            }
        }
    };

    $addUrl($decodedResponse['preview_url'] ?? null);
    $addUrl($decodedResponse['audio_url'] ?? null);
    $addUrl($decodedResponse['audio_url_mp3'] ?? null);
    $addUrl($decodedResponse['audio_url_hq'] ?? null);
    $addUrl($decodedResponse['audio_url_320'] ?? null);
    $addUrl($decodedResponse['audio_url_128'] ?? null);

    if (isset($decodedResponse['audio_urls']) && is_array($decodedResponse['audio_urls'])) {
        foreach ($decodedResponse['audio_urls'] as $candidate) {
            $addUrl($candidate);
        }
    }

    if (isset($decodedResponse['clips']) && is_array($decodedResponse['clips'])) {
        foreach ($decodedResponse['clips'] as $clip) {
            if (is_array($clip)) {
                $addUrl($clip['audio_url'] ?? null);
                $addUrl($clip['preview_url'] ?? null);
            }
        }
    }

    if (isset($decodedResponse['data']) && is_array($decodedResponse['data'])) {
        $nested = $decodedResponse['data'];
        $addUrl($nested['preview_url'] ?? null);
        $addUrl($nested['audio_url'] ?? null);
        if (isset($nested['audio_urls']) && is_array($nested['audio_urls'])) {
            foreach ($nested['audio_urls'] as $candidate) {
                $addUrl($candidate);
            }
        }
    }

    $audioUrlList = array_keys($audioUrls);
    $previewUrl = $audioUrlList !== [] ? $audioUrlList[0] : null;

    echo json_encode(
        [
            'ok' => true,
            'provider' => 'sunoapi.com',
            'requestId' => $requestId,
            'jobId' => $requestId,
            'status' => $status,
            'message' => $message,
            'previewUrl' => $previewUrl,
            'audioUrls' => $audioUrlList,
            'lyrics' => $lyricsResponse,
            'raw' => $decodedResponse,
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

http_response_code(502);
$errorMessage = is_string($lastError) ? $lastError : 'Upstream error occurred.';
$details = null;
if (is_string($lastBody)) {
    $decodedBody = json_decode($lastBody, true);
    $details = is_array($decodedBody) ? $decodedBody : $lastBody;
}

echo json_encode(
    [
        'ok' => false,
        'status' => 502,
        'error' => $errorMessage,
        'details' => $details,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
