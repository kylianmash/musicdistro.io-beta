<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_authentication();

header('Content-Type: application/json; charset=utf-8');

$user = current_user();
$impersonatorId = isset($_SESSION['impersonator_id']) ? (int) $_SESSION['impersonator_id'] : 0;
$impersonatorName = trim((string) ($_SESSION['impersonator_name'] ?? ''));
$impersonatorEmail = trim((string) ($_SESSION['impersonator_email'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$input = [];
$rawInput = file_get_contents('php://input');

if ($rawInput !== false && $rawInput !== '') {
    try {
        $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($decoded)) {
            $input = $decoded;
        }
    } catch (JsonException $exception) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.json_invalid')
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if (!$input) {
    $input = $_POST;
}

$action = isset($input['action']) ? trim((string) $input['action']) : '';
$userId = isset($input['user_id']) ? (int) $input['user_id'] : 0;

$isSuperAdmin = $user && isset($user['is_super_admin']) && (int) $user['is_super_admin'] === 1;

if ($action === '') {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.action_missing')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ($action === 'stop_impersonating') {
    if ($impersonatorId <= 0) {
        http_response_code(409);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.impersonation.not_active')
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    $impersonatorStatement = $pdo->prepare('SELECT id, is_super_admin, language FROM users WHERE id = :id');
    $impersonatorStatement->execute([':id' => $impersonatorId]);
    $impersonator = $impersonatorStatement->fetch();

    if (!$impersonator || (int) ($impersonator['is_super_admin'] ?? 0) !== 1) {
        unset($_SESSION['impersonator_id'], $_SESSION['impersonator_name'], $_SESSION['impersonator_email']);
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.impersonation.not_active')
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    $_SESSION['user_id'] = (int) $impersonator['id'];
    unset($_SESSION['impersonator_id'], $_SESSION['impersonator_name'], $_SESSION['impersonator_email']);

    if (!empty($impersonator['language'])) {
        set_current_language((string) $impersonator['language']);
    }

    session_regenerate_id(true);

    echo json_encode([
        'status' => 'success',
        'message' => __('dashboard.admin.impersonation.stopped'),
        'redirect' => '/dashboard.php'
    ], JSON_THROW_ON_ERROR);
    exit;
}

if (!$isSuperAdmin) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.auth_required')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ($userId <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.user_not_found')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$statement = $pdo->prepare('SELECT id, email, language, is_super_admin, is_blocked, is_verified FROM users WHERE id = :id');
$statement->execute([':id' => $userId]);
$target = $statement->fetch();

if (!$target) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.user_not_found')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ((int) $target['id'] === (int) $user['id']) {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.cannot_self_manage')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$isTargetSuperAdmin = isset($target['is_super_admin']) && (int) $target['is_super_admin'] === 1;

switch ($action) {
    case 'impersonate':
        if ($impersonatorId > 0) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => __('dashboard.admin.impersonation.already_active')
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        if (isset($target['is_blocked']) && (int) $target['is_blocked'] === 1) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => __('dashboard.admin.impersonation.blocked')
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        $_SESSION['impersonator_id'] = (int) $user['id'];
        $adminDisplayName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
        if ($adminDisplayName === '') {
            $adminDisplayName = (string) ($user['email'] ?? '');
        }
        $_SESSION['impersonator_name'] = $adminDisplayName;
        $_SESSION['impersonator_email'] = (string) ($user['email'] ?? '');

        $_SESSION['user_id'] = (int) $target['id'];
        if (!empty($target['language'])) {
            set_current_language((string) $target['language']);
        }
        session_regenerate_id(true);

        echo json_encode([
            'status' => 'success',
            'message' => __('dashboard.admin.impersonation.started'),
            'redirect' => '/dashboard.php'
        ], JSON_THROW_ON_ERROR);
        exit;

    case 'delete':
        if ($isTargetSuperAdmin) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => __('validation.cannot_manage_super_admin')
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        $delete = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $delete->execute([':id' => $target['id']]);

        echo json_encode([
            'status' => 'success',
            'message' => __('validation.account_deleted')
        ], JSON_THROW_ON_ERROR);
        exit;

    case 'block':
        if ($isTargetSuperAdmin) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => __('validation.cannot_manage_super_admin')
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        if (isset($target['is_blocked']) && (int) $target['is_blocked'] === 1) {
            echo json_encode([
                'status' => 'success',
                'message' => __('validation.account_already_blocked'),
                'user' => [
                    'id' => (int) $target['id'],
                    'is_blocked' => true,
                    'is_verified' => isset($target['is_verified']) && (int) $target['is_verified'] === 1,
                    'is_super_admin' => $isTargetSuperAdmin,
                ]
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        $update = $pdo->prepare('UPDATE users SET is_blocked = 1 WHERE id = :id');
        $update->execute([':id' => $target['id']]);

        echo json_encode([
            'status' => 'success',
            'message' => __('validation.account_blocked'),
            'user' => [
                'id' => (int) $target['id'],
                'is_blocked' => true,
                'is_verified' => isset($target['is_verified']) && (int) $target['is_verified'] === 1,
                'is_super_admin' => $isTargetSuperAdmin,
            ]
        ], JSON_THROW_ON_ERROR);
        exit;

    case 'unblock':
        if (isset($target['is_blocked']) && (int) $target['is_blocked'] === 0) {
            echo json_encode([
                'status' => 'success',
                'message' => __('validation.account_already_active'),
                'user' => [
                    'id' => (int) $target['id'],
                    'is_blocked' => false,
                    'is_verified' => isset($target['is_verified']) && (int) $target['is_verified'] === 1,
                    'is_super_admin' => $isTargetSuperAdmin,
                ]
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        $update = $pdo->prepare('UPDATE users SET is_blocked = 0 WHERE id = :id');
        $update->execute([':id' => $target['id']]);

        echo json_encode([
            'status' => 'success',
            'message' => __('validation.account_unblocked'),
            'user' => [
                'id' => (int) $target['id'],
                'is_blocked' => false,
                'is_verified' => isset($target['is_verified']) && (int) $target['is_verified'] === 1,
                'is_super_admin' => $isTargetSuperAdmin,
            ]
        ], JSON_THROW_ON_ERROR);
        exit;
}

http_response_code(400);

echo json_encode([
    'status' => 'error',
    'message' => __('validation.unknown_action')
], JSON_THROW_ON_ERROR);
