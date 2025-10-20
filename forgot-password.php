<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = (string) __('validation.email_invalid');
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, first_name, email, language FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower($email)]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = (new DateTimeImmutable('now'))->modify('+1 hour');
            $update = $pdo->prepare('UPDATE users SET password_reset_token = :token, password_reset_expires_at = :expires WHERE id = :id');
            $update->execute([
                ':token' => $tokenHash,
                ':expires' => $expiresAt->format(DateTimeInterface::RFC3339),
                ':id' => $user['id'],
            ]);

            send_password_reset_email($user['email'], $user['first_name'], $token, $user['language'] ?? null);
        }

        flash('success', (string) __('auth.forgot.success'));
        header('Location: /forgot-password.php');
        exit;
    }
}

$flashMessages = get_flashes();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= __e('auth.forgot.title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
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

    input[type="email"] {
      width: 100%;
      padding: 0.95rem 1rem;
      border-radius: 16px;
      border: 1px solid rgba(148, 163, 184, 0.25);
      background: rgba(15, 23, 42, 0.75);
      color: var(--text);
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="email"]:focus {
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
      color: #a855f7;
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 600;
    }

  </style>
</head>
<body>
  <main class="card">
    <div class="brand">
      <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
      <div class="brand-copy">
        <h1><?= __e('auth.forgot.title') ?></h1>
        <p class="lead"><?= __e('auth.forgot.lead') ?></p>
      </div>
    </div>
    <form method="post" novalidate>
      <div>
        <label for="email"><?= __e('auth.common.email_label') ?></label>
        <input type="email" name="email" id="email" value="<?= sanitize($email) ?>" autocomplete="email" required>
        <?php if (isset($errors['email'])): ?>
          <p class="error" role="alert"><?= sanitize($errors['email']) ?></p>
        <?php endif; ?>
      </div>
      <button type="submit"><?= __e('auth.forgot.submit') ?></button>
    </form>

    <div class="links">
      <a href="/login.php"><?= __e('auth.forgot.back_to_login') ?></a>
      <a href="/register.php"><?= __e('auth.login.register_link') ?></a>
    </div>
  </main>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
