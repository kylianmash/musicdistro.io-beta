<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_authentication();

$siteName = site_name();

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

$planKey = isset($input['plan']) ? strtolower(trim((string) $input['plan'])) : '';
$metadataInput = isset($input['metadata']) && is_array($input['metadata']) ? $input['metadata'] : [];
$sanitizeMetadataValue = static function ($value, int $limit = 120): string {
    $stringValue = trim((string) $value);
    if ($stringValue === '' || $limit <= 0) {
        return '';
    }

    if (function_exists('mb_substr')) {
        return mb_substr($stringValue, 0, $limit);
    }

    return substr($stringValue, 0, $limit);
};

$checkoutMetadata = [];

if (isset($metadataInput['release_title'])) {
    $releaseTitle = $sanitizeMetadataValue($metadataInput['release_title'], 160);
    if ($releaseTitle !== '') {
        $checkoutMetadata['release_title'] = $releaseTitle;
    }
}

if (isset($metadataInput['release_type'])) {
    $releaseType = strtolower($sanitizeMetadataValue($metadataInput['release_type'], 24));
    if (in_array($releaseType, ['single', 'ep', 'album'], true)) {
        $checkoutMetadata['release_type'] = $releaseType;
    }
}

if (isset($metadataInput['customer_first_name'])) {
    $firstName = $sanitizeMetadataValue($metadataInput['customer_first_name'], 80);
    if ($firstName !== '') {
        $checkoutMetadata['customer_first_name'] = $firstName;
    }
}

if (isset($metadataInput['customer_last_name'])) {
    $lastName = $sanitizeMetadataValue($metadataInput['customer_last_name'], 80);
    if ($lastName !== '') {
        $checkoutMetadata['customer_last_name'] = $lastName;
    }
}

if (isset($metadataInput['customer_email'])) {
    $emailValue = trim((string) $metadataInput['customer_email']);
    if ($emailValue !== '' && filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
        $checkoutMetadata['customer_email'] = $emailValue;
    }
}

$normalizePriceSetting = static function (?string $value, int $fallback): int {
    if ($value === null) {
        return $fallback;
    }

    $trimmed = trim($value);

    if ($trimmed === '' || !ctype_digit($trimmed)) {
        return $fallback;
    }

    $amount = (int) $trimmed;

    return $amount > 0 ? $amount : $fallback;
};

$masteringSinglePriceCents = $normalizePriceSetting(get_setting('mastering_price_single'), 900);
$masteringYearlyPriceCents = $normalizePriceSetting(get_setting('mastering_price_yearly'), 10800);
$publishingSetupPriceCents = $normalizePriceSetting(get_setting('publishing_setup_price'), 7500);
$expressSinglePriceCents = $normalizePriceSetting(get_setting('express_delivery_price_single'), 3900);
$expressEpPriceCents = $normalizePriceSetting(get_setting('express_delivery_price_ep'), 6900);
$expressAlbumPriceCents = $normalizePriceSetting(get_setting('express_delivery_price_album'), 9900);

