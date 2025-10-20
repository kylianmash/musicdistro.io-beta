<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const SITE_NAME = 'MusicDistro.io';
const APP_URL = 'https://musicdistro.io';
const SUPPORT_EMAIL = 'support@musicdistro.io';
const EMAIL_FROM = 'MusicDistro.io <no-reply@musicdistro.io>';
const SONOSUITE_BASE_URL = 'https://platform.musicdistribution.cloud';
const SONOSUITE_SHARED_SECRET = 'qwertyuiopasdfghjklzxcvbnm123456';

/**
 * Retrieve the translations repository.
 */
function translation_repository(): array
{
    static $repository;

    if ($repository === null) {
        $repository = require __DIR__ . '/translations.php';
    }

    return $repository;
}

/**
 * List available languages.
 *
 * @return array<string, array{label: string, native: string, direction?: string}>
 */
function all_languages(): array
{
    $repository = translation_repository();

    return $repository['available_languages'] ?? [];
}

function available_languages(): array
{
    $languages = all_languages();

    if ($languages === []) {
        return [];
    }

    $multilingualSetting = get_setting('languages_multilingual_enabled');
    $multilingualEnabled = $multilingualSetting === null ? true : $multilingualSetting !== '0';

    $enabledListSetting = get_setting('languages_enabled_list');
    $enabledSet = null;

    if ($enabledListSetting !== null && trim($enabledListSetting) !== '') {
        try {
            $decoded = json_decode($enabledListSetting, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                $enabledSet = [];
                foreach ($decoded as $code) {
                    if (!is_string($code)) {
                        continue;
                    }
                    $normalized = strtolower(trim($code));
                    if ($normalized === '' || !array_key_exists($normalized, $languages)) {
                        continue;
                    }
                    $enabledSet[$normalized] = true;
                }
            }
        } catch (JsonException $exception) {
            $enabledSet = null;
        }
    }

    $defaultLanguage = get_setting('languages_default');
    if (!is_string($defaultLanguage) || $defaultLanguage === '' || !array_key_exists(strtolower($defaultLanguage), $languages)) {
        $defaultLanguage = array_key_first($languages) ?? 'en';
    }
    $defaultLanguage = strtolower((string) $defaultLanguage);
    if (!array_key_exists($defaultLanguage, $languages)) {
        $defaultLanguage = 'en';
    }
    if (!array_key_exists($defaultLanguage, $languages)) {
        $defaultLanguage = array_key_first($languages);
    }

    if ($multilingualEnabled === false) {
        if ($defaultLanguage === null) {
            return $languages;
        }

        return $defaultLanguage !== null && isset($languages[$defaultLanguage])
            ? [$defaultLanguage => $languages[$defaultLanguage]]
            : $languages;
    }

    if (is_array($enabledSet) && $enabledSet !== []) {
        $filtered = array_filter(
            $languages,
            static fn ($meta, $code) => isset($enabledSet[$code]) || $code === $defaultLanguage,
            ARRAY_FILTER_USE_BOTH
        );

        if ($filtered !== []) {
            return $filtered;
        }
    }

    return $languages;
}

/**
 * Retrieve metadata for a given language.
 *
 * @return array{label?: string, native?: string, direction?: string}
 */
function language_metadata(?string $language = null): array
{
    $language = $language ? normalize_language($language) : current_language();
    $languages = available_languages();
    $metadata = $languages[$language] ?? [];

    if (!isset($metadata['direction'])) {
        $metadata['direction'] = 'ltr';
    }

    return $metadata;
}

function language_direction(?string $language = null): string
{
    $metadata = language_metadata($language);
    $direction = strtolower((string) ($metadata['direction'] ?? 'ltr'));

    return $direction === 'rtl' ? 'rtl' : 'ltr';
}

function normalize_language(?string $language): string
{
    $language = strtolower(trim((string) $language));
    $available = available_languages();

    if ($language !== '' && array_key_exists($language, $available)) {
        return $language;
    }

    $defaultSetting = get_setting('languages_default');
    if (is_string($defaultSetting)) {
        $default = strtolower(trim($defaultSetting));
        if ($default !== '' && array_key_exists($default, $available)) {
            return $default;
        }
    }

    if (array_key_exists('en', $available)) {
        return 'en';
    }

    $first = array_key_first($available);
    if ($first !== null) {
        return $first;
    }

    return $language !== '' ? $language : 'en';
}

function language_flag(?string $language): string
{
    static $flags = [
        'fr' => '🇫🇷',
        'en' => '🇬🇧',
        'fi' => '🇫🇮',
        'es' => '🇪🇸',
        'pt' => '🇵🇹',
        'de' => '🇩🇪',
        'it' => '🇮🇹',
        'nl' => '🇳🇱',
        'sv' => '🇸🇪',
        'no' => '🇳🇴',
        'ar' => '🇸🇦',
        'ro' => '🇷🇴',
        'hi' => '🇮🇳',
        'ja' => '🇯🇵',
        'vi' => '🇻🇳',
        'zh' => '🇨🇳',
    ];

    $normalized = strtolower(trim((string) $language));

    if ($normalized === '') {
        $normalized = 'en';
    }

    return $flags[$normalized] ?? '🏳️';
}

