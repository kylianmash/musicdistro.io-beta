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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($userId <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.user_not_found'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$statement = $pdo->prepare('SELECT id, email, first_name, last_name, last_login_ip, last_login_at FROM users WHERE id = :id');
$statement->execute([':id' => $userId]);
$target = $statement->fetch();

if (!$target) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.user_not_found'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$displayName = trim((string) ($target['first_name'] ?? ''));
$lastName = trim((string) ($target['last_name'] ?? ''));
if ($lastName !== '') {
    $displayName = trim($displayName . ' ' . $lastName);
}
if ($displayName === '') {
    $displayName = (string) ($target['email'] ?? '');
}

$lastLoginAtRaw = (string) ($target['last_login_at'] ?? '');
$lastLoginAtFormatted = $lastLoginAtRaw;
if ($lastLoginAtRaw !== '') {
    try {
        $lastLoginAtFormatted = (new DateTimeImmutable($lastLoginAtRaw))->format('d/m/Y H:i');
    } catch (Exception $exception) {
        $lastLoginAtFormatted = $lastLoginAtRaw;
    }
}

$eventsStatement = $pdo->prepare('SELECT id, ip_address, user_agent, device_type, os_name, browser_name, created_at FROM user_login_events WHERE user_id = :user_id ORDER BY datetime(created_at) DESC LIMIT 50');
$eventsStatement->execute([':user_id' => $userId]);

$events = [];

while ($row = $eventsStatement->fetch()) {
    $createdAtRaw = (string) ($row['created_at'] ?? '');
    $createdAtFormatted = $createdAtRaw;
    if ($createdAtRaw !== '') {
        try {
            $createdAtFormatted = (new DateTimeImmutable($createdAtRaw))->format('d/m/Y H:i');
        } catch (Exception $exception) {
            $createdAtFormatted = $createdAtRaw;
        }
    }

    $events[] = [
        'id' => (int) ($row['id'] ?? 0),
        'ip_address' => $row['ip_address'] ?? '',
        'user_agent' => $row['user_agent'] ?? '',
        'device_type' => $row['device_type'] ?? '',
        'os_name' => $row['os_name'] ?? '',
        'browser_name' => $row['browser_name'] ?? '',
        'created_at' => $createdAtRaw,
        'created_at_formatted' => $createdAtFormatted,
        'is_current' => $lastLoginAtRaw !== '' && $createdAtRaw !== '' && $createdAtRaw === $lastLoginAtRaw,
    ];
}

echo json_encode([
    'status' => 'success',
    'user' => [
        'id' => (int) $target['id'],
        'email' => (string) ($target['email'] ?? ''),
        'display_name' => $displayName,
        'last_login_ip' => (string) ($target['last_login_ip'] ?? ''),
        'last_login_at' => $lastLoginAtRaw,
        'last_login_at_formatted' => $lastLoginAtFormatted,
    ],
    'events' => $events,
], JSON_THROW_ON_ERROR);
