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

$variant = isset($input['variant']) ? strtolower(trim((string) $input['variant'])) : '';
$allowedVariants = ['classic', 'vision', 'focus', 'aura'];

if ($variant === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.design.validation.variant'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

if (!in_array($variant, $allowedVariants, true)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.design.validation.variant'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

try {
    set_setting('dashboard_design_variant', $variant);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.design.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.design.feedback.saved'),
    'variant' => $variant,
], JSON_THROW_ON_ERROR);