function set_current_language(string $language, bool $persist = true): void
{
    $normalized = normalize_language($language);
    $_SESSION['language'] = $normalized;

    if ($persist) {
        setcookie('language', $normalized, [
            'expires' => time() + 365 * 24 * 60 * 60,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    }
}

function detect_language(): string
{
    if (!empty($_GET['lang'])) {
        return normalize_language($_GET['lang']);
    }

    if (!empty($_SESSION['language'])) {
        return normalize_language($_SESSION['language']);
    }

    if (!empty($_COOKIE['language'])) {
        return normalize_language($_COOKIE['language']);
    }

    $autoDetectSetting = get_setting('languages_auto_detect_enabled');
    $autoDetectEnabled = $autoDetectSetting === null ? true : $autoDetectSetting !== '0';

    if ($autoDetectEnabled) {
        $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($header !== '') {
            $available = array_keys(available_languages());
            $candidates = [];
            $position = 0;

            foreach (explode(',', (string) $header) as $segment) {
                $segment = trim($segment);
                if ($segment === '') {
                    $position++;
                    continue;
                }

                $parts = explode(';', $segment);
                $locale = strtolower(trim((string) array_shift($parts)));

                if ($locale === '' || $locale === '*') {
                    $position++;
                    continue;
                }

                $quality = 1.0;
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '') {
                        continue;
                    }

                    [$key, $value] = array_map('trim', explode('=', $part, 2) + [null, null]);
                    if ($key === null || $value === null || strtolower($key) !== 'q') {
                        continue;
                    }

                    $parsed = (float) $value;
                    if ($parsed <= 0.0) {
                        $quality = 0.0;
                    } elseif ($parsed >= 1.0) {
                        $quality = 1.0;
                    } else {
                        $quality = $parsed;
                    }

                    break;
                }

                if ($quality <= 0.0) {
                    $position++;
                    continue;
                }

                $candidates[] = [
                    'locale' => str_replace('_', '-', $locale),
                    'quality' => $quality,
                    'index' => $position,
                ];

                $position++;
            }

            if ($candidates) {
                usort($candidates, static function (array $a, array $b): int {
                    if ($a['quality'] === $b['quality']) {
                        return $a['index'] <=> $b['index'];
                    }

                    return $a['quality'] < $b['quality'] ? 1 : -1;
                });

                foreach ($candidates as $candidate) {
                    $locale = $candidate['locale'];

                    if (in_array($locale, $available, true)) {
                        return $locale;
                    }

                    $primary = substr($locale, 0, 2);
                    if ($primary !== '' && in_array($primary, $available, true)) {
                        return $primary;
                    }

                    if (strpos($locale, '-') !== false) {
                        $compact = str_replace('-', '', $locale);
                        if (in_array($compact, $available, true)) {
                            return $compact;
                        }
                    }
                }
            }
        }
    }

    $defaultSetting = get_setting('languages_default');
    $fallback = is_string($defaultSetting) && $defaultSetting !== '' ? $defaultSetting : 'en';

    return normalize_language($fallback);
}

function current_language(): string
{
    static $language;

    if ($language === null) {
        $language = detect_language();
        set_current_language($language);
    }

    return $language;
}

function translation_overrides(bool $refresh = false): array
{
    static $overrides;

    if ($refresh || $overrides === null) {
        $stored = get_setting('translation_overrides');

        if ($stored === null || trim($stored) === '') {
            $overrides = [];
        } else {
            try {
                $decoded = json_decode($stored, true, 512, JSON_THROW_ON_ERROR);
                $overrides = is_array($decoded) ? $decoded : [];
            } catch (JsonException $exception) {
                $overrides = [];
            }
        }
    }

    return $overrides;
}

function set_translation_overrides(array $overrides): void
{
    $normalized = [];

    foreach ($overrides as $locale => $values) {
        if (!is_string($locale) || $locale === '' || !is_array($values)) {
            continue;
        }

        $normalizedLocale = strtolower(trim($locale));

        if ($normalizedLocale === '') {
            continue;
        }

        $normalized[$normalizedLocale] = $values;
    }

    if ($normalized === []) {
        set_setting('translation_overrides', null);
    } else {
        $encoded = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        set_setting('translation_overrides', $encoded === false ? '{}' : $encoded);
    }

    translation_overrides(true);
}

function translation_locale_data(string $locale): array
{
    $normalizedLocale = strtolower(trim($locale));
    if ($normalizedLocale === '') {
        $normalizedLocale = 'en';
    }

    $repository = translation_repository();
    $translations = $repository['translations'] ?? [];
    $data = $translations[$normalizedLocale] ?? [];

    $overrides = translation_overrides();
    $localeOverrides = $overrides[$normalizedLocale] ?? [];

    if (is_array($data) && is_array($localeOverrides) && $localeOverrides !== []) {
        $data = array_replace_recursive($data, $localeOverrides);
    }

    return is_array($data) ? $data : [];
}

function translation_get(string $locale, string $key)
{
    $data = translation_locale_data($locale);
    $segments = explode('.', $key);
    $value = $data;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return null;
        }

        $value = $value[$segment];
    }

    return $value;
}

function __(
    string $key,
    array $replace = [],
    ?string $locale = null,
    ?string $fallbackLocale = 'en'
) {
    $locale = $locale ? normalize_language($locale) : current_language();
    $value = translation_get($locale, $key);

    if ($value === null && $fallbackLocale !== null) {
        $value = translation_get(normalize_language($fallbackLocale), $key);
    }

    if ($value === null) {
        $value = $key;
    }

    if (is_string($value)) {
        foreach ($replace as $search => $replacement) {
            $value = str_replace(':' . $search, (string) $replacement, $value);
        }
    }

    return $value;
}