$plans = [
    'monthly' => [
        'amount' => 999,
        'mode' => 'subscription',
        'interval' => 'month',
        'product_name' => sprintf('%s – %s', $siteName, (string) __('dashboard.royalties_modal.plans.monthly.label')),
        'product_description' => (string) __('dashboard.royalties_modal.subheadline'),
        'success_query' => ['checkout' => 'success', 'plan' => 'monthly'],
        'cancel_query' => ['checkout' => 'cancel'],
        'metadata' => ['category' => 'royalties'],
    ],
    'yearly' => [
        'amount' => 9900,
        'mode' => 'subscription',
        'interval' => 'year',
        'product_name' => sprintf('%s – %s', $siteName, (string) __('dashboard.royalties_modal.plans.yearly.label')),
        'product_description' => (string) __('dashboard.royalties_modal.subheadline'),
        'success_query' => ['checkout' => 'success', 'plan' => 'yearly'],
        'cancel_query' => ['checkout' => 'cancel'],
        'metadata' => ['category' => 'royalties'],
    ],
    'mastering_single' => [
        'amount' => $masteringSinglePriceCents,
        'mode' => 'payment',
        'product_name' => (string) __('dashboard.mastering_modal.checkout.single.product_name'),
        'product_description' => (string) __('dashboard.mastering_modal.checkout.single.product_description'),
        'success_query' => ['checkout' => 'success', 'mastering' => 'single'],
        'cancel_query' => ['checkout' => 'cancel', 'mastering' => 'single'],
        'metadata' => ['category' => 'mastering', 'type' => 'single'],
    ],
    'mastering_yearly' => [
        'amount' => $masteringYearlyPriceCents,
        'mode' => 'subscription',
        'interval' => 'year',
        'product_name' => (string) __('dashboard.mastering_modal.checkout.subscription.product_name'),
        'product_description' => (string) __('dashboard.mastering_modal.checkout.subscription.product_description'),
        'success_query' => ['checkout' => 'success', 'mastering' => 'yearly'],
        'cancel_query' => ['checkout' => 'cancel', 'mastering' => 'yearly'],
        'metadata' => ['category' => 'mastering', 'type' => 'yearly'],
    ],
    'publishing_administration' => [
        'amount' => $publishingSetupPriceCents,
        'mode' => 'payment',
        'product_name' => sprintf('%s – %s', $siteName, (string) __('dashboard.publishing_modal.title')),
        'product_description' => (string) __('dashboard.publishing_modal.subtitle'),
        'success_query' => ['checkout' => 'success', 'publishing' => 'setup'],
        'cancel_query' => ['checkout' => 'cancel', 'publishing' => 'setup'],
        'metadata' => ['category' => 'publishing'],
    ],
    'express_single' => [
        'amount' => $expressSinglePriceCents,
        'mode' => 'payment',
        'product_name' => (string) __('dashboard.express_modal.checkout.single.product_name'),
        'product_description' => (string) __('dashboard.express_modal.checkout.single.product_description'),
        'success_query' => ['checkout' => 'success', 'express' => 'single'],
        'cancel_query' => ['checkout' => 'cancel', 'express' => 'single'],
        'metadata' => ['category' => 'express_delivery', 'type' => 'single'],
    ],
    'express_ep' => [
        'amount' => $expressEpPriceCents,
        'mode' => 'payment',
        'product_name' => (string) __('dashboard.express_modal.checkout.ep.product_name'),
        'product_description' => (string) __('dashboard.express_modal.checkout.ep.product_description'),
        'success_query' => ['checkout' => 'success', 'express' => 'ep'],
        'cancel_query' => ['checkout' => 'cancel', 'express' => 'ep'],
        'metadata' => ['category' => 'express_delivery', 'type' => 'ep'],
    ],
    'express_album' => [
        'amount' => $expressAlbumPriceCents,
        'mode' => 'payment',
        'product_name' => (string) __('dashboard.express_modal.checkout.album.product_name'),
        'product_description' => (string) __('dashboard.express_modal.checkout.album.product_description'),
        'success_query' => ['checkout' => 'success', 'express' => 'album'],
        'cancel_query' => ['checkout' => 'cancel', 'express' => 'album'],
        'metadata' => ['category' => 'express_delivery', 'type' => 'album'],
    ],
];

if (!array_key_exists($planKey, $plans)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.action_missing')
    ], JSON_THROW_ON_ERROR);
    exit;
}

$currencySettings = currency_settings();
$defaultCurrencyCode = $currencySettings['default'] ?? 'eur';
$userCurrencyCode = user_currency($user);
$currencyRates = $userCurrencyCode === $defaultCurrencyCode ? [] : currency_exchange_rates($defaultCurrencyCode);
$userCurrencyRate = $currencyRates[$userCurrencyCode] ?? null;
$canConvertCurrency = $userCurrencyCode !== $defaultCurrencyCode
    && is_numeric($userCurrencyRate)
    && (float) $userCurrencyRate > 0;

if (!$canConvertCurrency && $userCurrencyCode !== $defaultCurrencyCode) {
    $sampleAmountCents = 10000;
    $sampleConversion = convert_currency_amount($sampleAmountCents, $defaultCurrencyCode, $userCurrencyCode);
    $canConvertCurrency = $sampleConversion > 0 && $sampleConversion !== $sampleAmountCents;
}

$stripeSecretKey = trim((string) (get_setting('stripe_secret_key') ?? ''));

if ($stripeSecretKey === '') {
    $missingKeyMessage = in_array($planKey, ['mastering_single', 'mastering_yearly'], true)
        ? __('dashboard.mastering_modal.checkout.missing_key')
        : __('dashboard.royalties_modal.checkout.missing_key');
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'message' => $missingKeyMessage
    ], JSON_THROW_ON_ERROR);
    exit;
}

$planConfig = $plans[$planKey];
$mode = $planConfig['mode'] ?? 'subscription';
$productName = $planConfig['product_name'] ?? '';
if ($productName === '') {
    $productName = sprintf('%s – %s', $siteName, ucfirst($planKey));
}
$productDescription = $planConfig['product_description'] ?? '';
$successQuery = $planConfig['success_query'] ?? ['checkout' => 'success'];
$cancelQuery = $planConfig['cancel_query'] ?? ['checkout' => 'cancel'];
$successUrl = app_url('dashboard.php?' . http_build_query($successQuery));
$cancelUrl = app_url('dashboard.php?' . http_build_query($cancelQuery));
$genericErrorMessage = in_array($planKey, ['mastering_single', 'mastering_yearly'], true)
    ? __('dashboard.mastering_modal.checkout.generic_error')
    : __('dashboard.royalties_modal.checkout.generic_error');
