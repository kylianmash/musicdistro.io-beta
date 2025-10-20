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

$translationsInput = $input['translations'] ?? [];
if (!is_array($translationsInput)) {
    $translationsInput = [];
}

$linkUrlInput = isset($input['link_url']) ? trim((string) $input['link_url']) : '';

$availableLanguages = available_languages();
$normalizedTranslations = [];
$hasCompleteTranslation = false;

foreach ($translationsInput as $locale => $values) {
    if (!is_string($locale) || $locale === '') {
        continue;
    }

    $normalizedLocale = normalize_language($locale);
    if (!array_key_exists($normalizedLocale, $availableLanguages)) {
        continue;
    }

    if (!is_array($values)) {
        continue;
    }

    $title = trim((string) ($values['title'] ?? ''));
    $message = trim((string) ($values['message'] ?? ''));
    $actionLabel = trim((string) ($values['action_label'] ?? ''));

    if ($title === '' && $message === '' && $actionLabel === '') {
        continue;
    }

    if ($title !== '' && $message !== '') {
        $hasCompleteTranslation = true;
    }

    $normalizedTranslations[$normalizedLocale] = [
        'title' => $title,
        'message' => $message,
    ];

    if ($actionLabel !== '') {
        $normalizedTranslations[$normalizedLocale]['action_label'] = $actionLabel;
    }
}

if (!$hasCompleteTranslation) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.notifications.broadcast.feedback.missing'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$linkUrl = '';

if ($linkUrlInput !== '') {
    $validated = filter_var($linkUrlInput, FILTER_VALIDATE_URL);
    $scheme = strtolower((string) parse_url($linkUrlInput, PHP_URL_SCHEME));

    if ($validated === false || !in_array($scheme, ['http', 'https'], true) || mb_strlen($linkUrlInput) > 2048) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.notifications.broadcast.feedback.invalid_link'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    $linkUrl = $linkUrlInput;
}

try {
    $payload = json_encode($normalizedTranslations, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (JsonException $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.notifications.broadcast.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$createdAt = (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339);
$createdBy = isset($user['id']) ? (int) $user['id'] : null;

try {
    global $pdo;
    $statement = $pdo->prepare('INSERT INTO broadcast_notifications (translations, link_url, created_at, created_by) VALUES (:translations, :link_url, :created_at, :created_by)');
    $statement->execute([
        ':translations' => $payload,
        ':link_url' => $linkUrl !== '' ? $linkUrl : null,
        ':created_at' => $createdAt,
        ':created_by' => $createdBy,
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.notifications.broadcast.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.notifications.broadcast.feedback.success'),
], JSON_THROW_ON_ERROR);
