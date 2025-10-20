<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();

/**
 * Retrieve the requested slug from the URI or query parameters.
 */
function resolve_requested_slug(): string
{
    $slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
    if ($slug !== '') {
        return strtolower($slug);
    }

    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($requestUri, PHP_URL_PATH);
    if (is_string($path)) {
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $index = array_search('musiclink', $segments, true);
        if ($index !== false && isset($segments[$index + 1])) {
            return strtolower($segments[$index + 1]);
        }
    }

    return '';
}

/**
 * Load stored smartlink data by slug.
 *
 * @return array<string, mixed>|null
 */
function load_smartlink(string $slug): ?array
{
    if ($slug === '') {
        return null;
    }

    $storagePath = __DIR__ . '/../storage/smartlinks.json';
    if (!is_file($storagePath)) {
        return null;
    }

    $contents = file_get_contents($storagePath);
    if ($contents === false || $contents === '') {
        return null;
    }

    $data = json_decode($contents, true);
    if (!is_array($data) || !isset($data[$slug]) || !is_array($data[$slug])) {
        return null;
    }

    return $data[$slug];
}

/**
 * Generate initials for the artwork placeholder.
 */
function smartlink_initials(string $title, string $slug): string
{
    $source = $title !== '' ? $title : $slug;
    $source = preg_replace('/[^A-Za-z0-9]+/', ' ', $source);
    $source = trim((string) $source);
    if ($source === '') {
        return 'MD';
    }
    $words = explode(' ', $source);
    $initials = '';
    foreach ($words as $word) {
        if ($word === '') {
            continue;
        }
        $initials .= strtoupper($word[0]);
        if (strlen($initials) >= 2) {
            break;
        }
    }

    return $initials !== '' ? $initials : 'MD';
}

$slug = resolve_requested_slug();
$smartlink = $slug !== '' ? load_smartlink($slug) : null;
if ($smartlink === null) {
    http_response_code(404);
}

$lang = current_language();
$direction = language_direction($lang);
$title = '';
$artist = '';
$upc = '';
$artwork = '';
$platforms = [];
$shareUrl = rtrim(APP_URL, '/') . '/musiclink/' . ($slug !== '' ? $slug : '');
$createdAt = '';

if ($smartlink !== null) {
    $title = isset($smartlink['title']) ? (string) $smartlink['title'] : '';
    $artist = isset($smartlink['artist']) ? (string) $smartlink['artist'] : '';
    $upc = isset($smartlink['upc']) ? (string) $smartlink['upc'] : '';
    $artwork = isset($smartlink['artwork']) ? (string) $smartlink['artwork'] : '';
    $createdAt = isset($smartlink['created_at']) ? (string) $smartlink['created_at'] : '';
    if (isset($smartlink['platforms']) && is_array($smartlink['platforms'])) {
        foreach ($smartlink['platforms'] as $platform) {
            if (!is_array($platform) || empty($platform['url'])) {
                continue;
            }
            $platforms[] = [
                'id' => (string) ($platform['id'] ?? ''),
                'label' => (string) ($platform['label'] ?? ''),
                'logo' => (string) ($platform['logo'] ?? ''),
                'color' => (string) ($platform['color'] ?? '#6366f1'),
                'url' => (string) $platform['url'],
                'source' => (string) ($platform['source'] ?? ''),
            ];
        }
    }
}

$displayTitle = $smartlink !== null
    ? ($title !== '' ? $title : 'Release ' . ($upc !== '' ? $upc : $slug))
    : 'Smartlink unavailable';
$subtitleParts = [];
if ($artist !== '') {
    $subtitleParts[] = $artist;
}
if ($upc !== '') {
    $subtitleParts[] = 'UPC ' . $upc;
}
$displaySubtitle = implode(' • ', $subtitleParts);
$metaDescription = $smartlink !== null
    ? ($displaySubtitle !== ''
        ? 'Listen to ' . $displayTitle . ' • ' . $displaySubtitle
        : 'Listen to ' . $displayTitle . ' on your preferred streaming service.')
    : 'The requested smartlink could not be found.';
