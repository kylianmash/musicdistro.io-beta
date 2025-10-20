<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_authentication();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$user = current_user();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.auth_required'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$secretKey = trim((string) (get_setting('stripe_secret_key') ?? ''));

if ($secretKey === '') {
    echo json_encode([
        'status' => 'missing_key',
        'message' => __('dashboard.payments.errors.missing_key') ?: __('dashboard.royalties_modal.checkout.missing_key'),
        'payments' => [],
        'scheduled' => [],
        'now' => time(),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$filters = [
    'user_id' => (string) ($user['id'] ?? ''),
    'email' => strtolower(trim((string) ($user['email'] ?? ''))),
];

try {
    $subscriptionsPayload = stripe_request($secretKey, 'https://api.stripe.com/v1/subscriptions', [
        'limit' => 40,
        'status' => 'all',
        'expand[]' => ['data.latest_invoice', 'data.items.data.price.product'],
    ]);

    $invoicesPayload = stripe_request($secretKey, 'https://api.stripe.com/v1/invoices', [
        'limit' => 60,
        'expand[]' => ['data.subscription', 'data.payment_intent'],
    ]);

    $paymentIntentsPayload = stripe_request($secretKey, 'https://api.stripe.com/v1/payment_intents', [
        'limit' => 60,
        'expand[]' => ['data.latest_charge'],
    ]);
} catch (Throwable $exception) {
    http_response_code(502);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.payments.errors.unreachable') ?: __('dashboard.royalties_modal.checkout.generic_error'),
        'details' => $exception->getMessage(),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$subscriptions = is_array($subscriptionsPayload['data'] ?? null) ? $subscriptionsPayload['data'] : [];
$invoices = is_array($invoicesPayload['data'] ?? null) ? $invoicesPayload['data'] : [];
$paymentIntents = is_array($paymentIntentsPayload['data'] ?? null) ? $paymentIntentsPayload['data'] : [];

$scheduled = [];
$payments = [];
$consumedPaymentIntents = [];

foreach ($subscriptions as $subscription) {
    if (!is_array($subscription) || !belongsToUser($subscription['metadata'] ?? [], $filters)) {
        continue;
    }

    $scheduled[] = normalizeSubscription($subscription);
}

foreach ($invoices as $invoice) {
    if (!is_array($invoice)) {
        continue;
    }

    $subscriptionMetadata = [];
    if (isset($invoice['subscription']) && is_array($invoice['subscription'])) {
        $subscriptionMetadata = $invoice['subscription']['metadata'] ?? [];
    }

    if (!belongsToUser($invoice['metadata'] ?? [], $filters)
        && !belongsToUser($subscriptionMetadata, $filters)
        && !emailMatches((string) ($invoice['customer_email'] ?? ''), $filters['email'])) {
        continue;
    }

    $payment = normalizeInvoice($invoice);
    $payments[] = $payment;

    if (!empty($payment['payment_intent_id'])) {
        $consumedPaymentIntents[$payment['payment_intent_id']] = true;
    }
}

foreach ($paymentIntents as $paymentIntent) {
    if (!is_array($paymentIntent)) {
        continue;
    }

    $paymentIntentId = (string) ($paymentIntent['id'] ?? '');
    if ($paymentIntentId === '' || isset($consumedPaymentIntents[$paymentIntentId])) {
        continue;
    }

    if (!belongsToUser($paymentIntent['metadata'] ?? [], $filters)
        && !emailMatches((string) ($paymentIntent['receipt_email'] ?? ''), $filters['email'])
        && !emailMatches(extractChargeEmail($paymentIntent), $filters['email'])) {
        continue;
    }

    $payments[] = normalizePaymentIntent($paymentIntent);
}

usort($payments, static function (array $a, array $b): int {
    return ($b['created'] ?? 0) <=> ($a['created'] ?? 0);
});

usort($scheduled, static function (array $a, array $b): int {
    return ($a['scheduled_for'] ?? PHP_INT_MAX) <=> ($b['scheduled_for'] ?? PHP_INT_MAX);
});

echo json_encode([
    'status' => 'success',
    'payments' => $payments,
    'scheduled' => $scheduled,
    'now' => time(),
], JSON_THROW_ON_ERROR);

function belongsToUser(array $metadata, array $filters): bool
{
    $userId = $filters['user_id'] ?? '';
    if ($userId !== '') {
        foreach (['user_id', 'userId'] as $key) {
            if (isset($metadata[$key]) && (string) $metadata[$key] === $userId) {
                return true;
            }
        }
    }

    $email = $filters['email'] ?? '';
    if ($email !== '') {
        foreach (['email', 'user_email'] as $key) {
            if (isset($metadata[$key]) && emailMatches((string) $metadata[$key], $email)) {
                return true;
            }
        }
    }

    return false;
}

function emailMatches(string $value, string $expected): bool
{
    if ($value === '' || $expected === '') {
        return false;
    }

    return strtolower(trim($value)) === $expected;
}

function extractChargeEmail(array $paymentIntent): string
{
    $latestCharge = $paymentIntent['latest_charge'] ?? null;
    if (!is_array($latestCharge)) {
        return '';
    }

    $details = $latestCharge['billing_details'] ?? [];
    if (!is_array($details)) {
        return '';
    }

    return (string) ($details['email'] ?? '');
}

function normalizeInvoice(array $invoice): array
{
    $lines = $invoice['lines']['data'] ?? [];
    $line = is_array($lines) && count($lines) > 0 && is_array($lines[0]) ? $lines[0] : [];
    $price = $line['price'] ?? [];
    $metadataSources = [
        $invoice['metadata'] ?? [],
        $invoice['subscription']['metadata'] ?? [],
        $price['metadata'] ?? [],
    ];

    $planKey = extractPlanKey($metadataSources);
    $category = extractCategory($metadataSources);
    $interval = isset($price['recurring']['interval']) ? (string) $price['recurring']['interval'] : null;
    $retryUrl = (string) ($invoice['hosted_invoice_url'] ?? '');
    $paymentIntent = $invoice['payment_intent'] ?? null;
    $paymentIntentId = '';
    $paymentIntentStatus = '';
    $requiresAction = false;

    if (is_array($paymentIntent)) {
        $paymentIntentId = (string) ($paymentIntent['id'] ?? '');
        $paymentIntentStatus = (string) ($paymentIntent['status'] ?? '');
        $requiresAction = in_array($paymentIntentStatus, ['requires_payment_method', 'requires_action'], true);
        if (!$retryUrl && isset($paymentIntent['next_action']['redirect_to_url']['url'])) {
            $retryUrl = (string) $paymentIntent['next_action']['redirect_to_url']['url'];
        }
    } elseif (is_string($paymentIntent) && $paymentIntent !== '') {
        $paymentIntentId = $paymentIntent;
    }

    return [
        'id' => (string) ($invoice['id'] ?? ''),
        'type' => 'invoice',
        'status' => (string) ($invoice['status'] ?? ''),
        'is_paid' => (bool) ($invoice['paid'] ?? false),
        'amount' => (int) ($invoice['amount_paid'] ?? $invoice['amount_due'] ?? 0),
        'currency' => strtolower((string) ($invoice['currency'] ?? 'eur')),
        'created' => normalizeTimestamp($invoice['created'] ?? null),
        'description' => (string) ($invoice['description'] ?? ($line['description'] ?? '')),
        'plan_key' => $planKey,
        'category' => $category,
        'interval' => $interval,
        'hosted_invoice_url' => $retryUrl,
        'retry_url' => $retryUrl,
        'invoice_pdf' => (string) ($invoice['invoice_pdf'] ?? ''),
        'receipt_url' => (string) ($invoice['receipt_url'] ?? ''),
        'scheduled_for' => normalizeTimestamp($invoice['next_payment_attempt'] ?? null),
        'period_start' => normalizeTimestamp($invoice['period_start'] ?? ($line['period']['start'] ?? null)),
        'period_end' => normalizeTimestamp($invoice['period_end'] ?? ($line['period']['end'] ?? null)),
        'payment_intent_id' => $paymentIntentId,
        'payment_intent_status' => $paymentIntentStatus,
        'requires_action' => $requiresAction,
        'collection_method' => (string) ($invoice['collection_method'] ?? ''),
        'is_subscription' => isset($invoice['subscription']) && $invoice['subscription'] !== null,
        'metadata' => filterMetadata($invoice['metadata'] ?? []),
    ];
}

function normalizePaymentIntent(array $paymentIntent): array
{
    $metadata = $paymentIntent['metadata'] ?? [];
    $latestCharge = $paymentIntent['latest_charge'] ?? [];
    $planKey = extractPlanKey([$metadata]);
    $category = extractCategory([$metadata]);
    $description = (string) ($paymentIntent['description'] ?? ($latestCharge['description'] ?? ''));
    $receiptUrl = (string) ($latestCharge['receipt_url'] ?? '');
    $retryUrl = '';

    if (isset($paymentIntent['next_action']['redirect_to_url']['url'])) {
        $retryUrl = (string) $paymentIntent['next_action']['redirect_to_url']['url'];
    }

    return [
        'id' => (string) ($paymentIntent['id'] ?? ''),
        'type' => 'payment_intent',
        'status' => (string) ($paymentIntent['status'] ?? ''),
        'is_paid' => (string) ($paymentIntent['status'] ?? '') === 'succeeded',
        'amount' => (int) ($paymentIntent['amount_received'] ?? $paymentIntent['amount'] ?? 0),
        'currency' => strtolower((string) ($paymentIntent['currency'] ?? 'eur')),
        'created' => normalizeTimestamp($paymentIntent['created'] ?? null),
        'description' => $description,
        'plan_key' => $planKey,
        'category' => $category,
        'interval' => null,
        'hosted_invoice_url' => $retryUrl,
        'retry_url' => $retryUrl,
        'invoice_pdf' => '',
        'receipt_url' => $receiptUrl,
        'scheduled_for' => null,
        'period_start' => null,
        'period_end' => null,
        'payment_intent_id' => (string) ($paymentIntent['id'] ?? ''),
        'payment_intent_status' => (string) ($paymentIntent['status'] ?? ''),
        'requires_action' => in_array($paymentIntent['status'] ?? '', ['requires_payment_method', 'requires_action'], true),
        'collection_method' => '',
        'is_subscription' => false,
        'metadata' => filterMetadata($metadata),
    ];
}

function normalizeSubscription(array $subscription): array
{
    $items = $subscription['items']['data'] ?? [];
    $item = is_array($items) && count($items) > 0 && is_array($items[0]) ? $items[0] : [];
    $price = $item['price'] ?? [];
    $metadataSources = [
        $subscription['metadata'] ?? [],
        $price['metadata'] ?? [],
    ];

    $planKey = extractPlanKey($metadataSources);
    $category = extractCategory($metadataSources);

    $amount = 0;
    $currency = 'eur';

    if (isset($price['unit_amount'])) {
        $amount = (int) $price['unit_amount'] * (int) ($item['quantity'] ?? 1);
    }

    if (isset($price['currency'])) {
        $currency = strtolower((string) $price['currency']);
    }

    $latestInvoice = $subscription['latest_invoice'] ?? null;
    $latestInvoiceStatus = '';
    $retryUrl = '';
    $requiresAction = false;

    if (is_array($latestInvoice)) {
        $latestInvoiceStatus = (string) ($latestInvoice['status'] ?? '');
        if (isset($latestInvoice['hosted_invoice_url'])) {
            $retryUrl = (string) $latestInvoice['hosted_invoice_url'];
        }
        $paymentIntent = $latestInvoice['payment_intent'] ?? null;
        if (is_array($paymentIntent)) {
            $requiresAction = in_array($paymentIntent['status'] ?? '', ['requires_payment_method', 'requires_action'], true);
            if (!$retryUrl && isset($paymentIntent['next_action']['redirect_to_url']['url'])) {
                $retryUrl = (string) $paymentIntent['next_action']['redirect_to_url']['url'];
            }
        }
    }

    return [
        'id' => (string) ($subscription['id'] ?? ''),
        'status' => (string) ($subscription['status'] ?? ''),
        'plan_key' => $planKey,
        'category' => $category,
        'amount' => $amount,
        'currency' => $currency,
        'interval' => isset($price['recurring']['interval']) ? (string) $price['recurring']['interval'] : null,
        'scheduled_for' => normalizeTimestamp($subscription['current_period_end'] ?? null),
        'cancel_at_period_end' => (bool) ($subscription['cancel_at_period_end'] ?? false),
        'latest_invoice_status' => $latestInvoiceStatus,
        'latest_invoice_id' => is_array($latestInvoice) ? (string) ($latestInvoice['id'] ?? '') : '',
        'retry_url' => $retryUrl,
        'requires_action' => $requiresAction,
        'collection_method' => (string) ($subscription['collection_method'] ?? ''),
        'metadata' => filterMetadata($subscription['metadata'] ?? []),
    ];
}

function extractPlanKey(array $metadataSources): string
{
    foreach ($metadataSources as $metadata) {
        if (!is_array($metadata)) {
            continue;
        }
        foreach (['plan', 'plan_key', 'product_plan'] as $key) {
            if (isset($metadata[$key]) && $metadata[$key] !== '') {
                return (string) $metadata[$key];
            }
        }
    }

    return '';
}

function extractCategory(array $metadataSources): string
{
    foreach ($metadataSources as $metadata) {
        if (!is_array($metadata)) {
            continue;
        }
        foreach (['category', 'service'] as $key) {
            if (isset($metadata[$key]) && $metadata[$key] !== '') {
                return (string) $metadata[$key];
            }
        }
    }

    return '';
}

function normalizeTimestamp($value): ?int
{
    if (!is_numeric($value)) {
        return null;
    }

    $intValue = (int) $value;

    if ($intValue > 0 && $intValue < 2000000000) {
        return $intValue * 1000;
    }

    return $intValue > 0 ? $intValue : null;
}

function filterMetadata(array $metadata): array
{
    $allowed = [];
    foreach ($metadata as $key => $value) {
        if (in_array($key, ['user_id', 'userId'], true)) {
            continue;
        }
        if (is_scalar($value)) {
            $allowed[$key] = (string) $value;
        }
    }

    return $allowed;
}
