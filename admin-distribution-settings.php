<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_authentication();

header('Content-Type: application/json; charset=utf-8');

$user = current_user();

if (!$user || !isset($user['is_super_admin']) || (int) $user['is_super_admin'] !== 1) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.auth_required'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = [];

if (is_string($rawInput) && $rawInput !== '') {
    try {
        $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($decoded)) {
            $input = $decoded;
        }
    } catch (JsonException $exception) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.json_invalid'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if (!$input) {
    $input = $_POST;
}

$provider = isset($input['distribution_provider']) ? strtolower(trim((string) $input['distribution_provider'])) : '';

if ($provider === '') {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.distribution_provider_invalid'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$allowedProviders = ['sonosuite'];

if (!in_array($provider, $allowedProviders, true)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.distribution_provider_invalid'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$sonosuiteBaseUrl = '';
$sonosuiteSharedSecret = '';

if ($provider === 'sonosuite') {
    $sonosuiteBaseUrl = isset($input['sonosuite_base_url']) ? trim((string) $input['sonosuite_base_url']) : '';
    $sonosuiteSharedSecret = isset($input['sonosuite_shared_secret']) ? trim((string) $input['sonosuite_shared_secret']) : '';

    if ($sonosuiteBaseUrl === '' || !filter_var($sonosuiteBaseUrl, FILTER_VALIDATE_URL) || stripos($sonosuiteBaseUrl, 'https://') !== 0) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.sonosuite_base_url_invalid'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    $sonosuiteBaseUrl = rtrim($sonosuiteBaseUrl, '/');

    if ($sonosuiteBaseUrl === '') {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.sonosuite_base_url_invalid'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    if ($sonosuiteSharedSecret === '') {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.sonosuite_shared_secret_required'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

try {
    set_setting('distribution_dashboard_provider', $provider);
    set_setting('sonosuite_base_url', $provider === 'sonosuite' ? $sonosuiteBaseUrl : null);
    set_setting('sonosuite_shared_secret', $provider === 'sonosuite' ? $sonosuiteSharedSecret : null);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.distribution.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.distribution.feedback.saved'),
], JSON_THROW_ON_ERROR);
