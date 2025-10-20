<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$message = $_SESSION['blocked_notice'] ?? (string) __('alerts.blocked_access');
unset($_SESSION['blocked_notice']);
$flashMessages = get_flashes();

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= __e('auth.blocked.title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <style>
    :root {
      color-scheme: dark;
      --bg: radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 55%),
             radial-gradient(circle at bottom right, rgba(236, 72, 153, 0.2), transparent 55%),
             #020617;
      --surface: rgba(15, 23, 42, 0.85);
      --border: rgba(148, 163, 184, 0.18);
      --text: #f8fafc;
      --muted: rgba(203, 213, 225, 0.78);
      --accent: linear-gradient(135deg, #6366f1, #ec4899);
      --shadow: 0 24px 44px rgba(2, 6, 23, 0.55);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(2.5rem, 6vw, 5rem) 1.5rem;
      background: var(--bg);
      font-family: 'Manrope', sans-serif;
      color: var(--text);
    }

    .card {
      width: min(520px, 100%);
      background: var(--surface);
      border-radius: clamp(28px, 4vw, 36px);
      border: 1px solid var(--border);
      padding: clamp(2.75rem, 6vw, 3.5rem);
      display: grid;
      gap: 1.5rem;
      text-align: center;
      box-shadow: var(--shadow);
    }

    .card img {
      width: clamp(180px, 40vw, 240px);
      justify-self: center;
    }

    h1 {
      margin: 0;
      font-size: clamp(1.9rem, 3.4vw, 2.4rem);
    }

    p {
      margin: 0;
      font-size: 1rem;
      color: var(--muted);
      line-height: 1.6;
    }

    .actions {
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .actions a {
      padding: 0.85rem 1.6rem;
      border-radius: 999px;
      background: var(--accent);
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 18px 32px rgba(99, 102, 241, 0.35);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .actions a:hover {
      transform: translateY(-1px);
      box-shadow: 0 22px 36px rgba(99, 102, 241, 0.45);
    }

    .support {
      font-size: 0.9rem;
      color: rgba(226, 232, 240, 0.72);
    }

    .support a {
      color: inherit;
      font-weight: 600;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <main class="card" role="main">
    <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <h1><?= __e('auth.blocked.title') ?></h1>
    <p><?= sanitize($message) ?></p>
    <div class="actions">
      <a href="/login.php"><?= __e('auth.blocked.cta_login') ?></a>
    </div>
    <?php $supportLink = '<a href="mailto:' . SUPPORT_EMAIL . '">' . sanitize(SUPPORT_EMAIL) . '</a>'; ?>
    <p class="support"><?= __('auth.blocked.lead', ['email' => $supportLink]) ?></p>
  </main>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
