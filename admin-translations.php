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

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'GET') {
    handle_translations_index();
    exit;
}

if ($method === 'POST') {
    handle_translations_update();
    exit;
}

http_response_code(405);
header('Allow: GET, POST');
echo json_encode([
    'status' => 'error',
    'message' => __('validation.method_not_allowed'),
], JSON_THROW_ON_ERROR);
exit;

function flatten_translations(array $translations, string $prefix = ''): array
{
    $result = [];

    foreach ($translations as $key => $value) {
        if (!is_string($key) && !is_int($key)) {
            continue;
        }

        $segment = (string) $key;
        $path = $prefix === '' ? $segment : $prefix . '.' . $segment;

        if (is_array($value)) {
            $nested = flatten_translations($value, $path);
            foreach ($nested as $nestedKey => $nestedValue) {
                $result[$nestedKey] = $nestedValue;
            }
            continue;
        }

        if (is_scalar($value) || $value === null) {
            $result[$path] = $value === null ? '' : (string) $value;
        }
    }

    return $result;
}

function get_nested_value(array $data, array $segments)
{
    $current = $data;

    foreach ($segments as $segment) {
        if (!is_array($current) || !array_key_exists($segment, $current)) {
            return null;
        }

        $current = $current[$segment];
    }

    return $current;
}

function set_nested_value(array &$data, array $segments, string $value): void
{
    $segment = array_shift($segments);

    if ($segment === null) {
        return;
    }

    if ($segments === []) {
        $data[$segment] = $value;
        return;
    }

    if (!isset($data[$segment]) || !is_array($data[$segment])) {
        $data[$segment] = [];
    }

    set_nested_value($data[$segment], $segments, $value);
}

function remove_nested_value(array &$data, array $segments): void
{
    $segment = array_shift($segments);

    if ($segment === null || !array_key_exists($segment, $data)) {
        return;
    }

    if ($segments === []) {
        unset($data[$segment]);
    } else {
        if (!is_array($data[$segment])) {
            return;
        }

        remove_nested_value($data[$segment], $segments);

        if ($data[$segment] === [] || $data[$segment] === null) {
            unset($data[$segment]);
        }
    }
}

function sanitize_segments(string $key): array
{
    $segments = array_filter(
        explode('.', $key),
        static fn ($segment) => $segment !== ''
    );

    return array_map(static fn ($segment) => (string) $segment, $segments);
}

function handle_translations_index(): void
{
    $languages = all_languages();
    $available = available_languages();
    $enabledLocales = array_fill_keys(array_keys($available), true);
    $languagePayload = [];

    foreach ($languages as $code => $meta) {
        $direction = strtolower((string) ($meta['direction'] ?? 'ltr')) === 'rtl' ? 'rtl' : 'ltr';
        $languagePayload[] = [
            'code' => $code,
            'label' => (string) ($meta['label'] ?? strtoupper($code)),
            'native' => (string) ($meta['native'] ?? ''),
            'direction' => $direction,
            'flag' => language_flag($code),
            'enabled' => isset($enabledLocales[$code]),
        ];
    }

    $localeTranslations = [];
    $keys = [];

    foreach (array_keys($languages) as $locale) {
        $data = translation_locale_data($locale);
        $flattened = flatten_translations($data);
        $localeTranslations[$locale] = $flattened;
        $keys = array_unique(array_merge($keys, array_keys($flattened)));
    }

    sort($keys);

    $translationsPayload = [];
    foreach ($keys as $key) {
        $entry = [];
        foreach (array_keys($languages) as $locale) {
            $value = $localeTranslations[$locale][$key] ?? '';
            $entry[$locale] = is_scalar($value) ? (string) $value : '';
        }
        $translationsPayload[$key] = $entry;
    }

    echo json_encode([
        'status' => 'success',
        'languages' => $languagePayload,
        'translations' => $translationsPayload,
    ], JSON_THROW_ON_ERROR);
}

function handle_translations_update(): void
{
    $rawInput = file_get_contents('php://input');
    $payload = [];

    if (is_string($rawInput) && $rawInput !== '') {
        try {
            $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        } catch (JsonException $exception) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => __('validation.json_invalid'),
            ], JSON_THROW_ON_ERROR);
            return;
        }
    }

    if (!$payload) {
        $payload = $_POST;
    }

    $translationsInput = $payload['translations'] ?? [];

    if (!is_array($translationsInput)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.json_invalid'),
        ], JSON_THROW_ON_ERROR);
        return;
    }

    $languages = all_languages();
    $repository = translation_repository();
    $baseTranslations = $repository['translations'] ?? [];
    $currentOverrides = translation_overrides();
    $updatedOverrides = $currentOverrides;
    $changes = false;

    foreach ($translationsInput as $locale => $entries) {
        if (!is_string($locale)) {
            continue;
        }

        $normalizedLocale = strtolower(trim($locale));
        if ($normalizedLocale === '' || !array_key_exists($normalizedLocale, $languages)) {
            continue;
        }

        if (!is_array($entries)) {
            continue;
        }

        foreach ($entries as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }

            if (!is_scalar($value)) {
                continue;
            }

            $segments = sanitize_segments($key);

            if ($segments === []) {
                continue;
            }

            $stringValue = (string) $value;

            $baseValue = get_nested_value($baseTranslations[$normalizedLocale] ?? [], $segments);
            $baseString = null;

            if (is_scalar($baseValue) || $baseValue === null) {
                $baseString = $baseValue === null ? '' : (string) $baseValue;
            }

            if ($baseString !== null && $stringValue === $baseString) {
                if (isset($updatedOverrides[$normalizedLocale])) {
                    remove_nested_value($updatedOverrides[$normalizedLocale], $segments);
                    if ($updatedOverrides[$normalizedLocale] === [] || $updatedOverrides[$normalizedLocale] === null) {
                        unset($updatedOverrides[$normalizedLocale]);
                    }
                    $changes = true;
                }
                continue;
            }

            if (!isset($updatedOverrides[$normalizedLocale]) || !is_array($updatedOverrides[$normalizedLocale])) {
                $updatedOverrides[$normalizedLocale] = [];
            }

            set_nested_value($updatedOverrides[$normalizedLocale], $segments, $stringValue);
            $changes = true;
        }
    }

    if (!$changes) {
        echo json_encode([
            'status' => 'success',
            'message' => __('dashboard.admin.translations.feedback.saved'),
        ], JSON_THROW_ON_ERROR);
        return;
    }

    set_translation_overrides($updatedOverrides);

    echo json_encode([
        'status' => 'success',
        'message' => __('dashboard.admin.translations.feedback.saved'),
    ], JSON_THROW_ON_ERROR);
}