function __e(string $key, array $replace = [], ?string $locale = null, ?string $fallbackLocale = 'en'): string
{
    return htmlspecialchars((string) __($key, $replace, $locale, $fallbackLocale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function __d(string $key, ?string $locale = null)
{
    $value = __($key, [], $locale);

    if (!is_array($value)) {
        return [];
    }

    return $value;
}

function render_language_switcher(string $variant = 'menu'): string
{
    $languages = available_languages();

    if (count($languages) <= 1) {
        return '';
    }

    static $instance = 0;
    $instance++;

    $id = 'language-switcher-' . $instance;
    $current = current_language();
    $currentDirection = language_direction($current);
    $normalizedVariant = preg_replace('/[^a-z0-9_-]/i', '', $variant) ?: 'default';
    $redirectValue = $_SERVER['REQUEST_URI'] ?? '/';
    if (!is_string($redirectValue) || $redirectValue === '') {
        $redirectValue = '/';
    }

    ob_start();
    ?>
    <?php if ($normalizedVariant === 'nav'): ?>
      <?php
      $currentLabel = (string) ($languages[$current]['native'] ?? $languages[$current]['label'] ?? strtoupper($current));
      $menuLabel = (string) __('language.menu_label');
      $dropdownId = $id . '-dropdown';
      ?>
      <form class="language-switcher language-switcher--nav" method="post" action="/set-language.php" data-language-switcher="nav">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <button
          class="language-switcher__toggle"
          type="button"
          aria-haspopup="listbox"
          aria-expanded="false"
          aria-controls="<?= htmlspecialchars($dropdownId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          aria-label="<?= htmlspecialchars(trim($menuLabel . ' (' . $currentLabel . ')'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          title="<?= htmlspecialchars(trim($menuLabel . ' (' . $currentLabel . ')'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          lang="<?= htmlspecialchars($current, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          dir="<?= htmlspecialchars($currentDirection, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
          <span class="sr-only"><?= __e('language.menu_label') ?></span>
          <span class="language-switcher__toggle-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
              <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                <path d="M3.5 4.75h7a1.25 1.25 0 0 1 1.25 1.25v7a1.25 1.25 0 0 1-1.25 1.25h-7A1.25 1.25 0 0 1 2.25 13V6a1.25 1.25 0 0 1 1.25-1.25Z" />
                <path d="M5.4 12.25 7 7.75l1.6 4.5M6 10.5h2" />
                <path d="M11.75 6.25h4.5A1.5 1.5 0 0 1 17.75 7.75v6" />
                <path d="M12.75 9.5c0 2.7 2.05 4.75 4.5 4.75" />
                <path d="M14.25 9.25 17 14.5" />
              </g>
            </svg>
          </span>
          <span class="language-switcher__toggle-indicator" aria-hidden="true"></span>
        </button>
        <div
          class="language-switcher__dropdown"
          id="<?= htmlspecialchars($dropdownId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          role="listbox"
          aria-label="<?= __e('language.choose_language') ?>"
          aria-hidden="true"
        >
          <div class="language-switcher__dropdown-header">
            <span><?= __e('language.choose_language') ?></span>
            <button class="language-switcher__close" type="button" aria-label="<?= __e('language.close_menu') ?>">
              <svg width="16" height="16" viewBox="0 0 16 16" role="img" aria-hidden="true" focusable="false">
                <path
                  fill="currentColor"
                  fill-rule="evenodd"
                  d="M3.22 3.22a.75.75 0 0 1 1.06 0L8 6.94l3.72-3.72a.75.75 0 1 1 1.06 1.06L9.06 8l3.72 3.72a.75.75 0 1 1-1.06 1.06L8 9.06l-3.72 3.72a.75.75 0 0 1-1.06-1.06L6.94 8 3.22 4.28a.75.75 0 0 1 0-1.06Z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
          <div class="language-switcher__options">
            <?php foreach ($languages as $code => $info): ?>
              <?php
              $primary = (string) ($info['label'] ?? strtoupper($code));
              $secondary = (string) ($info['native'] ?? '');
              if (trim($secondary) === '' || strcasecmp($primary, $secondary) === 0) {
                  $secondary = strtoupper($code);
              }
              $direction = strtolower((string) ($info['direction'] ?? 'ltr')) === 'rtl' ? 'rtl' : 'ltr';
              $flag = language_flag($code);
              ?>
              <button
                class="language-switcher__option<?= $code === $current ? ' language-switcher__option--active' : '' ?>"
                type="submit"
                name="language"
                value="<?= htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                role="option"
                aria-selected="<?= $code === $current ? 'true' : 'false' ?>"
                lang="<?= htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                dir="<?= htmlspecialchars($direction, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              >
                <span class="language-switcher__option-text">
                  <span class="language-switcher__option-primary">
                    <span class="language-switcher__option-flag" aria-hidden="true"><?= htmlspecialchars($flag, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                    <span class="language-switcher__option-primary-label"><?= htmlspecialchars($primary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                  </span>
                  <span class="language-switcher__option-secondary"><?= htmlspecialchars($secondary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                </span>
                <span class="language-switcher__option-icon" aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 16 16" role="img" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M6.35 11.35 3.7 8.7a.75.75 0 0 1 1.06-1.06l1.94 1.94 4.54-4.54a.75.75 0 0 1 1.06 1.06l-5.07 5.07a.75.75 0 0 1-1.06 0Z" />
                  </svg>
                </span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </form>
    <?php else: ?>
      <form class="language-switcher language-switcher--<?= htmlspecialchars($normalizedVariant, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" method="post" action="/set-language.php">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <label class="language-switcher__label" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;" for="<?= htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= __e('language.label') ?></label>
        <select
          id="<?= htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          name="language"
          aria-label="<?= __e('language.menu_label') ?>"
          onchange="this.form.submit()"
          lang="<?= htmlspecialchars($current, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
          dir="<?= htmlspecialchars($currentDirection, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
          <?php foreach ($languages as $code => $info): ?>
            <?php
            $direction = strtolower((string) ($info['direction'] ?? 'ltr')) === 'rtl' ? 'rtl' : 'ltr';
            $optionLabel = trim(language_flag($code) . ' ' . (string) ($info['native'] ?? $info['label'] ?? strtoupper($code)));
            ?>
            <option
              value="<?= htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              <?= $code === $current ? 'selected' : '' ?>
              lang="<?= htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              dir="<?= htmlspecialchars($direction, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            ><?= htmlspecialchars($optionLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>
    <?php

    return trim((string) ob_get_clean());
}

$databasePath = __DIR__ . '/storage/app.sqlite';
$initializeDatabase = !file_exists($databasePath);

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

if ($initializeDatabase) {
    $pdo->exec('CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        country TEXT NOT NULL,
        role TEXT NOT NULL,
        language TEXT NOT NULL DEFAULT "en",
        is_verified INTEGER NOT NULL DEFAULT 0,
        verification_token TEXT,
        password_reset_token TEXT,
        password_reset_expires_at TEXT,
        created_at TEXT NOT NULL,
        address_line1 TEXT,
        address_line2 TEXT,
        postal_code TEXT,
        city TEXT,
        phone_number TEXT,
        company_type TEXT NOT NULL DEFAULT "individual",
        company_name TEXT,
        company_vat TEXT
    )');
}

$columns = $pdo->query('PRAGMA table_info(users)')->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');

if (!in_array('avatar_path', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN avatar_path TEXT');
}

if (!in_array('created_ip', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN created_ip TEXT');
}

if (!in_array('last_login_ip', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN last_login_ip TEXT');
}

if (!in_array('last_login_at', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN last_login_at TEXT');
}

if (!in_array('is_super_admin', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN is_super_admin INTEGER NOT NULL DEFAULT 0');
}

if (!in_array('is_blocked', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN is_blocked INTEGER NOT NULL DEFAULT 0');
}

if (!in_array('password_reset_token', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN password_reset_token TEXT');
}

if (!in_array('password_reset_expires_at', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN password_reset_expires_at TEXT');
}

if (!in_array('address_line1', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN address_line1 TEXT');
}

if (!in_array('address_line2', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN address_line2 TEXT');
}

if (!in_array('postal_code', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN postal_code TEXT');
}

if (!in_array('city', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN city TEXT');
}

if (!in_array('phone_number', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN phone_number TEXT');
}

if (!in_array('company_type', $columnNames, true)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN company_type TEXT NOT NULL DEFAULT 'individual'");
}

if (!in_array('company_name', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN company_name TEXT');
}

if (!in_array('company_vat', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN company_vat TEXT');
}

if (!in_array('currency', $columnNames, true)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN currency TEXT NOT NULL DEFAULT 'eur'");
}

$hasSettingsTable = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'settings' LIMIT 1")->fetchColumn();

if ($hasSettingsTable === false) {
    $pdo->exec('CREATE TABLE settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )');
}

if (!in_array('language', $columnNames, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN language TEXT NOT NULL DEFAULT "en"');
}

$hasLoginEventsTable = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'user_login_events' LIMIT 1")->fetchColumn();

if ($hasLoginEventsTable === false) {
    $pdo->exec('CREATE TABLE user_login_events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        ip_address TEXT,
        user_agent TEXT,
        device_type TEXT,
        os_name TEXT,
        browser_name TEXT,
        created_at TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');
    $pdo->exec('CREATE INDEX idx_user_login_events_user ON user_login_events(user_id)');
    $pdo->exec('CREATE INDEX idx_user_login_events_created_at ON user_login_events(created_at)');
}

$hasBroadcastNotificationsTable = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'broadcast_notifications' LIMIT 1")->fetchColumn();

if ($hasBroadcastNotificationsTable === false) {
    $pdo->exec('CREATE TABLE broadcast_notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        translations TEXT NOT NULL,
        link_url TEXT,
        created_at TEXT NOT NULL,
        created_by INTEGER,
        FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL
    )');
    $pdo->exec('CREATE INDEX idx_broadcast_notifications_created_at ON broadcast_notifications(created_at)');
}

$superAdminEmail = 'kylianmash@me.com';
$assignSuperAdmin = $pdo->prepare('UPDATE users SET is_super_admin = 1 WHERE LOWER(email) = :email');
$assignSuperAdmin->execute([':email' => strtolower($superAdminEmail)]);

function app_url(string $path = ''): string
{
    $normalized = '/' . ltrim($path, '/');
    return rtrim(APP_URL, '/') . $normalized;
}

function base_path(string $path = ''): string
{
    $normalized = '/' . ltrim($path, '/');
    return __DIR__ . $normalized;
}

function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        return null;
    }

    if (!empty($user['language'])) {
        set_current_language($user['language']);
    }

    $currencySettings = currency_settings();
    $enabledCurrencies = array_fill_keys($currencySettings['enabled'] ?? [], true);
    $userCurrency = normalize_currency($user['currency'] ?? '');

    if ($userCurrency === '' || !isset($enabledCurrencies[$userCurrency])) {
        $user['currency'] = $currencySettings['default'] ?? 'eur';
    }

    return $user;
}

function get_setting(string $key, ?string $default = null): ?string
{
    $key = trim($key);

    if ($key === '') {
        return $default;
    }

    global $pdo;
    $statement = $pdo->prepare('SELECT value FROM settings WHERE key = :key LIMIT 1');
    $statement->execute([':key' => $key]);
    $value = $statement->fetchColumn();

    if ($value === false) {
        return $default;
    }

    return (string) $value;
}

function site_name(): string
{
    $storedName = trim((string) get_setting('site_name'));

    if ($storedName !== '') {
        return $storedName;
    }

    return SITE_NAME;
}

function site_logo_url(): string
{
    $logoPath = trim((string) get_setting('site_logo_path'));

    if ($logoPath !== '') {
        return $logoPath;
    }

    return '/assets/musicdistro-logo.svg';
}

function dashboard_logo_url(): string
{
    $logoPath = trim((string) get_setting('dashboard_logo_path'));

    if ($logoPath !== '') {
        return $logoPath;
    }

    return '/assets/musicdistro-logo-dashboard.svg';
}

function site_favicon_url(): string
{
    $faviconPath = trim((string) get_setting('site_favicon_path'));

    if ($faviconPath !== '') {
        return $faviconPath;
    }

    return '/assets/musicdistro-icon.svg';
}

function set_setting(string $key, ?string $value): void
{
    $key = trim($key);

    if ($key === '') {
        return;
    }

    global $pdo;

    if ($value === null || trim($value) === '') {
        $delete = $pdo->prepare('DELETE FROM settings WHERE key = :key');
        $delete->execute([':key' => $key]);

        return;
    }

    $normalizedValue = trim($value);
    $timestamp = (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339);
    $statement = $pdo->prepare('INSERT INTO settings (key, value, updated_at) VALUES (:key, :value, :updated_at)
        ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at');
    $statement->execute([
        ':key' => $key,
        ':value' => $normalizedValue,
        ':updated_at' => $timestamp,
    ]);
}

function stripe_request(string $secretKey, string $endpoint, array $params = [], string $method = 'GET'): array
{
    $secretKey = trim($secretKey);

    if ($secretKey === '') {
        throw new RuntimeException('Stripe secret key is missing.');
    }

    if (!str_starts_with($endpoint, 'http')) {
        $endpoint = 'https://api.stripe.com' . (str_starts_with($endpoint, '/') ? '' : '/') . $endpoint;
    }

    $query = '';
    $requestBody = null;
    $normalizedMethod = strtoupper($method);

    if ($normalizedMethod === 'GET' && $params) {
        $query = '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    } elseif ($params) {
        $requestBody = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    $url = $endpoint . $query;

    $ch = curl_init($url);

    if ($ch === false) {
        throw new RuntimeException('Unable to initialise Stripe request.');
    }

    $headers = [
        'Stripe-Version: 2023-10-16',
    ];

    $options = [
        CURLOPT_USERPWD => $secretKey . ':',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $normalizedMethod,
    ];

    if ($requestBody !== null) {
        $options[CURLOPT_POSTFIELDS] = $requestBody;
    }

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException($error !== '' ? $error : 'Stripe request failed.');
    }

    $decoded = json_decode($response, true);

    if (!is_array($decoded)) {
        throw new RuntimeException('Invalid Stripe response.');
    }

    if ($statusCode >= 400) {
        $message = $decoded['error']['message'] ?? 'Stripe request failed.';
        throw new RuntimeException((string) $message);
    }

    return $decoded;
}

function normalize_currency(?string $currency): string
{
    $normalized = strtolower(trim((string) $currency));

    return preg_match('/^[a-z]{3}$/', $normalized) ? $normalized : '';
}

function stripe_supported_currencies(): array
{
    static $currencies;

    if ($currencies !== null) {
        return $currencies;
    }

    $currencies = [
        'aed', 'aud', 'bgn', 'brl', 'cad', 'chf', 'czk', 'dkk', 'eur', 'gbp', 'hkd', 'hrk', 'huf', 'idr', 'ils', 'inr', 'jpy',
        'krw', 'mxn', 'myr', 'nok', 'nzd', 'php', 'pln', 'ron', 'sek', 'sgd', 'thb', 'try', 'twd', 'usd', 'zar',
    ];

    sort($currencies);

    return $currencies;
}

function currency_symbol(string $currency): string
{
    $currency = normalize_currency($currency);

    $symbols = [
        'aed' => 'د.إ',
        'ars' => '$',
        'aud' => 'A$',
        'brl' => 'R$',
        'cad' => 'C$',
        'chf' => 'CHF',
        'cny' => '¥',
        'czk' => 'Kč',
        'dkk' => 'kr',
        'egp' => 'E£',
        'eur' => '€',
        'gbp' => '£',
        'hkd' => 'HK$',
        'huf' => 'Ft',
        'ils' => '₪',
        'inr' => '₹',
        'jpy' => '¥',
        'krw' => '₩',
        'mxn' => 'MX$',
        'nok' => 'kr',
        'nzd' => 'NZ$',
        'pln' => 'zł',
        'ron' => 'lei',
        'rub' => '₽',
        'sek' => 'kr',
        'sgd' => 'S$',
        'thb' => '฿',
        'try' => '₺',
        'usd' => '$',
        'zar' => 'R',
    ];

    return $symbols[$currency] ?? strtoupper($currency);
}

function currency_settings(): array
{
    $supported = stripe_supported_currencies();
    $supportedSet = array_fill_keys($supported, true);

    $defaultCurrency = normalize_currency(get_setting('currency_default') ?? '') ?: 'eur';
    if (!isset($supportedSet[$defaultCurrency])) {
        $defaultCurrency = 'eur';
    }

    $allowUserChoiceSetting = get_setting('currency_allow_user_choice');
    $allowUserChoice = $allowUserChoiceSetting === null ? true : $allowUserChoiceSetting !== '0';

    $enabledSetting = get_setting('currency_enabled_list');
    $enabled = [];

    if ($enabledSetting) {
        try {
            $decoded = json_decode($enabledSetting, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                foreach ($decoded as $code) {
                    $normalized = normalize_currency((string) $code);
                    if ($normalized !== '' && isset($supportedSet[$normalized])) {
                        $enabled[$normalized] = true;
                    }
                }
            }
        } catch (JsonException $exception) {
            $enabled = [];
        }
    }

    if (!$enabled) {
        $enabled[$defaultCurrency] = true;
    }

    $enabled[$defaultCurrency] = true;

    return [
        'default' => $defaultCurrency,
        'allow_user_choice' => $allowUserChoice,
        'enabled' => array_keys($enabled),
        'supported' => $supported,
    ];
}

function currency_allow_user_choice(): bool
{
    $settings = currency_settings();

    return $settings['allow_user_choice'] ?? false;
}

function default_currency(): string
{
    $settings = currency_settings();

    return $settings['default'] ?? 'eur';
}

function user_currency(?array $user = null): string
{
    $settings = currency_settings();
    $enabled = array_fill_keys($settings['enabled'] ?? [], true);

    if ($user && isset($user['currency'])) {
        $currency = normalize_currency((string) $user['currency']);
        if ($currency !== '' && isset($enabled[$currency])) {
            return $currency;
        }
    }

    return $settings['default'] ?? 'eur';
}

function currency_exchange_rates(?string $baseCurrency = null): array
{
    $baseCurrency = normalize_currency($baseCurrency) ?: default_currency();
    $cacheKey = 'currency_rates_' . $baseCurrency;
    $cached = get_setting($cacheKey);
    $shouldRefresh = true;
    $rates = [];

    if ($cached) {
        try {
            $decoded = json_decode($cached, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded) && isset($decoded['rates'], $decoded['updated_at'])) {
                $rates = $decoded['rates'];
                $updatedAt = strtotime((string) $decoded['updated_at']);
                if ($updatedAt && (time() - $updatedAt) < 43200) { // 12 hours
                    $shouldRefresh = false;
                }
            }
        } catch (JsonException $exception) {
            $rates = [];
        }
    }

    if (!$shouldRefresh) {
        return is_array($rates) ? $rates : [];
    }

    $secretKey = trim((string) (get_setting('stripe_secret_key') ?? ''));

    if ($secretKey === '') {
        return is_array($rates) ? $rates : [];
    }

    try {
        $params = ['limit' => 100];
        $endpoint = '/v1/exchange_rates';
        $normalizedBase = strtolower($baseCurrency);
        $freshRates = [];
        $attempts = 0;
        $startingAfter = null;

        do {
            if ($startingAfter !== null) {
                $params['starting_after'] = $startingAfter;
            } elseif (isset($params['starting_after'])) {
                unset($params['starting_after']);
            }

            $response = stripe_request($secretKey, $endpoint, $params);
            $data = $response['data'] ?? [];

            if (is_array($data) && $data) {
                foreach ($data as $entry) {
                    if (!is_array($entry)) {
                        continue;
                    }

                    $entryBase = normalize_currency($entry['base'] ?? '');
                    if ($entryBase !== $normalizedBase) {
                        continue;
                    }

                    $code = normalize_currency($entry['currency'] ?? '');
                    $value = $entry['rate'] ?? null;

                    if ($code === '' || !is_numeric($value)) {
                        continue;
                    }

                    $freshRates[$code] = (float) $value;
                }

                $lastEntry = end($data);
                $startingAfter = is_array($lastEntry) ? ($lastEntry['id'] ?? null) : null;
            } else {
                $startingAfter = null;
            }

            $hasMore = !empty($response['has_more']);
            $attempts++;
        } while ($hasMore && $startingAfter !== null && $attempts < 10);

        if ($freshRates) {
            $freshRates[$normalizedBase] = 1.0;
            $rates = $freshRates;
            $payload = json_encode([
                'base' => $baseCurrency,
                'rates' => $freshRates,
                'updated_at' => gmdate(DateTimeInterface::RFC3339),
            ], JSON_THROW_ON_ERROR);

            set_setting($cacheKey, $payload);
        }
    } catch (Throwable $exception) {
        // Ignore and fall back to cached rates if available
    }

    return is_array($rates) ? $rates : [];
}

function currency_minor_unit(string $currency): int
{
    $currency = normalize_currency($currency);

    if ($currency === '') {
        return 2;
    }

    static $zeroDecimalCurrencies = [
        'bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf',
    ];

    return in_array($currency, $zeroDecimalCurrencies, true) ? 0 : 2;
}

function convert_currency_amount(int $amountCents, string $fromCurrency, string $toCurrency, ?array $rates = null): int
{
    $fromCurrency = normalize_currency($fromCurrency);
    $toCurrency = normalize_currency($toCurrency);

    if ($fromCurrency === '' || $toCurrency === '' || $amountCents <= 0) {
        return max(0, $amountCents);
    }

    if ($fromCurrency === $toCurrency) {
        return max(0, $amountCents);
    }

    if ($rates === null) {
        $rates = currency_exchange_rates($fromCurrency);
    }

    $rate = $rates[$toCurrency] ?? null;

    if (!is_numeric($rate) || (float) $rate <= 0) {
        return max(0, $amountCents);
    }

    $fromMinorUnit = currency_minor_unit($fromCurrency);
    $toMinorUnit = currency_minor_unit($toCurrency);
    $fromDivisor = (float) pow(10, $fromMinorUnit);
    $toMultiplier = (float) pow(10, $toMinorUnit);

    $amountMajor = $amountCents / $fromDivisor;
    $convertedMajor = $amountMajor * (float) $rate;
    $roundedMajor = max(1.0, ceil($convertedMajor - 1e-9));
    $roundedAmount = (int) round($roundedMajor * $toMultiplier);

    return $roundedAmount > 0 ? $roundedAmount : max(0, $amountCents);
}

function format_currency_amount(int $amountCents, string $currency): string
{
    $currency = normalize_currency($currency) ?: 'eur';
    $symbol = currency_symbol($currency);
    $minorUnit = currency_minor_unit($currency);
    $divisor = (float) pow(10, $minorUnit);
    $value = $amountCents / ($divisor > 0 ? $divisor : 1);
    $formatted = number_format($value, $minorUnit, '.', ' ');

    if ($minorUnit > 0) {
        $formatted = rtrim(rtrim($formatted, '0'), '.');
    }

    return $symbol . $formatted;
}

function get_broadcast_notifications(): array
{
    global $pdo;

    try {
        $statement = $pdo->query('SELECT id, translations, link_url FROM broadcast_notifications ORDER BY id DESC');
    } catch (Throwable $exception) {
        return [];
    }

    $rows = $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : [];
    $notifications = [];

    foreach ($rows as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : 0;

        if ($id <= 0) {
            continue;
        }

        $decodedTranslations = [];
        $rawTranslations = $row['translations'] ?? '';

        if (is_string($rawTranslations) && $rawTranslations !== '') {
            try {
                $parsed = json_decode($rawTranslations, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($parsed)) {
                    $decodedTranslations = $parsed;
                }
            } catch (JsonException $exception) {
                $decodedTranslations = [];
            }
        }

        if (!is_array($decodedTranslations)) {
            $decodedTranslations = [];
        }

        $linkUrl = '';

        if (isset($row['link_url']) && is_string($row['link_url'])) {
            $linkUrl = trim($row['link_url']);
        }

        $notifications[] = [
            'id' => $id,
            'translations' => $decodedTranslations,
            'link_url' => $linkUrl,
        ];
    }

    return $notifications;
}

function distribution_dashboard_provider(): string
{
    $stored = get_setting('distribution_dashboard_provider');
    $provider = is_string($stored) ? strtolower(trim($stored)) : '';

    return $provider !== '' ? $provider : 'sonosuite';
}

function sonosuite_base_url(): string
{
    $stored = get_setting('sonosuite_base_url');
    $value = is_string($stored) ? trim($stored) : '';

    if ($value === '') {
        $value = SONOSUITE_BASE_URL;
    }

    return $value;
}

function sonosuite_shared_secret(): string
{
    $stored = get_setting('sonosuite_shared_secret');
    $value = is_string($stored) ? trim($stored) : '';

    if ($value === '') {
        $value = SONOSUITE_SHARED_SECRET;
    }

    return $value;
}

function require_authentication(?string $redirectTo = null): void
{
    $user = current_user();

    if ($user) {
        if (isset($user['is_blocked']) && (int) $user['is_blocked'] === 1) {
            handle_blocked_access();
        }

        return;
    }

    $target = $redirectTo ?? ($_SERVER['REQUEST_URI'] ?? '/dashboard.php');

    if ($target === '') {
        $target = '/dashboard.php';
    }

    if (!preg_match('/^\//', $target)) {
        $target = '/' . ltrim($target, '/');
    }

    header('Location: /login.php?redirect=' . urlencode($target));
    exit;
}

function is_json_request(): bool
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }

    if (!empty($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return true;
    }

    return false;
}

function handle_blocked_access(): void
{
    $message = (string) __('alerts.blocked_access');
    $_SESSION['blocked_notice'] = $message;
    unset($_SESSION['user_id']);
    unset(
        $_SESSION['impersonator_id'],
        $_SESSION['impersonator_name'],
        $_SESSION['impersonator_email']
    );
    session_regenerate_id(true);

    if (is_json_request()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'reason' => 'blocked',
            'message' => $message
        ], JSON_THROW_ON_ERROR);
    } else {
        header('Location: /blocked.php');
    }

    exit;
}

function send_verification_email(string $email, string $token, string $firstName, ?string $language = null): void
{
    $verificationLink = app_url('verify.php?token=' . urlencode($token));
    $locale = $language ? normalize_language($language) : current_language();
    $siteName = site_name();
    $subject = (string) __('email.verification.subject', ['site' => $siteName], $locale);
    $trimmedName = trim($firstName);
    $greeting = $trimmedName === ''
        ? (string) __('email.common.greeting_generic', [], $locale)
        : (string) __('email.common.greeting', ['name' => $trimmedName], $locale);
    $bodyLines = [
        __('email.verification.intro', ['site' => $siteName], $locale),
        __('email.verification.action', ['link' => $verificationLink], $locale),
        __('email.verification.footer', ['site' => $siteName], $locale),
    ];
    $message = $greeting . "\n\n" . implode("\n\n", $bodyLines);

    $headers = 'From: ' . EMAIL_FROM . "\r\n" .
        'Reply-To: ' . SUPPORT_EMAIL . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $mailSent = @mail($email, $subject, $message, $headers);

    if (!$mailSent) {
        $logMessage = sprintf(
            '[%s] Verification email not sent to %s.%sLink: %s',
            (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339),
            $email,
            PHP_EOL,
            $verificationLink
        );

        file_put_contents(base_path('storage/email.log'), $logMessage . PHP_EOL, FILE_APPEND);
    }
}

function send_password_reset_email(string $email, string $firstName, string $token, ?string $language = null): void
{
    $resetLink = app_url('reset-password.php?token=' . urlencode($token) . '&email=' . urlencode($email));
    $locale = $language ? normalize_language($language) : current_language();
    $siteName = site_name();
    $subject = (string) __('email.reset.subject', ['site' => $siteName], $locale);
    $trimmedName = trim($firstName);
    $greeting = $trimmedName === ''
        ? (string) __('email.common.greeting_generic', [], $locale)
        : (string) __('email.common.greeting', ['name' => $trimmedName], $locale);
    $message = $greeting . "\n\n" .
        __('email.reset.intro', ['site' => $siteName], $locale) . "\n" .
        __('email.reset.action', ['link' => $resetLink], $locale) . "\n\n" .
        __('email.reset.expiration', [], $locale) . "\n\n" .
        __('email.common.signature', ['site' => $siteName], $locale);

    $headers = 'From: ' . EMAIL_FROM . "\r\n" .
        'Reply-To: ' . SUPPORT_EMAIL . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $mailSent = @mail($email, $subject, $message, $headers);

    if (!$mailSent) {
        $logMessage = sprintf(
            '[%s] Password reset email not sent to %s.%sLink: %s',
            (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339),
            $email,
            PHP_EOL,
            $resetLink
        );

        file_put_contents(base_path('storage/email.log'), $logMessage . PHP_EOL, FILE_APPEND);
    }
}

function sanitize(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function get_flashes(): array
{
    if (empty($_SESSION['flash'])) {
        return [];
    }

    $messages = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $messages;
}

function render_flash_notifications(array $flashMessages): string
{
    $notifications = [];

    foreach ($flashMessages as $type => $messages) {
        if (!is_array($messages)) {
            continue;
        }

        foreach ($messages as $message) {
            if (!is_string($message) || trim($message) === '') {
                continue;
            }

            $notifications[] = [
                'type' => (string) $type,
                'message' => $message,
            ];
        }
    }

    if ($notifications === []) {
        return '';
    }

    $encoded = json_encode($notifications, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false) {
        return '';
    }

    static $stylesRendered = false;

    ob_start();

    if (!$stylesRendered) {
        $stylesRendered = true;
        ?>
        <style data-flash-toast-styles>
          .flash-toast-container {
            position: fixed;
            top: clamp(1rem, 2vw, 1.5rem);
            right: clamp(1rem, 3vw, 1.75rem);
            width: min(320px, calc(100vw - 2rem));
            z-index: 9999;
            pointer-events: none;
          }

          .flash-toast-stack {
            display: grid;
            gap: 0.6rem;
          }

          .flash-toast {
            pointer-events: auto;
            border-radius: 16px;
            padding: 0.9rem 1.1rem;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #f8fafc;
            box-shadow: 0 22px 44px rgba(2, 6, 23, 0.45);
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.36s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.32s ease;
            position: relative;
          }

          .flash-toast.is-visible {
            transform: translateX(0);
            opacity: 1;
          }

          .flash-toast.is-leaving {
            transform: translateX(110%);
            opacity: 0;
          }

          .flash-toast__accent {
            flex-shrink: 0;
            width: 6px;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(129, 140, 248, 0.9), rgba(236, 72, 153, 0.9));
            align-self: stretch;
          }

          .flash-toast__message {
            flex: 1 1 auto;
            font-size: 0.92rem;
            line-height: 1.45;
          }

          .flash-toast__dismiss {
            position: absolute;
            top: 0.45rem;
            right: 0.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border: none;
            border-radius: 999px;
            background: transparent;
            color: rgba(248, 250, 252, 0.7);
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
          }

          .flash-toast__dismiss:hover,
          .flash-toast__dismiss:focus-visible {
            background: rgba(148, 163, 184, 0.18);
            color: #f8fafc;
            outline: none;
          }

          .flash-toast--success {
            border-color: rgba(34, 197, 94, 0.45);
            background: rgba(15, 23, 42, 0.95);
          }

          .flash-toast--success .flash-toast__accent {
            background: linear-gradient(180deg, rgba(34, 197, 94, 0.9), rgba(16, 185, 129, 0.9));
          }

          .flash-toast--error {
            border-color: rgba(248, 113, 113, 0.45);
          }

          .flash-toast--error .flash-toast__accent {
            background: linear-gradient(180deg, rgba(248, 113, 113, 0.9), rgba(239, 68, 68, 0.9));
          }

          @media (max-width: 640px) {
            .flash-toast-container {
              left: clamp(0.75rem, 4vw, 1rem);
              right: clamp(0.75rem, 4vw, 1rem);
              width: auto;
            }

            .flash-toast {
              border-radius: 14px;
            }
          }
        </style>
        <?php
    }

    ?>
    <div class="flash-toast-container" data-flash-toasts="<?= htmlspecialchars($encoded, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
      <div class="flash-toast-stack" role="status" aria-live="polite"></div>
    </div>
    <script>
      (function () {
        const script = document.currentScript;
        if (!script) {
          return;
        }

        const host = script.previousElementSibling;
        if (!(host instanceof HTMLElement) || !host.hasAttribute('data-flash-toasts')) {
          return;
        }

        let entries;
        try {
          entries = JSON.parse(host.getAttribute('data-flash-toasts') || '[]');
        } catch (error) {
          console.error('Unable to parse flash notifications', error);
          host.remove();
          return;
        }

        if (!Array.isArray(entries) || entries.length === 0) {
          host.remove();
          return;
        }

        const stack = host.querySelector('.flash-toast-stack');
        if (!(stack instanceof HTMLElement)) {
          host.remove();
          return;
        }

        const removeToast = (toast) => {
          if (!(toast instanceof HTMLElement)) {
            return;
          }

          toast.classList.add('is-leaving');
          const handleTransitionEnd = () => {
            toast.removeEventListener('transitionend', handleTransitionEnd);
            toast.remove();
            if (!stack.children.length) {
              host.remove();
            }
          };

          toast.addEventListener('transitionend', handleTransitionEnd);

          window.setTimeout(() => {
            toast.removeEventListener('transitionend', handleTransitionEnd);
            toast.remove();
            if (!stack.children.length) {
              host.remove();
            }
          }, 600);
        };

        entries.forEach((entry, index) => {
          if (!entry || typeof entry.message !== 'string') {
            return;
          }

          const toast = document.createElement('div');
          const type = typeof entry.type === 'string' ? entry.type.toLowerCase() : '';
          toast.className = 'flash-toast' + (type ? ` flash-toast--${type}` : '');
          toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
          toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

          const accent = document.createElement('span');
          accent.className = 'flash-toast__accent';
          toast.appendChild(accent);

          const message = document.createElement('span');
          message.className = 'flash-toast__message';
          message.textContent = entry.message;
          toast.appendChild(message);

          const dismiss = document.createElement('button');
          dismiss.type = 'button';
          dismiss.className = 'flash-toast__dismiss';
          dismiss.setAttribute('aria-label', 'Dismiss notification');
          dismiss.innerHTML = '&times;';
          toast.appendChild(dismiss);

          stack.appendChild(toast);

          window.requestAnimationFrame(() => {
            toast.classList.add('is-visible');
          });

          let hideTimeout = window.setTimeout(() => removeToast(toast), 4800 + index * 200);

          const clearHideTimeout = () => {
            window.clearTimeout(hideTimeout);
          };

          const restartHideTimeout = (duration) => {
            clearHideTimeout();
            hideTimeout = window.setTimeout(() => removeToast(toast), duration);
          };

          toast.addEventListener('mouseenter', () => {
            clearHideTimeout();
          });

          toast.addEventListener('mouseleave', () => {
            restartHideTimeout(2600);
          });

          const dismissToast = () => {
            clearHideTimeout();
            removeToast(toast);
          };

          toast.addEventListener('click', dismissToast);

          dismiss.addEventListener('click', (event) => {
            event.stopPropagation();
            dismissToast();
          });
        });
      })();
    </script>
    <?php

    return trim((string) ob_get_clean());
}

function sanitize_sonosuite_return_to(?string $url): ?string
{
    if ($url === null) {
        return null;
    }

    $trimmed = trim($url);

    if ($trimmed === '') {
        return null;
    }

    $parsed = parse_url($trimmed);

    if ($parsed === false) {
        return null;
    }

    $scheme = strtolower($parsed['scheme'] ?? '');

    if ($scheme !== 'https') {
        return null;
    }

    $baseUrl = sonosuite_base_url();
    if ($baseUrl === '') {
        return null;
    }

    $allowedHost = parse_url($baseUrl, PHP_URL_HOST);

    if (!$allowedHost) {
        return null;
    }

    $host = strtolower($parsed['host'] ?? '');

    if ($host !== strtolower($allowedHost)) {
        return null;
    }

    $path = $parsed['path'] ?? '/';

    if ($path === '') {
        $path = '/';
    }

    $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
    $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

    return sprintf('https://%s%s%s%s', $host, $path, $query, $fragment);
}

function client_ip(): string
{
    $candidates = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ];

    foreach ($candidates as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $value = $_SERVER[$key];
        $parts = explode(',', $value);
        foreach ($parts as $part) {
            $ip = trim($part);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

function analyze_user_agent(?string $userAgent): array
{
    $value = trim((string) $userAgent);

    if ($value === '') {
        return [
            'device_type' => 'Unknown',
            'os_name' => 'Unknown',
            'browser_name' => 'Unknown',
        ];
    }

    $ua = strtolower($value);
    $deviceType = 'Desktop';

    if (preg_match('/(bot|crawl|spider|slurp|bingpreview)/', $ua)) {
        $deviceType = 'Bot';
    } elseif (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false || strpos($ua, 'nexus 7') !== false) {
        $deviceType = 'Tablet';
    } elseif (strpos($ua, 'mobile') !== false || strpos($ua, 'iphone') !== false || strpos($ua, 'android') !== false || strpos($ua, 'windows phone') !== false) {
        $deviceType = 'Mobile';
    }

    $osName = 'Unknown';

    if (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false || strpos($ua, 'ipod') !== false) {
        $osName = 'iOS';
    } elseif (strpos($ua, 'android') !== false) {
        $osName = 'Android';
    } elseif (strpos($ua, 'windows nt 10') !== false || strpos($ua, 'windows nt 11') !== false) {
        $osName = 'Windows 10/11';
    } elseif (strpos($ua, 'windows nt 6.3') !== false) {
        $osName = 'Windows 8.1';
    } elseif (strpos($ua, 'windows nt 6.2') !== false) {
        $osName = 'Windows 8';
    } elseif (strpos($ua, 'windows nt 6.1') !== false) {
        $osName = 'Windows 7';
    } elseif (strpos($ua, 'mac os x') !== false || strpos($ua, 'macintosh') !== false) {
        $osName = 'macOS';
    } elseif (strpos($ua, 'cros') !== false) {
        $osName = 'ChromeOS';
    } elseif (strpos($ua, 'linux') !== false) {
        $osName = 'Linux';
    }

    $browserName = 'Unknown';

    if (strpos($ua, 'edg/') !== false) {
        $browserName = 'Microsoft Edge';
    } elseif (strpos($ua, 'opr/') !== false || strpos($ua, 'opera') !== false) {
        $browserName = 'Opera';
    } elseif (strpos($ua, 'samsungbrowser') !== false) {
        $browserName = 'Samsung Internet';
    } elseif (strpos($ua, 'brave') !== false) {
        $browserName = 'Brave';
    } elseif (strpos($ua, 'vivaldi') !== false) {
        $browserName = 'Vivaldi';
    } elseif (strpos($ua, 'chrome/') !== false && strpos($ua, 'chromium') === false && strpos($ua, 'edg/') === false && strpos($ua, 'opr/') === false) {
        $browserName = 'Chrome';
    } elseif ((strpos($ua, 'safari/') !== false || strpos($ua, 'version/') !== false) && strpos($ua, 'chrome/') === false && strpos($ua, 'opr/') === false && strpos($ua, 'chromium') === false) {
        $browserName = 'Safari';
    } elseif (strpos($ua, 'firefox/') !== false || strpos($ua, 'fxios') !== false) {
        $browserName = 'Firefox';
    } elseif (strpos($ua, 'chromium') !== false) {
        $browserName = 'Chromium';
    } elseif (strpos($ua, 'msie') !== false || strpos($ua, 'trident/') !== false) {
        $browserName = 'Internet Explorer';
    }

    return [
        'device_type' => ucfirst($deviceType),
        'os_name' => $osName,
        'browser_name' => $browserName,
    ];
}

function record_login_event(int $userId, string $ipAddress, ?string $userAgent = null): void
{
    global $pdo;

    $ip = trim($ipAddress);
    $normalizedIp = $ip === '' ? null : $ip;
    $agent = $userAgent !== null ? trim($userAgent) : null;
    $details = analyze_user_agent($agent);
    $timestamp = (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339);

    $insert = $pdo->prepare('INSERT INTO user_login_events (user_id, ip_address, user_agent, device_type, os_name, browser_name, created_at) VALUES (:user_id, :ip_address, :user_agent, :device_type, :os_name, :browser_name, :created_at)');
    $insert->execute([
        ':user_id' => $userId,
        ':ip_address' => $normalizedIp,
        ':user_agent' => $agent,
        ':device_type' => $details['device_type'] ?? 'Unknown',
        ':os_name' => $details['os_name'] ?? 'Unknown',
        ':browser_name' => $details['browser_name'] ?? 'Unknown',
        ':created_at' => $timestamp,
    ]);
}
