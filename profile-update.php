<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_authentication();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$user = current_user();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.auth_required')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$countries = require __DIR__ . '/data-countries.php';
$roles = __d('auth.roles') ?: [];

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$country = strtoupper(trim($_POST['country'] ?? ''));
$role = $_POST['role'] ?? '';
$removeAvatar = isset($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1';
$language = isset($_POST['language']) ? normalize_language($_POST['language']) : ($user['language'] ?? current_language());
$currencySettings = currency_settings();
$allowedCurrencies = array_fill_keys($currencySettings['enabled'] ?? [], true);
$defaultCurrencyCode = $currencySettings['default'] ?? 'eur';
$allowCurrencyChoice = $currencySettings['allow_user_choice'] ?? false;
$currency = isset($_POST['currency'])
    ? normalize_currency((string) $_POST['currency'])
    : normalize_currency((string) ($user['currency'] ?? $defaultCurrencyCode));
$currency = $currency ?: $defaultCurrencyCode;
$addressLine1 = trim($_POST['address_line1'] ?? '');
$addressLine2 = trim($_POST['address_line2'] ?? '');
$postalCode = trim($_POST['postal_code'] ?? '');
$city = trim($_POST['city'] ?? '');
$phoneNumber = trim($_POST['phone_number'] ?? '');
$businessType = $_POST['business_type'] ?? 'individual';
$companyName = trim($_POST['company_name'] ?? '');
$companyVat = trim($_POST['company_vat'] ?? '');

if (mb_strlen($addressLine1) > 180) {
    $addressLine1 = mb_substr($addressLine1, 0, 180);
}

if (mb_strlen($addressLine2) > 180) {
    $addressLine2 = mb_substr($addressLine2, 0, 180);
}

if (mb_strlen($postalCode) > 32) {
    $postalCode = mb_substr($postalCode, 0, 32);
}

if (mb_strlen($city) > 120) {
    $city = mb_substr($city, 0, 120);
}

if (mb_strlen($phoneNumber) > 64) {
    $phoneNumber = mb_substr($phoneNumber, 0, 64);
}

if (mb_strlen($companyName) > 180) {
    $companyName = mb_substr($companyName, 0, 180);
}

if (mb_strlen($companyVat) > 60) {
    $companyVat = mb_substr($companyVat, 0, 60);
}

$errors = [];

if ($firstName === '') {
    $errors['first_name'] = (string) __('validation.first_name_required');
}

if ($lastName === '') {
    $errors['last_name'] = (string) __('validation.last_name_required');
}

if (!isset($countries[$country])) {
    $errors['country'] = (string) __('validation.country_invalid');
}

if (!array_key_exists($role, $roles)) {
    $errors['role'] = (string) __('validation.role_required');
}

$allowedBusinessTypes = ['individual', 'company'];
if (!in_array($businessType, $allowedBusinessTypes, true)) {
    $errors['business_type'] = (string) __('validation.business_type_invalid');
}

if ($phoneNumber !== '' && !preg_match('/^[0-9+().\\s-]{6,}$/u', $phoneNumber)) {
    $errors['phone_number'] = (string) __('validation.phone_invalid');
}

if ($businessType === 'company' && $companyName === '') {
    $errors['company_name'] = (string) __('validation.company_name_required');
}

if ($businessType !== 'company') {
    $companyName = '';
    $companyVat = '';
}

if ($allowCurrencyChoice) {
    if ($currency === '' || !isset($allowedCurrencies[$currency])) {
        $errors['currency'] = (string) __('validation.currency_invalid');
    }
} else {
    $currency = $defaultCurrencyCode;
}

$avatarDirectory = base_path('storage/avatars');
if (!is_dir($avatarDirectory) && !mkdir($avatarDirectory, 0775, true) && !is_dir($avatarDirectory)) {
    $errors['avatar'] = (string) __('validation.avatar_storage_failed');
}

$avatarPath = $user['avatar_path'] ?? null;
$upload = $_FILES['avatar'] ?? null;

if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($upload['error'] !== UPLOAD_ERR_OK) {
        $errors['avatar'] = (string) __('validation.avatar_upload_failed');
    } elseif (($upload['size'] ?? 0) > 5 * 1024 * 1024) {
        $errors['avatar'] = (string) __('validation.avatar_size');
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($upload['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mimeType])) {
            $errors['avatar'] = (string) __('validation.avatar_format');
        } else {
            $filename = sprintf(
                'user-%d-%s.%s',
                (int) $user['id'],
                bin2hex(random_bytes(8)),
                $allowed[$mimeType]
            );
            $destination = $avatarDirectory . '/' . $filename;

            if (!move_uploaded_file($upload['tmp_name'], $destination)) {
                $errors['avatar'] = (string) __('validation.avatar_save_failed');
            } else {
                if ($avatarPath) {
                    $previous = base_path(ltrim($avatarPath, '/'));
                    if (is_file($previous)) {
                        @unlink($previous);
                    }
                }
                $avatarPath = '/storage/avatars/' . $filename;
            }
        }
    }
}

if ($removeAvatar) {
    if ($avatarPath) {
        $previous = base_path(ltrim($avatarPath, '/'));
        if (is_file($previous)) {
            @unlink($previous);
        }
    }
    $avatarPath = null;
}

if ($errors) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'errors' => $errors,
    ], JSON_THROW_ON_ERROR);
    exit;
}