$baseAmount = isset($planConfig['amount']) ? (int) $planConfig['amount'] : 0;
if ($baseAmount <= 0) {
    $baseAmount = 0;
}
$targetCurrency = $defaultCurrencyCode;
$unitAmount = $baseAmount;

if ($baseAmount > 0 && $canConvertCurrency) {
    $converted = convert_currency_amount($baseAmount, $defaultCurrencyCode, $userCurrencyCode, $currencyRates);

    if ($converted <= 0) {
        $converted = convert_currency_amount($baseAmount, $defaultCurrencyCode, $userCurrencyCode);
    }

    if ($converted > 0) {
        $unitAmount = $converted;
        $targetCurrency = $userCurrencyCode;
    }
}

if ($unitAmount <= 0) {
    $unitAmount = $baseAmount > 0 ? $baseAmount : 0;
}

if ($unitAmount <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $genericErrorMessage,
    ], JSON_THROW_ON_ERROR);
    exit;
}

$lineItem = [
    'quantity' => 1,
    'price_data' => [
        'currency' => $targetCurrency,
        'unit_amount' => $unitAmount,
        'product_data' => [
            'name' => $productName,
        ],
    ],
];

if ($productDescription !== '') {
    $lineItem['price_data']['product_data']['description'] = $productDescription;
}

if (($mode === 'subscription' || $mode === 'subscription_once') && isset($planConfig['interval'])) {
    $lineItem['price_data']['recurring'] = [
        'interval' => $planConfig['interval'],
        'interval_count' => $planConfig['interval_count'] ?? 1,
    ];
}

$payload = [
    'mode' => $mode === 'payment' ? 'payment' : 'subscription',
    'payment_method_types' => ['card'],
    'line_items' => [$lineItem],
    'success_url' => $successUrl,
    'cancel_url' => $cancelUrl,
    'client_reference_id' => (string) ($user['id'] ?? ''),
    'metadata' => [
        'user_id' => (string) ($user['id'] ?? ''),
        'plan' => $planKey,
    ],
];

if ($payload['mode'] === 'subscription') {
    $payload['allow_promotion_codes'] = true;
    $payload['subscription_data'] = [
        'metadata' => [
            'user_id' => (string) ($user['id'] ?? ''),
            'plan' => $planKey,
        ],
    ];
}

if (!empty($planConfig['metadata']) && is_array($planConfig['metadata'])) {
    $payload['metadata'] += $planConfig['metadata'];
    if (!empty($payload['subscription_data']['metadata'])) {
        $payload['subscription_data']['metadata'] += $planConfig['metadata'];
    }
}

if ($checkoutMetadata) {
    foreach ($checkoutMetadata as $metadataKey => $metadataValue) {
        $payload['metadata'][$metadataKey] = $metadataValue;
        if (!empty($payload['subscription_data']['metadata'])) {
            $payload['subscription_data']['metadata'][$metadataKey] = $metadataValue;
        }
    }
}

$payload['metadata']['currency'] = $targetCurrency;
$payload['metadata']['base_currency'] = $defaultCurrencyCode;
$payload['metadata']['base_amount'] = (string) $baseAmount;

if (!empty($payload['subscription_data'])) {
    $payload['subscription_data']['metadata']['currency'] = $targetCurrency;
    $payload['subscription_data']['metadata']['base_currency'] = $defaultCurrencyCode;
    $payload['subscription_data']['metadata']['base_amount'] = (string) $baseAmount;
}

if (!empty($user['email'])) {
    $payload['customer_email'] = (string) $user['email'];
}

$flatten = static function (array $data, string $prefix = '') use (&$flatten): array {
    $output = [];
    foreach ($data as $key => $value) {
        $composedKey = $prefix === '' ? (string) $key : $prefix . '[' . $key . ']';
        if (is_array($value)) {
            $output += $flatten($value, $composedKey);
        } elseif ($value !== null) {
            if (is_bool($value)) {
                $output[$composedKey] = $value ? 'true' : 'false';
            } else {
                $output[$composedKey] = $value;
            }
        }
    }
    return $output;
};

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($flatten($payload)));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Stripe-Version: 2023-10-16',
]);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(502);
    echo json_encode([
        'status' => 'error',
        'message' => $genericErrorMessage,
    ], JSON_THROW_ON_ERROR);
    exit;
}

$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 500;
curl_close($ch);

$decoded = json_decode($response, true);

if ($httpStatus >= 400 || !is_array($decoded)) {
    $message = $decoded['error']['message'] ?? $genericErrorMessage;
    http_response_code($httpStatus >= 400 ? $httpStatus : 500);
    echo json_encode([
        'status' => 'error',
        'message' => $message,
    ], JSON_THROW_ON_ERROR);
    exit;
}

if (empty($decoded['url'])) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $genericErrorMessage,
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode([
    'status' => 'success',
    'url' => $decoded['url'],
], JSON_THROW_ON_ERROR);
