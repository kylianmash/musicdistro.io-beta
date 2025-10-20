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

$enabledInput = isset($input['notifications_enabled']) ? trim((string) $input['notifications_enabled']) : '1';
$profileReminderInput = isset($input['automatic_profile_completion']) ? trim((string) $input['automatic_profile_completion']) : '1';

$notificationsEnabled = $enabledInput === '1';
$profileReminderEnabled = $notificationsEnabled && $profileReminderInput === '1';

try {
    set_setting('dashboard_notifications_enabled', $notificationsEnabled ? '1' : '0');
    set_setting('dashboard_notifications_auto_profile_completion', $profileReminderEnabled ? '1' : '0');
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.notifications.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.notifications.feedback.saved'),
], JSON_THROW_ON_ERROR);