$fields = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'country' => $country,
    'role' => $role,
    'language' => $language,
    'currency' => $currency,
    'address_line1' => $addressLine1,
    'address_line2' => $addressLine2,
    'postal_code' => $postalCode,
    'city' => $city,
    'phone_number' => $phoneNumber,
    'company_type' => $businessType,
    'company_name' => $companyName,
    'company_vat' => $companyVat,
];

if ($avatarPath !== ($user['avatar_path'] ?? null)) {
    $fields['avatar_path'] = $avatarPath;
}

$setParts = [];
$params = [];

foreach ($fields as $column => $value) {
    $setParts[] = sprintf('%s = :%s', $column, $column);
    $params[sprintf(':%s', $column)] = $value;
}

$params[':id'] = $user['id'];

$sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id';

$statement = $pdo->prepare($sql);
$statement->execute($params);

$user['first_name'] = $firstName;
$user['last_name'] = $lastName;
$user['country'] = $country;
$user['role'] = $role;
$user['avatar_path'] = $avatarPath;
$user['language'] = $language;
$user['currency'] = $currency;
$user['address_line1'] = $addressLine1;
$user['address_line2'] = $addressLine2;
$user['postal_code'] = $postalCode;
$user['city'] = $city;
$user['phone_number'] = $phoneNumber;
$user['company_type'] = $businessType;
$user['company_name'] = $companyName;
$user['company_vat'] = $companyVat;

if (!empty($language)) {
    set_current_language($language);
}

$avatarColors = [
    '#6366f1',
    '#8b5cf6',
    '#ec4899',
    '#14b8a6',
    '#f97316',
    '#22d3ee',
    '#facc15',
];

$colorIndex = count($avatarColors) > 0
    ? (int) (abs(crc32(($user['first_name'] ?? '') . ($user['last_name'] ?? ''))) % count($avatarColors))
    : 0;
$avatarColor = $avatarColors[$colorIndex] ?? '#6366f1';
$initial = strtoupper(mb_substr(trim((string) ($user['first_name'] ?? '')), 0, 1, 'UTF-8') ?: 'M');

$responseUser = [
    'id' => (int) $user['id'],
    'first_name' => $user['first_name'],
    'last_name' => $user['last_name'],
    'full_name' => trim($user['first_name'] . ' ' . $user['last_name']),
    'country' => $user['country'],
    'role' => $user['role'],
    'role_label' => $roles[$user['role']] ?? __('auth.roles.member'),
    'language' => $language,
    'language_label' => available_languages()[$language]['native'] ?? $language,
    'language_direction' => language_direction($language),
    'currency' => $currency,
    'currency_label' => strtoupper($currency) . ' (' . currency_symbol($currency) . ')',
    'avatar_path' => $avatarPath,
    'avatar_color' => $avatarColor,
    'initial' => $initial,
    'address_line1' => $addressLine1,
    'address_line2' => $addressLine2,
    'postal_code' => $postalCode,
    'city' => $city,
    'phone_number' => $phoneNumber,
    'company_type' => $businessType,
    'company_name' => $companyName,
    'company_vat' => $companyVat,
];

echo json_encode([
    'status' => 'success',
    'message' => __('auth.profile.updated'),
    'user' => $responseUser,
], JSON_THROW_ON_ERROR);
