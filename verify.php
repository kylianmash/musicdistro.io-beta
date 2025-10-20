<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();

$token = $_GET['token'] ?? '';
$token = is_string($token) ? trim($token) : '';
$success = false;

if ($token !== '') {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE verification_token = :token');
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        $pdo->prepare('UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = :id')->execute([':id' => $user['id']]);
        $success = true;
    }
}

if ($success) {
    flash('success', (string) __('auth.verify.success'));
    header('Location: /login.php');
    exit;
}

?><!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= __e('auth.verify.expired_title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: radial-gradient(circle at 10% 20%, rgba(236, 72, 153, 0.22), transparent 50%),
                  radial-gradient(circle at 90% 30%, rgba(129, 140, 248, 0.18), transparent 55%),
                  #020617;
      font-family: 'Manrope', sans-serif;
      color: #f8fafc;
      padding: 1.5rem;
      text-align: center;
    }

    .card {
      max-width: 520px;
      background: rgba(2, 6, 23, 0.82);
      border-radius: 28px;
      padding: 2.5rem;
      border: 1px solid rgba(148, 163, 184, 0.22);
      box-shadow: 0 30px 60px rgba(2, 6, 23, 0.55);
    }

    h1 {
      margin-top: 0;
      font-size: clamp(2rem, 3vw, 2.4rem);
    }

    p {
      color: rgba(226, 232, 240, 0.75);
      font-size: 0.98rem;
      margin-bottom: 2rem;
    }

    a {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: linear-gradient(135deg, #6366f1, #ec4899);
      color: white;
      padding: 0.95rem 1.6rem;
      border-radius: 18px;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 18px 36px rgba(79, 70, 229, 0.35);
    }
  </style>
</head>
<body>
  <div class="card">
    <h1><?= __e('auth.verify.expired_title') ?></h1>
    <?php $supportLink = '<a href="mailto:' . SUPPORT_EMAIL . '" style="color:#f9a8d4;">' . sanitize(SUPPORT_EMAIL) . '</a>'; ?>
    <p><?= __('auth.verify.expired_body', ['email' => $supportLink]) ?></p>
    <a href="/login.php"><?= __e('auth.verify.cta_login') ?></a>
  </div>
</body>
</html>
