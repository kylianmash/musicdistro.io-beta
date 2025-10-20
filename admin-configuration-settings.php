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

$studioValue = isset($input['studio_card']) ? (string) $input['studio_card'] : '1';
$studioCardEnabled = !in_array(strtolower($studioValue), ['0', 'false', 'off', 'no'], true);

$cardsInput = [];
if (isset($input['cards']) && is_array($input['cards'])) {
    $cardsInput = $input['cards'];
}

$languagesInput = [];
if (isset($input['languages']) && is_array($input['languages'])) {
    $languagesInput = $input['languages'];
}

$languagesToggleValue = isset($input['languages_multilingual']) ? (string) $input['languages_multilingual'] : '1';
$languagesMultilingualEnabled = !in_array(strtolower($languagesToggleValue), ['0', 'false', 'off', 'no'], true);
$autoDetectValue = isset($input['languages_auto_detect']) ? (string) $input['languages_auto_detect'] : '1';
$languagesAutoDetectEnabled = !in_array(strtolower($autoDetectValue), ['0', 'false', 'off', 'no'], true);
$defaultLanguageInput = isset($input['languages_default']) ? strtolower(trim((string) $input['languages_default'])) : '';

$dashboardTranslations = __d('dashboard');
$cardsTranslations = $dashboardTranslations['cards'] ?? [];
$cardSettings = [];

try {
    $enabledLanguagesLookup = [];
    $allLanguages = all_languages();
    $defaultLanguage = get_setting('languages_default');
    if (
        is_string($defaultLanguageInput)
        && $defaultLanguageInput !== ''
        && array_key_exists($defaultLanguageInput, $allLanguages)
    ) {
        $defaultLanguage = $defaultLanguageInput;
    }

    if (!is_string($defaultLanguage) || $defaultLanguage === '' || !array_key_exists(strtolower($defaultLanguage), $allLanguages)) {
        $defaultLanguage = array_key_first($allLanguages) ?? 'en';
    }
    $defaultLanguage = strtolower((string) $defaultLanguage);
    if (!array_key_exists($defaultLanguage, $allLanguages)) {
        $defaultLanguage = 'en';
    }
    if (!array_key_exists($defaultLanguage, $allLanguages)) {
        $defaultLanguage = array_key_first($allLanguages) ?: 'en';
    }

    foreach ($allLanguages as $code => $_meta) {
        $rawValue = $languagesInput[$code] ?? '1';
        if (is_bool($rawValue)) {
            $enabled = $rawValue;
        } else {
            $stringValue = strtolower(trim((string) $rawValue));
            $enabled = !in_array($stringValue, ['0', 'false', 'off', 'no'], true);
        }

        if ($code === $defaultLanguage) {
            $enabled = true;
        }

        if ($enabled) {
            $enabledLanguagesLookup[$code] = true;
        }
    }

    if (!$enabledLanguagesLookup) {
        $enabledLanguagesLookup[$defaultLanguage] = true;
    }

    $enabledLanguagesOrdered = [];
    foreach ($allLanguages as $code => $_meta) {
        if (isset($enabledLanguagesLookup[$code])) {
            $enabledLanguagesOrdered[] = $code;
        }
    }

    if ($languagesMultilingualEnabled && $enabledLanguagesOrdered === []) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $dashboardTranslations['admin']['configuration']['languages']['minimum_error']
                ?? __('dashboard.admin.configuration.feedback.error'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    $languagesEnabledValue = json_encode($enabledLanguagesOrdered, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($languagesEnabledValue === false) {
        throw new RuntimeException('Unable to encode languages setting.');
    }

    $languageSettings = [
        'multilingual' => $languagesMultilingualEnabled ? '1' : '0',
        'auto_detect' => $languagesAutoDetectEnabled ? '1' : '0',
        'default' => $defaultLanguage,
        'enabled' => $languagesEnabledValue,
    ];
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.configuration.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

foreach ($cardsTranslations as $cardKey => $_cardTranslation) {
    $value = $cardsInput[$cardKey] ?? '1';
    if (is_array($value) || is_object($value)) {
        $value = '1';
    }

    $stringValue = strtolower(trim((string) $value));
    $cardSettings[$cardKey] = !in_array($stringValue, ['0', 'false', 'off', 'no'], true);
}

try {
    set_setting('dashboard_studio_card_enabled', $studioCardEnabled ? '1' : '0');
    foreach ($cardSettings as $cardKey => $enabled) {
        set_setting('dashboard_card_' . $cardKey . '_enabled', $enabled ? '1' : '0');
    }
    set_setting('languages_multilingual_enabled', $languageSettings['multilingual']);
    set_setting('languages_auto_detect_enabled', $languageSettings['auto_detect']);
    set_setting('languages_default', $languageSettings['default']);
    set_setting('languages_enabled_list', $languageSettings['enabled']);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.configuration.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.configuration.feedback.saved'),
], JSON_THROW_ON_ERROR);
