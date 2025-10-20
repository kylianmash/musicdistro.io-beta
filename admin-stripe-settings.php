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
        'message' => __('validation.auth_required')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed')
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
            'message' => __('validation.json_invalid')
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if (!$input) {
    $input = $_POST;
}

$secretKey = isset($input['stripe_secret_key']) ? trim((string) $input['stripe_secret_key']) : '';
$publishableKey = isset($input['stripe_publishable_key']) ? trim((string) $input['stripe_publishable_key']) : '';

$parsePriceToCents = static function ($value): ?int {
    if ($value === null) {
        return null;
    }

    if (is_int($value)) {
        return $value >= 0 ? $value : null;
    }

    if (is_float($value)) {
        return $value >= 0 ? (int) round($value) : null;
    }

    $normalized = preg_replace('/[^\d.,-]/', '', (string) $value);
    if ($normalized === null) {
        return null;
    }

    $normalized = str_replace(',', '.', $normalized);
    $normalized = trim($normalized);

    if ($normalized === '') {
        return null;
    }

    if (!is_numeric($normalized)) {
        return null;
    }

    $amount = (float) $normalized;

    if ($amount < 0) {
        return null;
    }

    return (int) round($amount * 100);
};

$parsePositiveInt = static function ($value): ?int {
    if ($value === null) {
        return null;
    }

    if (is_int($value)) {
        return $value > 0 ? $value : null;
    }

    if (is_float($value)) {
        $rounded = (int) round($value);
        return $rounded > 0 ? $rounded : null;
    }

    $normalized = preg_replace('/[^\d]/', '', (string) $value);
    if ($normalized === null) {
        return null;
    }

    $normalized = ltrim($normalized);

    if ($normalized === '') {
        return null;
    }

    $intValue = (int) $normalized;

    return $intValue > 0 ? $intValue : null;
};

$hasPaymentsToggle = array_key_exists('mastering_payments_enabled', $input);
$paymentsToggle = $hasPaymentsToggle ? (string) $input['mastering_payments_enabled'] : '';
$masteringPaymentsEnabled = $paymentsToggle === ''
    ? null
    : (!in_array(strtolower($paymentsToggle), ['0', 'false', 'off', 'no'], true));

$hasSinglePrice = array_key_exists('mastering_price_single', $input);
$hasYearlyPrice = array_key_exists('mastering_price_yearly', $input);

$singlePriceCents = $hasSinglePrice ? $parsePriceToCents($input['mastering_price_single']) : null;
$yearlyPriceCents = $hasYearlyPrice ? $parsePriceToCents($input['mastering_price_yearly']) : null;
$hasExpressSinglePrice = array_key_exists('express_delivery_price_single', $input);
$hasExpressEpPrice = array_key_exists('express_delivery_price_ep', $input);
$hasExpressAlbumPrice = array_key_exists('express_delivery_price_album', $input);
$expressSinglePriceCents = $hasExpressSinglePrice ? $parsePriceToCents($input['express_delivery_price_single']) : null;
$expressEpPriceCents = $hasExpressEpPrice ? $parsePriceToCents($input['express_delivery_price_ep']) : null;
$expressAlbumPriceCents = $hasExpressAlbumPrice ? $parsePriceToCents($input['express_delivery_price_album']) : null;
$hasPublishingPrice = array_key_exists('publishing_setup_price', $input);
$publishingSetupPriceCents = $hasPublishingPrice ? $parsePriceToCents($input['publishing_setup_price']) : null;

$hasCloudUsageToggle = array_key_exists('cloud_storage_usage_enabled', $input);
$cloudUsageToggle = $hasCloudUsageToggle ? (string) $input['cloud_storage_usage_enabled'] : '';
$cloudUsageEnabled = $hasCloudUsageToggle
    ? (!in_array(strtolower($cloudUsageToggle), ['0', 'false', 'off', 'no'], true))
    : null;

