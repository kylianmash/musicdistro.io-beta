<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$rawToken = $_GET['token'] ?? $_POST['token'] ?? '';
$rawEmail = $_GET['email'] ?? $_POST['email'] ?? '';

$token = is_string($rawToken) ? trim($rawToken) : '';
$email = is_string($rawEmail) ? trim($rawEmail) : '';

$errors = [];
$tokenError = null;
$user = null;
$validToken = false;

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $token === '') {
    $tokenError = (string) __('auth.reset.token_invalid');
} else {
    $stmt = $pdo->prepare('SELECT id, first_name, password_reset_token, password_reset_expires_at FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => strtolower($email)]);
    $user = $stmt->fetch();

    if ($user && $user['password_reset_token'] && $user['password_reset_expires_at']) {
        $expectedHash = (string) $user['password_reset_token'];
        $providedHash = hash('sha256', $token);

        try {
            $expiresAt = new DateTimeImmutable($user['password_reset_expires_at']);
        } catch (Exception $e) {
            $expiresAt = false;
        }

        if ($expiresAt instanceof DateTimeImmutable && $expiresAt >= new DateTimeImmutable('now') && hash_equals($expectedHash, $providedHash)) {
            $validToken = true;
        } else {
            $tokenError = (string) __('auth.reset.token_expired');
        }
    } else {
        $tokenError = (string) __('auth.reset.token_invalid');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$validToken || !$user) {
        $tokenError = (string) __('auth.reset.token_used');
    } else {
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if ($password === '' || strlen($password) < 8) {
            $errors['password'] = (string) __('validation.password_min');
        }

        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = (string) __('validation.password_confirmation');
        }

        if (!$errors) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = :id');
            $update->execute([
                ':password' => $passwordHash,
                ':id' => $user['id'],
            ]);

            flash('success', (string) __('auth.reset.success'));
            header('Location: /login.php');
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
  <title><?= __e('auth.reset.title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
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
      width: min(480px, 100%);
      background: rgba(7, 13, 29, 0.82);
      border-radius: clamp(26px, 3vw, 34px);
      border: 1px solid var(--border);
      padding: clamp(2.5rem, 5vw, 3.5rem);
      box-shadow: var(--shadow);
      display: grid;
      gap: 1.6rem;
    }

    .brand {
      display: grid;
      gap: 1.2rem;
      justify-items: center;
      text-align: center;
    }

    .brand img {
      width: clamp(180px, 45vw, 220px);
      height: auto;
    }

    .brand h1 {
      margin: 0;
      font-size: clamp(2rem, 3vw, 2.3rem);
    }

    p.lead {
      margin: 0;
      color: var(--muted);
      font-size: 0.98rem;
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

    input[type="password"] {
      width: 100%;
      padding: 0.95rem 1rem;
      border-radius: 16px;
      border: 1px solid rgba(148, 163, 184, 0.25);
      background: rgba(15, 23, 42, 0.75);
      color: var(--text);
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="password"]:focus {
      outline: none;
      border-color: rgba(99, 102, 241, 0.8);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
    }

    .error {
      color: #fca5a5;
      font-size: 0.85rem;
      margin-top: 0.4rem;
    }

    button {
      border: none;
      border-radius: 16px;
      padding: 0.95rem 1rem;
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      background-image: var(--primary);
      cursor: pointer;
      transition: opacity 0.2s ease, transform 0.2s ease;
    }

    button:hover {
      opacity: 0.92;
      transform: translateY(-1px);
    }

    .links {
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      text-align: center;
    }

    .links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 0.92rem;
    }

    .flash {
      border-radius: 14px;
      padding: 0.85rem 1rem;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .flash.success { background: rgba(34, 197, 94, 0.14); border: 1px solid rgba(34, 197, 94, 0.35); }
    .flash.error { background: rgba(248, 113, 113, 0.14); border: 1px solid rgba(248, 113, 113, 0.35); }
  </style>
</head>
<body>
  <main class="card">
    <div class="brand">
      <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
      <div>
        <h1><?= __e('auth.reset.title') ?></h1>
        <p class="lead"><?= __e('auth.reset.lead') ?></p>
      </div>
    </div>

    <?php if ($tokenError): ?>
      <div class="flash error" role="alert">
        <?= sanitize($tokenError) ?>
      </div>
      <div class="links">
        <a href="/forgot-password.php"><?= __e('auth.reset.request_new_link') ?></a>
        <a href="/login.php"><?= __e('auth.reset.return_to_login') ?></a>
      </div>
    <?php elseif ($validToken): ?>
      <form method="post" novalidate>
        <input type="hidden" name="token" value="<?= sanitize($token) ?>">
        <input type="hidden" name="email" value="<?= sanitize($email) ?>">

        <div>
          <label for="password"><?= __e('auth.reset.new_password_label') ?></label>
          <input type="password" name="password" id="password" autocomplete="new-password" required>
          <?php if (isset($errors['password'])): ?>
            <p class="error" role="alert"><?= sanitize($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label for="password_confirmation"><?= __e('auth.reset.confirm_password_label') ?></label>
          <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required>
          <?php if (isset($errors['password_confirmation'])): ?>
            <p class="error" role="alert"><?= sanitize($errors['password_confirmation']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit"><?= __e('auth.reset.submit') ?></button>
      </form>

      <div class="links">
        <a href="/login.php"><?= __e('auth.reset.return_to_login') ?></a>
      </div>
    <?php endif; ?>
  </main>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
