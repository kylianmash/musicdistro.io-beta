<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if (!in_array($method, ['POST', 'GET'], true)) {
    http_response_code(405);
    header('Allow: GET, POST');
    exit;
}

$languageParam = $_POST['language'] ?? $_GET['language'] ?? '';
$language = normalize_language(is_string($languageParam) ? $languageParam : '');

set_current_language($language);

$user = current_user();

if ($user) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE users SET language = :language WHERE id = :id');
    $stmt->execute([
        ':language' => $language,
        ':id' => $user['id'],
    ]);
}

$successMessage = (string) __('language.updated', ['language' => available_languages()[$language]['native'] ?? $language]);

if (is_json_request()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'language' => $language,
        'message' => $successMessage,
    ], JSON_THROW_ON_ERROR);
    exit;
}

flash('success', $successMessage);

$redirectSource = $_POST['redirect'] ?? $_GET['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
if (!is_string($redirectSource) || trim($redirectSource) === '') {
    $redirectSource = '/';
}

$parsedRedirect = parse_url($redirectSource);
$redirectPath = is_array($parsedRedirect) && isset($parsedRedirect['path']) && is_string($parsedRedirect['path'])
    ? $parsedRedirect['path']
    : '/';
$redirectPath = $redirectPath === '' ? '/' : $redirectPath;

$redirectQuery = is_array($parsedRedirect) && isset($parsedRedirect['query']) && is_string($parsedRedirect['query'])
    ? $parsedRedirect['query']
    : '';
$redirectFragment = is_array($parsedRedirect) && isset($parsedRedirect['fragment']) && is_string($parsedRedirect['fragment'])
    ? $parsedRedirect['fragment']
    : '';

$languagePath = '/' . $language . '/';
$languageKeys = array_keys(available_languages());
$languagePattern = '/^\/(?:' . implode('|', array_map(static function (string $code): string {
    return preg_quote($code, '/');
}, $languageKeys)) . ')?\/?$/';

if ($redirectPath === '/' || preg_match($languagePattern, $redirectPath) === 1) {
    $redirectPath = $languagePath;
    $redirectQuery = '';
    $redirectFragment = '';
} elseif ($redirectPath[0] !== '/') {
    $redirectPath = '/' . ltrim($redirectPath, '/');
}

$redirectTo = $redirectPath;
if ($redirectQuery !== '') {
    $redirectTo .= '?' . $redirectQuery;
}
if ($redirectFragment !== '') {
    $redirectTo .= '#' . $redirectFragment;
}

header('Location: ' . $redirectTo);
exit;
