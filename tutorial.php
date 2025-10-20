<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$currentUser = current_user();
$languageSwitcherHtml = render_language_switcher('tutorial');
$flashMessages = get_flashes();
$tutorialTranslations = __d('tutorial');
$title = (string) ($tutorialTranslations['title'] ?? ('Tutorial – ' . $siteName));
$headerTranslations = $tutorialTranslations['header'] ?? [];
$headerTitle = (string) ($headerTranslations['title'] ?? '');
$headerSubtitle = (string) ($headerTranslations['subtitle'] ?? '');
$steps = $tutorialTranslations['steps'] ?? [];
$ctaTranslations = $tutorialTranslations['cta'] ?? [];
$ctaLabel = (string) ($ctaTranslations['label'] ?? __('home.nav.cta.dashboard'));
$ctaHref = '/dashboard.php';
if (!$currentUser) {
    $ctaHref = '/register.php';
    $ctaLabel = (string) ($ctaTranslations['guest_label'] ?? __('home.nav.cta.register'));
}
$metaDescription = $headerSubtitle !== '' ? $headerSubtitle : __('home.meta.description');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars((string) $metaDescription, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <style>
    :root {
      color-scheme: dark;
      --bg: radial-gradient(circle at top, rgba(99, 102, 241, 0.14), transparent 55%),
             radial-gradient(circle at bottom, rgba(236, 72, 153, 0.16), transparent 50%),
             #020617;
      --surface: rgba(15, 23, 42, 0.82);
      --border: rgba(148, 163, 184, 0.18);
      --muted: rgba(203, 213, 225, 0.82);
      --text: #f8fafc;
      --accent: linear-gradient(135deg, #8b5cf6, #ec4899);
      --shadow: 0 24px 48px rgba(2, 6, 23, 0.45);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: 'Manrope', sans-serif;
      line-height: 1.6;
      padding-bottom: 4rem;
    }

    a { color: inherit; text-decoration: none; }

    header {
      padding: clamp(2.5rem, 5vw, 4rem) clamp(1.5rem, 6vw, 6rem);
      text-align: center;
      display: grid;
      gap: 1.5rem;
    }

    .header__top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .brand img {
      width: clamp(140px, 20vw, 200px);
      height: auto;
    }

    .header__content {
      display: grid;
      gap: 1rem;
      justify-items: center;
    }

    .header__content h1 {
      margin: 0;
      font-size: clamp(2.4rem, 4vw, 3rem);
    }

    .header__content p {
      margin: 0;
      color: var(--muted);
      max-width: 780px;
      font-size: 1.05rem;
    }

    .language-switcher {
      position: relative;
      display: inline-flex;
      align-items: center;
    }

    .language-switcher select {
      appearance: none;
      background: rgba(15, 23, 42, 0.72);
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 999px;
      padding: 0.45rem 1.9rem 0.45rem 0.9rem;
      color: inherit;
      font: inherit;
      cursor: pointer;
      box-shadow: 0 12px 28px rgba(2, 6, 23, 0.28);
      transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .language-switcher select:focus {
      outline: none;
      border-color: rgba(99, 102, 241, 0.55);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
    }

    .language-switcher select:hover {
      border-color: rgba(99, 102, 241, 0.35);
      transform: translateY(-1px);
    }

    .language-switcher--tutorial::after {
      content: '';
      position: absolute;
      right: 0.85rem;
      top: 50%;
      transform: translateY(-50%);
      border: 0.35rem solid transparent;
      border-top-color: rgba(148, 163, 184, 0.75);
      pointer-events: none;
    }

    main {
      width: min(960px, 92vw);
      margin: 0 auto;
      display: grid;
      gap: 1.8rem;
    }

    .step {
      background: var(--surface);
      border-radius: clamp(24px, 4vw, 32px);
      border: 1px solid var(--border);
      padding: clamp(1.8rem, 4vw, 2.6rem);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    .step::before {
      content: attr(data-step);
      position: absolute;
      top: clamp(1.6rem, 3vw, 2rem);
      right: clamp(1.6rem, 3vw, 2rem);
      font-size: clamp(3rem, 5vw, 4.5rem);
      color: rgba(148, 163, 184, 0.1);
      font-weight: 800;
    }

    .step h2 {
      margin: 0 0 1rem;
      font-size: 1.5rem;
    }

    .step p,
    .step ul {
      margin: 0 0 1rem;
      color: var(--muted);
      font-size: 1rem;
    }

    .step ul {
      padding-left: 1.2rem;
    }

    .cta {
      text-align: center;
      margin-top: 2.4rem;
    }

    .cta a {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      padding: 1rem 1.8rem;
      border-radius: 18px;
      background: var(--accent);
      color: white;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 20px 40px rgba(139, 92, 246, 0.35);
      transition: transform 0.2s ease;
    }

    .cta a:hover {
      transform: translateY(-2px);
    }

    @media (max-width: 720px) {
      .header__top {
        flex-direction: column;
        justify-content: center;
      }

      .header__language {
        width: 100%;
        display: flex;
        justify-content: center;
      }

      .language-switcher select {
        width: 100%;
      }

      .step::before {
        top: 1.2rem;
        right: 1.2rem;
        font-size: clamp(2.4rem, 12vw, 3.5rem);
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header__top">
      <a class="brand" href="/" aria-label="<?= __e('home.nav.brand_aria') ?>">
        <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= __e('dashboard.brand_alt', ['site' => $siteName]) ?>">
      </a>
      <?php if ($languageSwitcherHtml !== ''): ?>
        <div class="header__language">
          <?= $languageSwitcherHtml ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="header__content">
      <h1><?= htmlspecialchars($headerTitle !== '' ? $headerTitle : $title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
      <?php if ($headerSubtitle !== ''): ?>
        <p><?= htmlspecialchars($headerSubtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
      <?php endif; ?>
    </div>
  </header>
  <main>
    <?php foreach ($steps as $index => $step): ?>
      <?php
        $stepTitle = (string) ($step['title'] ?? '');
        $stepDescription = (string) ($step['description'] ?? '');
        $bullets = isset($step['bullets']) && is_array($step['bullets']) ? $step['bullets'] : [];
        $stepNumber = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
      ?>
      <article class="step" data-step="<?= sanitize($stepNumber) ?>">
        <?php if ($stepTitle !== ''): ?>
          <h2><?= sanitize($stepTitle) ?></h2>
        <?php endif; ?>
        <?php if ($stepDescription !== ''): ?>
          <p><?= sanitize($stepDescription) ?></p>
        <?php endif; ?>
        <?php if ($bullets): ?>
          <ul>
            <?php foreach ($bullets as $bullet): ?>
              <li><?= sanitize((string) $bullet) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </main>
  <div class="cta">
    <a href="<?= htmlspecialchars($ctaHref, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= sanitize($ctaLabel) ?></a>
  </div>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
