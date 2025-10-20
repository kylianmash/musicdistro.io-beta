<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();

$countries = require __DIR__ . '/data-countries.php';
$roles = __d('auth.roles') ?: [];
$languages = available_languages();

$values = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'country' => 'FR',
    'role' => 'artist',
    'language' => current_language(),
];
$errors = [];
$singleLanguageAvailable = count($languages) <= 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['first_name'] = trim($_POST['first_name'] ?? '');
    $values['last_name'] = trim($_POST['last_name'] ?? '');
    $values['email'] = trim($_POST['email'] ?? '');
    $values['country'] = strtoupper(trim($_POST['country'] ?? ''));
    $values['role'] = $_POST['role'] ?? 'artist';
    $values['language'] = normalize_language($_POST['language'] ?? current_language());
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';

    if ($values['first_name'] === '') {
        $errors['first_name'] = (string) __('validation.first_name_required');
    }

    if ($values['last_name'] === '') {
        $errors['last_name'] = (string) __('validation.last_name_required');
    }

    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = (string) __('validation.email_invalid');
    }

    if (!isset($countries[$values['country']])) {
        $errors['country'] = (string) __('validation.country_required');
    }

    if (!array_key_exists($values['role'], $roles)) {
        $errors['role'] = (string) __('validation.role_required');
    }

    if (!array_key_exists($values['language'], $languages)) {
        $errors['language'] = (string) __('validation.language_invalid');
    }

    if ($password === '' || strlen($password) < 8) {
        $errors['password'] = (string) __('validation.password_min');
    } elseif ($password !== $confirmPassword) {
        $errors['password_confirmation'] = (string) __('validation.password_confirmation');
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => strtolower($values['email'])]);
        if ($stmt->fetch()) {
            $errors['email'] = (string) __('validation.email_exists');
        }
    }

    if (!$errors) {
        $token = bin2hex(random_bytes(32));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $createdIp = client_ip();
        $isSuperAdmin = strcasecmp($values['email'], 'kylianmash@me.com') === 0 ? 1 : 0;

        $stmt = $pdo->prepare('INSERT INTO users (email, password, first_name, last_name, country, role, language, is_verified, verification_token, created_at, created_ip, last_login_ip, last_login_at, is_super_admin) VALUES (:email, :password, :first_name, :last_name, :country, :role, :language, 0, :token, :created_at, :created_ip, :last_login_ip, :last_login_at, :is_super_admin)');
        $stmt->execute([
            ':email' => strtolower($values['email']),
            ':password' => $hash,
            ':first_name' => $values['first_name'],
            ':last_name' => $values['last_name'],
            ':country' => $values['country'],
            ':role' => $values['role'],
            ':language' => $values['language'],
            ':token' => $token,
            ':created_at' => (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339),
            ':created_ip' => $createdIp,
            ':last_login_ip' => null,
            ':last_login_at' => null,
            ':is_super_admin' => $isSuperAdmin,
        ]);

        send_verification_email($values['email'], $token, $values['first_name'], $values['language']);
        flash('success', (string) __('auth.register.success'));
        header('Location: /login.php');
        exit;
    }
}

$normalizedInitialLanguage = normalize_language($values['language'] ?? null);
if (array_key_exists($normalizedInitialLanguage, $languages)) {
    $values['language'] = $normalizedInitialLanguage;
} elseif ($languages) {
    $values['language'] = array_key_first($languages);
}

$languageFieldDirection = language_direction($values['language'] ?? null);

