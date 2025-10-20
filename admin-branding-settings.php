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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => __('validation.method_not_allowed'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$siteName = trim((string) ($_POST['site_name'] ?? ''));
$footerCopyright = trim((string) ($_POST['footer_copyright'] ?? ''));
$errors = [];

if ($siteName === '') {
    $errors['site_name'] = (string) __('dashboard.admin.design.branding.validation.site_name');
} elseif (mb_strlen($siteName) > 120) {
    $errors['site_name'] = (string) __('dashboard.admin.design.branding.validation.site_name_max');
}

if (mb_strlen($footerCopyright) > 160) {
    $errors['footer_copyright'] = (string) __('dashboard.admin.design.branding.validation.footer_copyright_max');
}

$logoUpload = $_FILES['dashboard_logo'] ?? null;
$faviconUpload = $_FILES['favicon'] ?? null;
$brandingDirectory = base_path('storage/branding');

if (!is_dir($brandingDirectory) && !mkdir($brandingDirectory, 0775, true) && !is_dir($brandingDirectory)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.design.branding.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$logoPath = trim((string) get_setting('dashboard_logo_path'));
$faviconPath = trim((string) get_setting('site_favicon_path'));
$logoStoredPath = null;
$faviconStoredPath = null;

$finfo = new finfo(FILEINFO_MIME_TYPE);

if ($logoUpload && ($logoUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($logoUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errors['dashboard_logo'] = (string) __('dashboard.admin.design.branding.validation.dashboard_logo');
    } elseif (($logoUpload['size'] ?? 0) > 4 * 1024 * 1024) {
        $errors['dashboard_logo'] = (string) __('dashboard.admin.design.branding.validation.dashboard_logo');
    } else {
        $logoMime = $finfo->file($logoUpload['tmp_name']) ?: '';
        $logoMap = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'text/plain' => 'svg',
            'text/xml' => 'svg',
            'application/xml' => 'svg',
        ];
        $logoExtension = $logoMap[$logoMime] ?? null;

        if ($logoExtension === 'svg' && !str_ends_with(strtolower((string) ($logoUpload['name'] ?? '')), '.svg')) {
            $logoExtension = null;
        }

        if ($logoExtension === null) {
            $errors['dashboard_logo'] = (string) __('dashboard.admin.design.branding.validation.dashboard_logo');
        } else {
            $logoFilename = sprintf('branding-logo-%s.%s', bin2hex(random_bytes(8)), $logoExtension);
            $logoDestination = $brandingDirectory . '/' . $logoFilename;

            if (!move_uploaded_file($logoUpload['tmp_name'], $logoDestination)) {
                $errors['dashboard_logo'] = (string) __('dashboard.admin.design.branding.validation.dashboard_logo');
            } else {
                if ($logoPath !== '' && str_starts_with($logoPath, '/storage/branding/')) {
                    $previousLogo = base_path(ltrim($logoPath, '/'));
                    if (is_file($previousLogo)) {
                        @unlink($previousLogo);
                    }
                }
                $logoStoredPath = '/storage/branding/' . $logoFilename;
            }
        }
    }
}

if ($faviconUpload && ($faviconUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($faviconUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errors['favicon'] = (string) __('dashboard.admin.design.branding.validation.favicon');
    } elseif (($faviconUpload['size'] ?? 0) > 1024 * 1024) {
        $errors['favicon'] = (string) __('dashboard.admin.design.branding.validation.favicon');
    } else {
        $faviconMime = $finfo->file($faviconUpload['tmp_name']) ?: '';
        $faviconMap = [
            'image/png' => 'png',
            'image/svg+xml' => 'svg',
            'image/x-icon' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'image/webp' => 'webp',
            'text/plain' => 'svg',
            'text/xml' => 'svg',
            'application/xml' => 'svg',
        ];
        $faviconExtension = $faviconMap[$faviconMime] ?? null;

        if ($faviconExtension === 'svg' && !str_ends_with(strtolower((string) ($faviconUpload['name'] ?? '')), '.svg')) {
            $faviconExtension = null;
        }

        if ($faviconExtension === null) {
            $errors['favicon'] = (string) __('dashboard.admin.design.branding.validation.favicon');
        } else {
            $faviconFilename = sprintf('branding-favicon-%s.%s', bin2hex(random_bytes(8)), $faviconExtension);
            $faviconDestination = $brandingDirectory . '/' . $faviconFilename;

            if (!move_uploaded_file($faviconUpload['tmp_name'], $faviconDestination)) {
                $errors['favicon'] = (string) __('dashboard.admin.design.branding.validation.favicon');
            } else {
                if ($faviconPath !== '' && str_starts_with($faviconPath, '/storage/branding/')) {
                    $previousFavicon = base_path(ltrim($faviconPath, '/'));
                    if (is_file($previousFavicon)) {
                        @unlink($previousFavicon);
                    }
                }
                $faviconStoredPath = '/storage/branding/' . $faviconFilename;
            }
        }
    }
}

if ($errors) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'errors' => $errors,
    ], JSON_THROW_ON_ERROR);
    exit;
}

try {
    set_setting('site_name', $siteName);
    if ($logoStoredPath !== null) {
        set_setting('dashboard_logo_path', $logoStoredPath);
    }
    if ($faviconStoredPath !== null) {
        set_setting('site_favicon_path', $faviconStoredPath);
    }
    set_setting('dashboard_footer_template', $footerCopyright !== '' ? $footerCopyright : null);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.design.branding.feedback.error'),
    ], JSON_THROW_ON_ERROR);
    exit;
}

$response = [
    'status' => 'success',
    'message' => __('dashboard.admin.design.branding.feedback.saved'),
    'site_name' => $siteName,
];

if ($logoStoredPath !== null) {
    $response['logo'] = $logoStoredPath;
} elseif ($logoPath !== '') {
    $response['logo'] = $logoPath;
}

if ($faviconStoredPath !== null) {
    $response['favicon'] = $faviconStoredPath;
} elseif ($faviconPath !== '') {
    $response['favicon'] = $faviconPath;
}

$response['footer_copyright'] = $footerCopyright;

echo json_encode($response, JSON_THROW_ON_ERROR);