$hasCloudPricePerMb = array_key_exists('cloud_storage_price_per_mb', $input);
$cloudPricePerMbCents = $hasCloudPricePerMb ? $parsePriceToCents($input['cloud_storage_price_per_mb']) : null;

$hasCloudPricePerFile = array_key_exists('cloud_storage_price_per_file', $input);
$cloudPricePerFileCents = $hasCloudPricePerFile ? $parsePriceToCents($input['cloud_storage_price_per_file']) : null;

$hasCloudSubscriptionToggle = array_key_exists('cloud_storage_subscription_enabled', $input);
$cloudSubscriptionToggle = $hasCloudSubscriptionToggle ? (string) $input['cloud_storage_subscription_enabled'] : '';
$cloudSubscriptionEnabled = $hasCloudSubscriptionToggle
    ? (!in_array(strtolower($cloudSubscriptionToggle), ['0', 'false', 'off', 'no'], true))
    : null;

$hasCloudSubscriptionPrice = array_key_exists('cloud_storage_subscription_price', $input);
$cloudSubscriptionPriceCents = $hasCloudSubscriptionPrice ? $parsePriceToCents($input['cloud_storage_subscription_price']) : null;

$hasCloudSubscriptionStorage = array_key_exists('cloud_storage_subscription_storage_mb', $input);
$cloudSubscriptionStorageMb = $hasCloudSubscriptionStorage ? $parsePositiveInt($input['cloud_storage_subscription_storage_mb']) : null;

$supportedCurrencies = stripe_supported_currencies();
$supportedCurrencySet = array_fill_keys($supportedCurrencies, true);
$currencyDefaultInput = isset($input['currency_default']) ? normalize_currency((string) $input['currency_default']) : '';
if ($currencyDefaultInput === '' || !isset($supportedCurrencySet[$currencyDefaultInput])) {
    $currencyDefaultInput = default_currency();
}

$currencyAllowChoiceFlag = array_key_exists('currency_allow_user_choice', $input)
    ? (string) $input['currency_allow_user_choice']
    : '';
$currencyAllowChoice = $currencyAllowChoiceFlag === ''
    ? null
    : (!in_array(strtolower($currencyAllowChoiceFlag), ['0', 'false', 'off', 'no'], true));

$currencyEnabledRaw = $input['currency_enabled'] ?? [];
if (is_string($currencyEnabledRaw)) {
    $currencyEnabledRaw = explode(',', $currencyEnabledRaw);
}

if (!is_array($currencyEnabledRaw)) {
    $currencyEnabledRaw = [];
}

$currencyEnabledSet = [];
foreach ($currencyEnabledRaw as $code) {
    $normalized = normalize_currency((string) $code);
    if ($normalized === '' || !isset($supportedCurrencySet[$normalized])) {
        continue;
    }
    $currencyEnabledSet[$normalized] = true;
}

$currencyEnabledSet[$currencyDefaultInput] = true;
$currencyEnabledList = array_keys($currencyEnabledSet);

if ($masteringPaymentsEnabled === true) {
    if ($singlePriceCents === null || $singlePriceCents <= 0 || $yearlyPriceCents === null || $yearlyPriceCents <= 0) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.monetization.invalid_price')
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if ($hasPublishingPrice && ($publishingSetupPriceCents === null || $publishingSetupPriceCents <= 0)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.monetization.invalid_publishing_price')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if (
    ($hasExpressSinglePrice && ($expressSinglePriceCents === null || $expressSinglePriceCents <= 0))
    || ($hasExpressEpPrice && ($expressEpPriceCents === null || $expressEpPriceCents <= 0))
    || ($hasExpressAlbumPrice && ($expressAlbumPriceCents === null || $expressAlbumPriceCents <= 0))
) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.monetization.invalid_express_price')
    ], JSON_THROW_ON_ERROR);
    exit;
}