$flashMessages = get_flashes();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= __e('auth.register.title') ?> – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <meta name="robots" content="index,follow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <script src="/assets/enhanced-select.js" defer></script>
  <style>
    :root {
      color-scheme: dark;
      --bg: radial-gradient(circle at top, rgba(168, 85, 247, 0.18), transparent 55%),
             radial-gradient(circle at bottom, rgba(14, 165, 233, 0.18), transparent 45%),
             #04050f;
      --surface: rgba(15, 23, 42, 0.82);
      --border: rgba(148, 163, 184, 0.18);
      --text: #f8fafc;
      --muted: rgba(226, 232, 240, 0.75);
      --accent: linear-gradient(135deg, #8b5cf6, #ec4899, #14b8a6);
      --radius: 28px;
      --shadow: 0 30px 60px rgba(2, 6, 23, 0.55);
      --select-surface: rgba(15, 23, 42, 0.78);
      --select-border: rgba(148, 163, 184, 0.3);
      --select-hover-border: rgba(129, 140, 248, 0.55);
      --select-focus-ring: rgba(99, 102, 241, 0.3);
      --select-arrow: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 18 18' fill='none' stroke='%23cbd5f5' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4.5 7l4.5 4 4.5-4'/%3E%3C/svg%3E");
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Manrope', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(2rem, 4vw, 5rem) 1.5rem;
      color: var(--text);
    }

    .wrapper {
      width: min(960px, 100%);
      background: rgba(6, 12, 31, 0.78);
      border: 1px solid var(--border);
      border-radius: clamp(26px, 3vw, 36px);
      box-shadow: var(--shadow);
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      overflow: hidden;
    }

    .intro {
      position: relative;
      padding: clamp(2.4rem, 4vw, 4rem);
      background: radial-gradient(circle at top left, rgba(139, 92, 246, 0.28), transparent 60%),
                  radial-gradient(circle at bottom right, rgba(20, 184, 166, 0.22), transparent 55%),
                  rgba(10, 17, 40, 0.85);
    }

    .intro h1 {
      font-size: clamp(2rem, 3vw, 2.7rem);
      line-height: 1.12;
      margin-bottom: 1rem;
    }

    .intro p {
      margin: 0 0 1.6rem;
      color: var(--muted);
      font-size: 1rem;
    }

    .intro ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      gap: 0.9rem;
    }

    .intro li {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      font-size: 0.98rem;
    }

    .intro li span.icon {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2.25rem;
      height: 2.25rem;
      flex-shrink: 0;
      font-weight: 700;
      font-size: 1.15rem;
      color: rgba(248, 250, 252, 0.96);
      z-index: 0;
    }

    .intro li span.icon::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(236, 72, 153, 0.28), rgba(14, 165, 233, 0.32));
      border: 1px solid rgba(148, 163, 184, 0.18);
      box-shadow: 0 8px 18px rgba(8, 47, 73, 0.35);
      z-index: -1;
    }

    form {
      padding: clamp(2.2rem, 4vw, 4rem);
      display: grid;
      gap: 1.2rem;
    }

    .field {
      display: grid;
      gap: 0.55rem;
    }

    label {
      font-size: 0.95rem;
      color: rgba(226, 232, 240, 0.85);
      font-weight: 600;
    }

    input,
    select {
      width: 100%;
      padding: 0.9rem 1rem;
      border-radius: 16px;
      border: 1px solid var(--select-border);
      background: rgba(15, 23, 42, 0.75);
      color: var(--text);
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease, background-position 0.4s ease;
    }

    select {
      appearance: none;
      background-color: var(--select-surface);
      background-image: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(236, 72, 153, 0.14)), var(--select-arrow);
      background-repeat: no-repeat, no-repeat;
      background-size: 260% 260%, 1.1rem;
      background-position: left center, calc(100% - 1.1rem) center;
      padding-right: 2.9rem;
      cursor: pointer;
    }

    select:hover {
      border-color: var(--select-hover-border);
      background-position: center center, calc(100% - 1.1rem) center;
    }

    input:focus,
    select:focus {
      outline: none;
      border-color: var(--select-hover-border);
      box-shadow: 0 0 0 4px var(--select-focus-ring);
    }

    select option {
      background: rgba(15, 23, 42, 0.95);
      color: var(--text);
    }

    select::-ms-expand {
      display: none;
    }

    .enhanced-select {
      position: relative;
    }

    .enhanced-select select {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      pointer-events: none;
    }

    .enhanced-select__trigger {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      padding: 0.9rem 1rem;
      border-radius: 16px;
      border: 1px solid var(--select-border);
      background-color: var(--select-surface);
      background-image: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(236, 72, 153, 0.14)), var(--select-arrow);
      background-repeat: no-repeat, no-repeat;
      background-size: 260% 260%, 1.1rem;
      background-position: left center, calc(100% - 1.1rem) center;
      color: var(--text);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: border 0.2s ease, box-shadow 0.2s ease, background-position 0.4s ease, transform 0.2s ease;
    }

    .enhanced-select__trigger:hover,
    .enhanced-select__trigger:focus-visible {
      border-color: var(--select-hover-border);
      background-position: center center, calc(100% - 1.1rem) center;
    }

    .enhanced-select__trigger:focus-visible {
      outline: none;
      box-shadow: 0 0 0 4px var(--select-focus-ring);
    }

    .enhanced-select.is-open .enhanced-select__trigger {
      border-color: var(--select-hover-border);
      background-position: center center, calc(100% - 1.1rem) center;
      box-shadow: 0 0 0 4px var(--select-focus-ring);
    }

    .enhanced-select__value {
      flex: 1;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .enhanced-select__menu {
      list-style: none;
      margin: 0;
      padding: 0.45rem 0;
      position: absolute;
      top: calc(100% + 0.45rem);
      left: 0;
      width: 100%;
      max-height: 260px;
      overflow-y: auto;
      overscroll-behavior: contain;
      touch-action: pan-y;
      -webkit-overflow-scrolling: touch;
      border-radius: 18px;
      border: 1px solid rgba(148, 163, 184, 0.25);
      background: rgba(7, 12, 28, 0.95);
      box-shadow: 0 28px 60px rgba(2, 6, 23, 0.55);
      backdrop-filter: blur(18px);
      display: none;
      z-index: 20;
    }

    .enhanced-select.is-open .enhanced-select__menu {
      display: block;
      animation: selectFadeIn 0.18s ease;
    }

    [dir='rtl'] .enhanced-select__trigger {
      background-position: right center, 1.1rem center;
      padding-right: 1.2rem;
      padding-left: 3.2rem;
    }

    [dir='rtl'] .enhanced-select.is-open .enhanced-select__trigger,
    [dir='rtl'] .enhanced-select__trigger:hover,
    [dir='rtl'] .enhanced-select__trigger:focus-visible {
      background-position: center center, 1.1rem center;
    }

    [dir='rtl'] .enhanced-select__value {
      text-align: right;
    }

    [dir='rtl'] .enhanced-select__menu {
      right: 0;
      left: auto;
    }

    .enhanced-select__option {
      padding: 0.7rem 1.1rem;
      cursor: pointer;
      font-size: 0.95rem;
      color: var(--text);
      display: flex;
      align-items: center;
    }

    .enhanced-select__option[aria-selected="true"] {
      font-weight: 600;
      color: #e0e7ff;
    }

    .enhanced-select__option.is-disabled {
      opacity: 0.45;
      cursor: not-allowed;
    }

    .enhanced-select__option.is-active,
    .enhanced-select__option:hover {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(236, 72, 153, 0.18));
    }

    @keyframes selectFadeIn {
      from {
        opacity: 0;
        transform: translateY(-6px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .error {
      color: #fca5a5;
      font-size: 0.85rem;
    }

    button {
      border: none;
      border-radius: 18px;
      padding: 1rem 1.4rem;
      font-size: 1.05rem;
      font-weight: 700;
      background: var(--accent);
      color: white;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 16px 35px rgba(124, 58, 237, 0.35);
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 20px 45px rgba(124, 58, 237, 0.4);
    }

    .footer {
      text-align: center;
      font-size: 0.92rem;
      color: var(--muted);
    }

    .footer a {
      color: #f0abfc;
      text-decoration: none;
      font-weight: 600;
    }

  </style>
</head>
<body>
  <div class="wrapper">
    <aside class="intro">
      <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width: clamp(180px, 45vw, 240px); height: auto; margin-bottom: 1.8rem;">
      <h1><?= __e('auth.register.intro_title', ['site' => $siteName]) ?></h1>
      <p><?= __e('auth.register.intro_text') ?></p>
      <ul>
        <li><span class="icon">⚡</span><?= __e('auth.register.bullets.native_ai') ?></li>
        <li><span class="icon">🌍</span><?= __e('auth.register.bullets.worldwide') ?></li>
        <li><span class="icon">💎</span><?= __e('auth.register.bullets.royalties') ?></li>
      </ul>
    </aside>
    <form method="post" novalidate>
      <h2 style="margin:0;font-size:1.6rem;"><?= __e('auth.register.title') ?></h2>
      <p style="margin:0;color:var(--muted);font-size:0.95rem;"><?= __e('auth.register.lead') ?></p>
      <div class="field">
        <label for="first_name"><?= __e('auth.common.first_name_label') ?></label>
        <input type="text" id="first_name" name="first_name" value="<?= sanitize($values['first_name']) ?>" autocomplete="given-name" required>
        <?php if (isset($errors['first_name'])): ?><p class="error"><?= sanitize($errors['first_name']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="last_name"><?= __e('auth.common.last_name_label') ?></label>
        <input type="text" id="last_name" name="last_name" value="<?= sanitize($values['last_name']) ?>" autocomplete="family-name" required>
        <?php if (isset($errors['last_name'])): ?><p class="error"><?= sanitize($errors['last_name']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="email"><?= __e('auth.common.email_label') ?></label>
        <input type="email" id="email" name="email" value="<?= sanitize($values['email']) ?>" autocomplete="email" required>
        <?php if (isset($errors['email'])): ?><p class="error"><?= sanitize($errors['email']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="country"><?= __e('auth.common.country_label') ?></label>
        <select id="country" name="country" required data-enhanced-select>
          <?php foreach ($countries as $code => $label): ?>
            <option value="<?= sanitize($code) ?>" <?= $values['country'] === $code ? 'selected' : '' ?>><?= sanitize($label) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['country'])): ?><p class="error"><?= sanitize($errors['country']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="role"><?= __e('auth.common.role_label') ?></label>
        <select id="role" name="role" required data-enhanced-select>
          <?php foreach ($roles as $key => $label): ?>
            <option value="<?= sanitize($key) ?>" <?= $values['role'] === $key ? 'selected' : '' ?>><?= sanitize($label) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['role'])): ?><p class="error"><?= sanitize($errors['role']) ?></p><?php endif; ?>
      </div>

      <?php if ($singleLanguageAvailable): ?>
        <input type="hidden" name="language" value="<?= sanitize($values['language']) ?>">
      <?php else: ?>
        <div class="field">
          <label for="language"><?= __e('auth.common.language_label') ?></label>
          <select
            id="language"
            name="language"
            required
            dir="<?= htmlspecialchars($languageFieldDirection, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            lang="<?= htmlspecialchars($values['language'] ?? current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            data-enhanced-select
          >
            <?php foreach ($languages as $code => $info): ?>
              <?php $direction = strtolower((string) ($info['direction'] ?? 'ltr')) === 'rtl' ? 'rtl' : 'ltr'; ?>
              <option
                value="<?= sanitize($code) ?>"
                <?= $values['language'] === $code ? 'selected' : '' ?>
                dir="<?= sanitize($direction) ?>"
                lang="<?= sanitize($code) ?>"
              ><?= sanitize($info['native']) ?></option>
            <?php endforeach; ?>
          </select>
          <p class="help" style="margin:0.35rem 0 0;font-size:0.85rem;color:var(--muted);"><?= __e('auth.register.language_help') ?></p>
          <?php if (isset($errors['language'])): ?><p class="error"><?= sanitize($errors['language']) ?></p><?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="field">
        <label for="password"><?= __e('auth.common.password_label') ?></label>
        <input type="password" id="password" name="password" required minlength="8">
        <?php if (isset($errors['password'])): ?><p class="error"><?= sanitize($errors['password']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="password_confirmation"><?= __e('auth.common.confirm_password_label') ?></label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
        <?php if (isset($errors['password_confirmation'])): ?><p class="error"><?= sanitize($errors['password_confirmation']) ?></p><?php endif; ?>
      </div>

      <button type="submit"><?= __e('auth.register.submit') ?></button>
      <?php $loginLink = '<a href="/login.php">' . __e('auth.register.login_link') . '</a>'; ?>
      <p class="footer"><?= __('auth.register.login_prompt', ['link' => $loginLink]) ?></p>
    </form>
  </div>
  <?= render_flash_notifications($flashMessages) ?>
</body>
</html>
