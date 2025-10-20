<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

// Ensure the generate-token endpoint is accessed through its canonical path so that
// locale-prefixed rewrites (for example "/fr/generate-token") are redirected back
// to the PHP script that actually generates the JWT for SonoSuite.
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if ($requestUri !== '') {
    $parsedUri = parse_url($requestUri);
    $incomingPath = $parsedUri['path'] ?? '';
    $allowedPaths = ['/generate-token', '/generate-token/', '/generate-token/index.php'];

    if ($incomingPath !== '' && !in_array($incomingPath, $allowedPaths, true)) {
        $querySuffix = isset($parsedUri['query']) && $parsedUri['query'] !== ''
            ? '?' . $parsedUri['query']
            : '';

        header('Location: /generate-token/' . $querySuffix, true, 302);
        exit;
    }
}

require_authentication();

$user = current_user();

if (!$user) {
    header('Location: /login.php');
    exit;
}

$returnTo = sanitize_sonosuite_return_to(isset($_GET['return_to']) && is_string($_GET['return_to']) ? $_GET['return_to'] : null);

$provider = distribution_dashboard_provider();

if ($provider !== 'sonosuite') {
    http_response_code(503);
    exit('Distribution provider not supported.');
}

$secretKey = sonosuite_shared_secret();

$payload = [
    'exp' => time() + (5 * 60),
    'iat' => time(),
    'jti' => bin2hex(random_bytes(16)),
    'email' => $user['email'],
    'externalId' => (string) $user['id'],
];

$token = (function (array $payload, string $secret): string {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];

    $segments = [];
    $segments[] = rtrim(strtr(base64_encode(json_encode($header, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
    $segments[] = rtrim(strtr(base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');

    $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
    $segments[] = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    return implode('.', $segments);
})($payload, $secretKey);

$baseUrl = sonosuite_base_url();

if ($baseUrl === '') {
    http_response_code(503);
    exit('Distribution platform URL not configured.');
}

$sonosuiteEndpoint = rtrim($baseUrl, '/') . '/albums';

$redirectUrl = $sonosuiteEndpoint . '?jwt=' . urlencode($token);

if ($returnTo !== null) {
    $redirectUrl .= '&return_to=' . urlencode($returnTo);
}

header('Location: ' . $redirectUrl);
exit;