$initials = smartlink_initials($displayTitle, $slug);
$formattedDate = '';
if ($createdAt !== '') {
    try {
        $date = new DateTimeImmutable($createdAt);
        $formattedDate = $date->format('F j, Y');
    } catch (Throwable $exception) {
        $formattedDate = '';
    }
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

?><!DOCTYPE html>
<html lang="<?= esc($lang) ?>" dir="<?= esc($direction) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($displayTitle) ?> • <?= esc($siteName) ?></title>
  <meta name="description" content="<?= esc($metaDescription) ?>">
  <link rel="icon" href="<?= esc($faviconUrl) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <meta property="og:site_name" content="<?= esc($siteName) ?>">
  <meta property="og:title" content="<?= esc($displayTitle) ?>">
  <meta property="og:description" content="<?= esc($metaDescription) ?>">
  <meta property="og:url" content="<?= esc($shareUrl) ?>">
  <?php if ($artwork !== ''): ?>
    <meta property="og:image" content="<?= esc($artwork) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= esc($artwork) ?>">
  <?php else: ?>
    <meta name="twitter:card" content="summary">
  <?php endif; ?>
  <style>
    :root {
      color-scheme: dark;
      --bg: radial-gradient(circle at 20% 10%, rgba(96, 165, 250, 0.18), transparent 55%),
             radial-gradient(circle at 80% 10%, rgba(129, 140, 248, 0.18), transparent 50%),
             radial-gradient(circle at 50% 90%, rgba(244, 114, 182, 0.18), transparent 50%),
             #020617;
      --card: rgba(15, 23, 42, 0.78);
      --border: rgba(148, 163, 184, 0.18);
      --muted: rgba(203, 213, 225, 0.82);
      --text: #f8fafc;
      --accent: linear-gradient(135deg, #6366f1, #ec4899);
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(2rem, 6vw, 4rem) clamp(1.5rem, 4vw, 3rem);
      background: var(--bg);
      font-family: 'Manrope', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      color: var(--text);
    }

    main {
      width: min(640px, 100%);
    }

    .smartlink-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1.6rem;
      padding: clamp(1.8rem, 4vw, 2.6rem);
      display: flex;
      flex-direction: column;
      gap: 1.8rem;
      box-shadow: 0 40px 80px rgba(2, 6, 23, 0.6);
      backdrop-filter: blur(20px);
    }

    .smartlink-header {
      display: flex;
      align-items: center;
      gap: 1.4rem;
    }

    .smartlink-cover {
      width: clamp(96px, 20vw, 128px);
      aspect-ratio: 1 / 1;
      border-radius: 1.1rem;
      background: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: clamp(1.8rem, 4vw, 2.2rem);
      overflow: hidden;
      position: relative;
      box-shadow: 0 30px 60px rgba(236, 72, 153, 0.28);
    }

    .smartlink-cover[data-has-cover="true"] {
      background: #0f172a;
      box-shadow: 0 30px 60px rgba(15, 23, 42, 0.45);
    }

    .smartlink-cover img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .smartlink-meta h1 {
      margin: 0;
      font-size: clamp(1.6rem, 4vw, 2rem);
      font-weight: 800;
    }

    .smartlink-meta p {
      margin: 0.35rem 0 0;
      color: var(--muted);
      font-size: 0.95rem;
      letter-spacing: 0.01em;
    }

    .smartlink-platforms {
      display: grid;
      gap: 0.95rem;
    }

    .smartlink-platforms a {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.2rem;
      padding: 1rem 1.2rem;
      border-radius: 1rem;
      text-decoration: none;
      color: inherit;
      font-weight: 600;
      background: rgba(15, 23, 42, 0.68);
      border: 1px solid rgba(148, 163, 184, 0.16);
      transition: transform 0.18s ease, border 0.18s ease, box-shadow 0.18s ease;
    }

    .smartlink-platforms a:hover,
    .smartlink-platforms a:focus-visible {
      transform: translateY(-2px);
      border-color: rgba(148, 163, 184, 0.32);
      box-shadow: 0 20px 42px rgba(2, 6, 23, 0.55);
      outline: none;
    }

    .smartlink-platforms a .smartlink-platform__label {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      min-width: 0;
    }

    .smartlink-platform__icon {
      width: 42px;
      height: 42px;
      border-radius: 1rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(148, 163, 184, 0.12);
      box-shadow: 0 18px 36px rgba(2, 6, 23, 0.45);
      overflow: hidden;
      flex-shrink: 0;
    }

    .smartlink-platform__icon img {
      width: 70%;
      height: 70%;
      object-fit: contain;
      filter: drop-shadow(0 2px 6px rgba(15, 23, 42, 0.45));
    }

    .smartlink-platform__dot {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      flex-shrink: 0;
      box-shadow: 0 0 0 6px rgba(148, 163, 184, 0.12);
    }

    .smartlink-platform__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    .smartlink-platform__cta {
      font-size: 0.82rem;
      opacity: 0.82;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .smartlink-footer {
      text-align: center;
      font-size: 0.85rem;
      color: rgba(148, 163, 184, 0.8);
    }

    .smartlink-footer a {
      color: inherit;
    }

    .smartlink-empty {
      text-align: center;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .smartlink-empty h2 {
      margin: 0;
      font-size: 1.5rem;
    }

    .smartlink-empty p {
      margin: 0;
      color: var(--muted);
    }

    .smartlink-share {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .smartlink-share input {
      flex: 1;
      padding: 0.65rem 0.85rem;
      border-radius: 0.6rem;
      border: 1px solid rgba(148, 163, 184, 0.3);
      background: rgba(15, 23, 42, 0.6);
      color: inherit;
      font-size: 0.9rem;
    }

    .smartlink-share button {
      padding: 0.6rem 1rem;
      border-radius: 0.6rem;
      border: none;
      font-weight: 600;
      cursor: pointer;
      background: var(--accent);
      color: #f8fafc;
    }

    @media (max-width: 640px) {
      body {
        padding: 1.5rem;
      }

      .smartlink-card {
        padding: 1.6rem;
        gap: 1.4rem;
      }

      .smartlink-header {
        flex-direction: column;
        text-align: center;
      }

      .smartlink-meta p {
        margin-top: 0.5rem;
      }

      .smartlink-share {
        flex-direction: column;
        align-items: stretch;
      }

      .smartlink-share button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <main>
    <article class="smartlink-card">
      <?php if ($smartlink !== null): ?>
        <header class="smartlink-header">
          <div class="smartlink-cover" data-has-cover="<?= $artwork !== '' ? 'true' : 'false' ?>">
            <?php if ($artwork !== ''): ?>
              <img src="<?= esc($artwork) ?>" alt="<?= esc($displayTitle) ?>">
            <?php else: ?>
              <?= esc($initials) ?>
            <?php endif; ?>
          </div>
          <div class="smartlink-meta">
            <h1><?= esc($displayTitle) ?></h1>
            <?php if ($displaySubtitle !== ''): ?>
              <p><?= esc($displaySubtitle) ?></p>
            <?php endif; ?>
            <?php if ($formattedDate !== ''): ?>
              <p>Published <?= esc($formattedDate) ?></p>
            <?php endif; ?>
          </div>
        </header>
        <section class="smartlink-platforms" aria-label="Available platforms">
          <?php if (!empty($platforms)): ?>
            <?php foreach ($platforms as $platform): ?>
              <a
                href="<?= esc($platform['url']) ?>"
                target="_blank"
                rel="noopener noreferrer"
                data-smartlink-platform
                data-platform-id="<?= esc($platform['id']) ?>"
                data-platform-label="<?= esc($platform['label'] !== '' ? $platform['label'] : ucfirst($platform['id'])) ?>"
              >
                <span class="smartlink-platform__label">
                  <?php if ($platform['logo'] !== ''): ?>
                    <span class="smartlink-platform__icon" aria-hidden="true">
                      <img src="<?= esc($platform['logo']) ?>" alt="">
                    </span>
                  <?php else: ?>
                    <span class="smartlink-platform__dot" style="background: <?= esc($platform['color']) ?>" aria-hidden="true"></span>
                  <?php endif; ?>
                  <span class="smartlink-platform__name"><?= esc($platform['label'] !== '' ? $platform['label'] : ucfirst($platform['id'])) ?></span>
                </span>
                <span class="smartlink-platform__cta">Listen</span>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="smartlink-empty">
              <h2>No platforms found</h2>
              <p>The streaming links for this release could not be loaded.</p>
            </div>
          <?php endif; ?>
        </section>
        <div class="smartlink-share">
          <input type="text" value="<?= esc($shareUrl) ?>" readonly aria-label="Share URL">
          <button type="button" data-copy-button>Copy</button>
        </div>
      <?php else: ?>
        <div class="smartlink-empty">
          <h2>Smartlink unavailable</h2>
          <p>The requested release could not be found. Please check the link or contact the artist.</p>
        </div>
      <?php endif; ?>
      <footer class="smartlink-footer">
        <p><a href="<?= esc(APP_URL) ?>"><?= esc($siteName) ?></a> • Smartlinks</p>
      </footer>
    </article>
  </main>
  <script>
    (function () {
      const hasSmartlink = <?= $smartlink !== null ? 'true' : 'false' ?>;
      const slug = <?= json_encode($slug) ?>;
      if (!hasSmartlink || !slug) {
        return;
      }

      const analyticsEndpoint = '/smartlink-analytics.php';
      const copyButton = document.querySelector('[data-copy-button]');
      const copyInput = copyButton && copyButton.previousElementSibling instanceof HTMLInputElement
        ? copyButton.previousElementSibling
        : null;

      function readCookie(name) {
        const value = document.cookie.split('; ').find((row) => row.startsWith(`${name}=`));
        return value ? decodeURIComponent(value.split('=')[1] || '') : '';
      }

      function writeCookie(name, value) {
        const secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = `${name}=${encodeURIComponent(value)}; Max-Age=${60 * 60 * 24 * 365 * 2}; Path=/; SameSite=Lax${secure}`;
      }

      function generateClientId() {
        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
          const bytes = new Uint8Array(16);
          window.crypto.getRandomValues(bytes);
          return Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');
        }
        return `${Math.random().toString(36).slice(2)}${Date.now().toString(36)}`;
      }

      function ensureClientId() {
        const cookieName = 'md_slid';
        let clientId = readCookie(cookieName);
        if (!clientId) {
          clientId = generateClientId();
          writeCookie(cookieName, clientId);
        }
        return clientId;
      }

      const clientId = ensureClientId();

      function detectCountry() {
        try {
          const locale = (Intl.DateTimeFormat && Intl.DateTimeFormat().resolvedOptions().locale)
            || navigator.language
            || '';
          if (!locale) {
            return '';
          }
          const match = locale.replace('_', '-').match(/[-_]([A-Za-z]{2})(?:-|$)/);
          return match ? match[1].toUpperCase() : '';
        } catch (error) {
          return '';
        }
      }

      const localeCountry = detectCountry();

      function sendAnalytics(event, extra = {}) {
        if (!slug) {
          return;
        }
        const payload = {
          slug,
          event,
          clientId,
        };
        if (localeCountry) {
          payload.country = localeCountry;
        }
        if (extra.platformId) {
          payload.platformId = extra.platformId;
        }
        if (extra.city) {
          payload.city = extra.city;
        }
        const body = JSON.stringify(payload);
        if (navigator.sendBeacon) {
          try {
            const blob = new Blob([body], { type: 'application/json' });
            navigator.sendBeacon(analyticsEndpoint, blob);
            return;
          } catch (error) {
            // Fallback to fetch.
          }
        }
        try {
          fetch(analyticsEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body,
            keepalive: true,
          }).catch(() => {
            // Ignore failures.
          });
        } catch (error) {
          // Ignore failures.
        }
      }

      window.addEventListener('load', () => {
        sendAnalytics('view');
      }, { once: true });

      document.querySelectorAll('[data-smartlink-platform]').forEach((link) => {
        link.addEventListener('click', () => {
          const platformId = link.getAttribute('data-platform-id') || '';
          sendAnalytics('click', { platformId });
        });
      });

      if (copyButton && copyInput) {
        copyButton.addEventListener('click', async () => {
          try {
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
              await navigator.clipboard.writeText(copyInput.value);
            } else {
              copyInput.select();
              document.execCommand('copy');
              copyInput.blur();
            }
            copyButton.textContent = 'Copied!';
            sendAnalytics('copy');
            setTimeout(() => {
              copyButton.textContent = 'Copy';
            }, 1800);
          } catch (error) {
            copyButton.textContent = 'Copy failed';
            setTimeout(() => {
              copyButton.textContent = 'Copy';
            }, 1800);
          }
        });
      }
    }());
  </script>
</body>
</html>