if ($cloudUsageEnabled === true) {
    if ($cloudPricePerMbCents === null || $cloudPricePerMbCents <= 0 || $cloudPricePerFileCents === null || $cloudPricePerFileCents <= 0) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.cloud_storage.validation_usage')
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if ($cloudSubscriptionEnabled === true) {
    if ($cloudSubscriptionPriceCents === null || $cloudSubscriptionPriceCents <= 0 || $cloudSubscriptionStorageMb === null || $cloudSubscriptionStorageMb <= 0) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => __('dashboard.admin.cloud_storage.validation_subscription')
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

try {
    set_setting('stripe_secret_key', $secretKey);
    set_setting('stripe_publishable_key', $publishableKey);
    if ($masteringPaymentsEnabled !== null) {
        set_setting('mastering_payments_enabled', $masteringPaymentsEnabled ? '1' : '0');
    }
    if ($hasSinglePrice) {
        set_setting('mastering_price_single', ($singlePriceCents !== null && $singlePriceCents > 0) ? (string) $singlePriceCents : null);
    }
    if ($hasYearlyPrice) {
        set_setting('mastering_price_yearly', ($yearlyPriceCents !== null && $yearlyPriceCents > 0) ? (string) $yearlyPriceCents : null);
    }
    if ($hasExpressSinglePrice) {
        set_setting('express_delivery_price_single', ($expressSinglePriceCents !== null && $expressSinglePriceCents > 0) ? (string) $expressSinglePriceCents : null);
    }
    if ($hasExpressEpPrice) {
        set_setting('express_delivery_price_ep', ($expressEpPriceCents !== null && $expressEpPriceCents > 0) ? (string) $expressEpPriceCents : null);
    }
    if ($hasExpressAlbumPrice) {
        set_setting('express_delivery_price_album', ($expressAlbumPriceCents !== null && $expressAlbumPriceCents > 0) ? (string) $expressAlbumPriceCents : null);
    }
    if ($hasPublishingPrice) {
        set_setting('publishing_setup_price', ($publishingSetupPriceCents !== null && $publishingSetupPriceCents > 0) ? (string) $publishingSetupPriceCents : null);
    }
    if ($cloudUsageEnabled !== null) {
        set_setting('cloud_storage_usage_enabled', $cloudUsageEnabled ? '1' : '0');
    }
    if ($hasCloudPricePerMb) {
        set_setting('cloud_storage_price_per_mb', ($cloudPricePerMbCents !== null && $cloudPricePerMbCents > 0) ? (string) $cloudPricePerMbCents : null);
    }
    if ($hasCloudPricePerFile) {
        set_setting('cloud_storage_price_per_file', ($cloudPricePerFileCents !== null && $cloudPricePerFileCents > 0) ? (string) $cloudPricePerFileCents : null);
    }
    if ($cloudSubscriptionEnabled !== null) {
        set_setting('cloud_storage_subscription_enabled', $cloudSubscriptionEnabled ? '1' : '0');
    }
    if ($hasCloudSubscriptionPrice) {
        set_setting('cloud_storage_subscription_price', ($cloudSubscriptionPriceCents !== null && $cloudSubscriptionPriceCents > 0) ? (string) $cloudSubscriptionPriceCents : null);
    }
    if ($hasCloudSubscriptionStorage) {
        set_setting('cloud_storage_subscription_storage_mb', ($cloudSubscriptionStorageMb !== null && $cloudSubscriptionStorageMb > 0) ? (string) $cloudSubscriptionStorageMb : null);
    }
    set_setting('currency_default', $currencyDefaultInput);
    if ($currencyAllowChoice !== null) {
        set_setting('currency_allow_user_choice', $currencyAllowChoice ? '1' : '0');
    }
    set_setting('currency_enabled_list', json_encode($currencyEnabledList, JSON_THROW_ON_ERROR));
    currency_exchange_rates($currencyDefaultInput);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.stripe.feedback.error')
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => __('dashboard.admin.stripe.feedback.saved')
], JSON_THROW_ON_ERROR);
