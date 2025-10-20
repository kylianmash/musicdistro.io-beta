<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$errors = [];
$email = '';

$returnToParam = $_GET['return_to'] ?? $_POST['return_to'] ?? null;
$sonosuiteReturnTo = sanitize_sonosuite_return_to(is_string($returnToParam) ? $returnToParam : null);

$redirectTarget = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

if ($redirectTarget === '' && $sonosuiteReturnTo !== null) {
    $redirectTarget = '/generate-token/?return_to=' . rawurlencode($sonosuiteReturnTo);
}

if ($redirectTarget === '') {
    $redirectTarget = '/dashboard.php';
}

$redirectTarget = (function (string $target): string {
    if ($target === '') {
        return '/dashboard.php';
    }

    if (preg_match('/^https?:\/\//i', $target)) {
        return '/dashboard.php';
    }

    $parsed = parse_url($target);

    if ($parsed === false) {
        return '/dashboard.php';
    }

    $path = $parsed['path'] ?? '/dashboard.php';

    if ($path === '') {
        $path = '/dashboard.php';
    }

    if ($path[0] !== '/') {
        $path = '/' . ltrim($path, '/');
    }

    $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

    return $path . $query;
})($redirectTarget);

if (current_user()) {
    header('Location: ' . $redirectTarget);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = (string) __('validation.email_invalid');
    }

    if ($password === '') {
        $errors['password'] = (string) __('validation.password_required');
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => strtolower($email)]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors['email'] = (string) __('validation.credentials_invalid');
        } elseif (isset($user['is_blocked']) && (int) $user['is_blocked'] === 1) {
            handle_blocked_access();
        } elseif (!(int) $user['is_verified']) {
            $errors['email'] = (string) __('validation.email_unverified');
        } else {
            $now = (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339);
            $ip = client_ip();
            $update = $pdo->prepare('UPDATE users SET last_login_at = :last_login_at, last_login_ip = :last_login_ip WHERE id = :id');
            $update->execute([
                ':last_login_at' => $now,
                ':last_login_ip' => $ip,
                ':id' => $user['id'],
            ]);
            try {
                record_login_event((int) $user['id'], $ip, $_SERVER['HTTP_USER_AGENT'] ?? null);
            } catch (Throwable $exception) {
                error_log('Failed to record login event: ' . $exception->getMessage());
            }
            if (!empty($user['language'])) {
                set_current_language($user['language']);
            }
            $_SESSION['user_id'] = (int) $user['id'];
            header('Location: ' . $redirectTarget);
            exit;
        }
    }
}

$flashMessages = get_flashes();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= __e('auth.login.title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <style>
    :root {
      color-scheme: dark;
      --gradient: radial-gradient(circle at top left, rgba(59, 130, 246, 0.22), transparent 58%),
                   radial-gradient(circle at bottom right, rgba(244, 114, 182, 0.2), transparent 52%),
                   #030712;
      --surface: rgba(15, 23, 42, 0.82);
      --border: rgba(148, 163, 184, 0.16);
      --text: #f8fafc;
      --muted: rgba(203, 213, 225, 0.8);
      --primary: linear-gradient(135deg, #6366f1, #ec4899);
      --shadow: 0 30px 60px rgba(15, 23, 42, 0.45);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(2rem, 5vw, 6rem) 1.5rem;
      background: var(--gradient);
      font-family: 'Manrope', sans-serif;
      color: var(--text);
    }

    .card {
      width: min(460px, 100%);
      background: rgba(7, 13, 29, 0.82);
      border-radius: clamp(26px, 3vw, 34px);
      border: 1px solid var(--border);
      padding: clamp(2.5rem, 5vw, 3.5rem);
      box-shadow: var(--shadow);
      display: grid;
      gap: 1.5rem;
    }

    .brand {
      display: grid;
      gap: 1.4rem;
      justify-items: center;
      text-align: center;
    }

    .brand img {
      width: clamp(180px, 45vw, 240px);
      height: auto;
    }

    .brand-copy {
      display: grid;
      gap: 0.75rem;
    }

    .brand-copy h1 {
      margin: 0;
      font-size: clamp(2rem, 3vw, 2.3rem);
    }

    p.lead {
      margin: 0;
      color: var(--muted);
      font-size: 0.95rem;
    }

    form {
      display: grid;
      gap: 1.2rem;
    }

    label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      color: rgba(226, 232, 240, 0.85);
      margin-bottom: 0.4rem;
    }

    input {
      width: 100%;
      padding: 0.95rem 1rem;
      border-radius: 16px;
      border: 1px solid rgba(148, 163, 184, 0.25);
      background: rgba(15, 23, 42, 0.75);
      color: var(--text);
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    input:focus {
      outline: none;
      border-color: rgba(99, 102, 241, 0.8);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
    }

    .error {
      color: #fca5a5;
      font-size: 0.85rem;
    }

    button {
      width: 100%;
      border: none;
      border-radius: 18px;
      padding: 1rem 1.3rem;
      font-size: 1.05rem;
      font-weight: 700;
      background: var(--primary);
      color: white;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 18px 35px rgba(79, 70, 229, 0.35);
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 24px 48px rgba(236, 72, 153, 0.35);
    }

    .footer {
      text-align: center;
      font-size: 0.9rem;
      color: var(--muted);
    }

    .footer a {
      color: #a855f7;
      text-decoration: none;
      font-weight: 600;
    }

    .help-links {
      display: flex;
      justify-content: flex-end;
      margin-top: -0.4rem;
      margin-bottom: 0.8rem;
    }

    .help-links a {
      font-size: 0.9rem;
      color: #a855f7;
      text-decoration: none;
      font-weight: 600;
    }

  </style>
</head>
<body>
  <div class="card">
    <div class="brand">
      <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
      <div class="brand-copy">
        <h1><?= __e('auth.login.title') ?></h1>
        <p class="lead"><?= __e('auth.login.lead') ?></p>
      </div>
    </div>

    <form method="post" novalidate>
      <input type="hidden" name="redirect" value="<?= sanitize($redirectTarget) ?>">
      <input type="hidden" name="return_to" value="<?= sanitize($sonosuiteReturnTo ?? '') ?>">
      <div>
        <label for="email"><?= __e('auth.common.email_label') ?></label>
        <input type="email" id="email" name="email" value="<?= sanitize($email) ?>" autocomplete="email" required>
        <?php if (isset($errors['email'])): ?><p class="error"><?= sanitize($errors['email']) ?></p><?php endif; ?>
      </div>

      <div>
        <label for="password"><?= __e('auth.common.password_label') ?></label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($errors['password'])): ?><p class="error"><?= sanitize($errors['password']) ?></p><?php endif; ?>
      </div>

      <div class="help-links">
        <a href="/forgot-password.php"><?= __e('auth.login.forgot') ?></a>
      </div>

      <button type="submit"><?= __e('auth.login.submit') ?></button>
    </form>

    <?php $registerLink = '<a href="/register.php">' . __e('auth.login.register_link') . '</a>'; ?>
    <p class="footer"><?= __('auth.login.register_prompt', ['link' => $registerLink]) ?></p>
  </div>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
