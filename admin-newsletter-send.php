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

$rawInput = file_get_contents('php://input');
$input = [];

if (is_string($rawInput) && $rawInput !== '') {
    try {
        $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($decoded)) {
            $input = $decoded;
        }
    } catch (Throwable $exception) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => __('validation.json_invalid'),
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}

if (!$input) {
    $input = $_POST;
}

$subject = isset($input['subject']) ? trim((string) $input['subject']) : '';
$htmlContent = isset($input['html']) ? (string) $input['html'] : '';
$textContent = isset($input['text']) ? (string) $input['text'] : '';
$senderName = isset($input['sender_name']) ? trim((string) $input['sender_name']) : '';
$senderEmail = isset($input['sender_email']) ? trim((string) $input['sender_email']) : '';
$replyToEmail = isset($input['reply_to']) ? trim((string) $input['reply_to']) : '';

if ($senderName === '') {
    $senderName = site_name();
}

$recipientMode = isset($input['recipient_mode']) && (string) $input['recipient_mode'] === 'selected'
    ? 'selected'
    : 'all';

$recipientIds = $input['recipients'] ?? [];
if (!is_array($recipientIds)) {
    $recipientIds = [];
}

$extraEmailsRaw = $input['extra_emails'] ?? [];
if (is_string($extraEmailsRaw)) {
    $extraEmails = preg_split('/[,;\n]+/', $extraEmailsRaw) ?: [];
} elseif (is_array($extraEmailsRaw)) {
    $extraEmails = $extraEmailsRaw;
} else {
    $extraEmails = [];
}

$transport = isset($input['transport']) && (string) $input['transport'] === 'smtp' ? 'smtp' : 'phpmail';

$batchSizeInput = isset($input['batch_size']) ? (int) $input['batch_size'] : 1;
$batchSize = $batchSizeInput > 0 ? $batchSizeInput : 1;

$intervalValueInput = isset($input['interval_value']) ? (int) $input['interval_value'] : 0;
if ($intervalValueInput < 0) {
    $intervalValueInput = 0;
}

$intervalUnit = isset($input['interval_unit']) ? (string) $input['interval_unit'] : 'seconds';
switch ($intervalUnit) {
    case 'hours':
        $intervalSeconds = $intervalValueInput * 3600;
        break;
    case 'minutes':
        $intervalSeconds = $intervalValueInput * 60;
        break;
    default:
        $intervalSeconds = $intervalValueInput;
        break;
}

if ($intervalSeconds < 0) {
    $intervalSeconds = 0;
}

$smtpInput = $input['smtp'] ?? [];
if (!is_array($smtpInput)) {
    $smtpInput = [];
}

$smtpHost = isset($smtpInput['host']) ? trim((string) $smtpInput['host']) : '';
$smtpPortInput = isset($smtpInput['port']) ? (int) $smtpInput['port'] : 0;
$smtpPort = $smtpPortInput > 0 ? $smtpPortInput : 0;
$smtpEncryption = isset($smtpInput['encryption']) ? strtolower(trim((string) $smtpInput['encryption'])) : 'tls';
if (!in_array($smtpEncryption, ['none', 'ssl', 'tls'], true)) {
    $smtpEncryption = 'tls';
}
$smtpUsername = isset($smtpInput['username']) ? trim((string) $smtpInput['username']) : '';
$smtpPassword = isset($smtpInput['password']) ? (string) $smtpInput['password'] : '';

$errors = [];

if ($subject === '') {
    $errors[] = __('dashboard.admin.newsletter.validation.subject');
}

$trimmedHtml = trim($htmlContent);
if ($trimmedHtml === '') {
    $errors[] = __('dashboard.admin.newsletter.validation.html');
}

if ($senderEmail === '' || filter_var($senderEmail, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = __('dashboard.admin.newsletter.validation.sender_email');
}

if ($replyToEmail !== '' && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = __('dashboard.admin.newsletter.validation.reply_to');
}

if ($recipientMode === 'selected') {
    $normalizedIds = [];
    foreach ($recipientIds as $value) {
        $id = (int) $value;
        if ($id > 0) {
            $normalizedIds[] = $id;
        }
    }
    $recipientIds = array_values(array_unique($normalizedIds));

    if (!$recipientIds && !$extraEmails) {
        $errors[] = __('dashboard.admin.newsletter.validation.recipients');
    }
}

if ($transport === 'smtp') {
    if ($smtpHost === '') {
        $errors[] = __('dashboard.admin.newsletter.validation.smtp_host');
    }
    if ($smtpPort <= 0) {
        $errors[] = __('dashboard.admin.newsletter.validation.smtp_port');
    }
}

if ($errors) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.newsletter.feedback.error'),
        'errors' => $errors,
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit;
}

$recipients = [];

$addRecipient = static function (array &$storage, string $email, string $name = ''): void {
    $normalized = strtolower(trim($email));
    if ($normalized === '' || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
        return;
    }

    if (!isset($storage[$normalized])) {
        $storage[$normalized] = [
            'email' => $normalized,
            'name' => trim($name),
        ];
    }
};

if ($recipientMode === 'selected' && $recipientIds) {
    $placeholders = implode(',', array_fill(0, count($recipientIds), '?'));
    $statement = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id IN ($placeholders)");
    $statement->execute($recipientIds);
} else {
    $statement = $pdo->query('SELECT email, first_name, last_name FROM users');
}

while ($statement && ($row = $statement->fetch())) {
    $email = isset($row['email']) ? trim((string) $row['email']) : '';
    if ($email === '') {
        continue;
    }

    $firstName = isset($row['first_name']) ? trim((string) $row['first_name']) : '';
    $lastName = isset($row['last_name']) ? trim((string) $row['last_name']) : '';
    $name = trim($firstName . ' ' . $lastName);

    $addRecipient($recipients, $email, $name);
}

foreach ($extraEmails as $extraEmail) {
    if (!is_string($extraEmail)) {
        continue;
    }
    $addRecipient($recipients, $extraEmail);
}

if (!$recipients) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => __('dashboard.admin.newsletter.validation.recipients'),
        'errors' => [__('dashboard.admin.newsletter.validation.recipients')],
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit;
}

$plainTextContent = $textContent !== ''
    ? $textContent
    : html_entity_decode(strip_tags($htmlContent), ENT_QUOTES | ENT_HTML5, 'UTF-8');

$smtpConfig = [
    'host' => $smtpHost,
    'port' => $smtpPort,
    'encryption' => $smtpEncryption,
    'username' => $smtpUsername,
    'password' => $smtpPassword,
];

$sentCount = 0;
$failedRecipients = [];
$recipientTotal = count($recipients);
$processedInBatch = 0;

set_time_limit(0);

foreach ($recipients as $index => $recipient) {
    try {
        $message = build_newsletter_message(
            $recipient,
            $subject,
            $htmlContent,
            $plainTextContent,
            $senderName,
            $senderEmail,
            $replyToEmail
        );
    } catch (Throwable $exception) {
        $failedRecipients[] = $recipient['email'];
        continue;
    }

    $success = false;

    try {
        if ($transport === 'smtp') {
            $success = send_newsletter_via_smtp($message, $smtpConfig);
        } else {
            $success = send_newsletter_via_phpmail($message);
        }
    } catch (Throwable $exception) {
        $success = false;
    }

    if ($success) {
        $sentCount++;
    } else {
        $failedRecipients[] = $recipient['email'];
    }

    $processedInBatch++;

    if ($batchSize > 0 && $intervalSeconds > 0 && $processedInBatch >= $batchSize && ($index + 1) < $recipientTotal) {
        $processedInBatch = 0;
        sleep($intervalSeconds);
    }
}

$status = 'success';
$message = __('dashboard.admin.newsletter.feedback.success', ['count' => $sentCount]);

if ($failedRecipients) {
    if ($sentCount > 0) {
        $status = 'partial';
        $message = __('dashboard.admin.newsletter.feedback.partial', [
            'sent' => $sentCount,
            'failed' => count($failedRecipients),
        ]);
    } else {
        $status = 'error';
        $message = __('dashboard.admin.newsletter.feedback.error');
    }
}

$logEntry = sprintf(
    '[%s] Newsletter by %s (%d) – subject "%s" – sent: %d, failed: %d',
    (new DateTimeImmutable('now'))->format(DateTimeInterface::RFC3339),
    isset($user['email']) ? (string) $user['email'] : 'unknown',
    isset($user['id']) ? (int) $user['id'] : 0,
    str_replace(["\r", "\n"], ' ', $subject),
    $sentCount,
    count($failedRecipients)
);

try {
    file_put_contents(base_path('storage/newsletter.log'), $logEntry . PHP_EOL, FILE_APPEND);
} catch (Throwable $exception) {
    // Ignore log errors
}

echo json_encode([
    'status' => $status,
    'message' => $message,
    'sent' => $sentCount,
    'failed' => count($failedRecipients),
    'failures' => $failedRecipients,
], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

function format_email_address(string $name, string $email): string
{
    $trimmedEmail = trim($email);
    $trimmedName = trim($name);

    if ($trimmedName === '') {
        return $trimmedEmail;
    }

    return mb_encode_mimeheader($trimmedName, 'UTF-8') . ' <' . $trimmedEmail . '>';
}

function normalize_line_endings(string $value): string
{
    return preg_replace('/\r\n|\r|\n/', "\r\n", $value);
}

/**
 * @param array{email: string, name?: string} $recipient
 * @return array{subject: string, mail_headers: string, smtp_headers: string, body: string, from_email: string, to_email: string, data: string}
 */
function build_newsletter_message(array $recipient, string $subject, string $htmlContent, string $textContent, string $senderName, string $senderEmail, string $replyTo): array
{
    $recipientEmail = (string) ($recipient['email'] ?? '');
    $recipientName = isset($recipient['name']) ? (string) $recipient['name'] : '';

    $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8');
    $boundary = '=_Newsletter_' . bin2hex(random_bytes(12));

    $plainText = $textContent !== ''
        ? $textContent
        : html_entity_decode(strip_tags($htmlContent), ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $plainText = normalize_line_endings($plainText);
    $htmlNormalized = normalize_line_endings($htmlContent);

    $bodyParts = [
        'This is a multi-part message in MIME format.',
        '--' . $boundary,
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
        $plainText,
        '',
        '--' . $boundary,
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
        $htmlNormalized,
        '',
        '--' . $boundary . '--',
        '',
    ];

    $body = implode("\r\n", $bodyParts);

    $domain = parse_url(APP_URL, PHP_URL_HOST) ?: 'musicdistro.io';
    $messageId = sprintf('<newsletter-%s@%s>', bin2hex(random_bytes(8)), $domain);
    $dateHeader = gmdate('D, d M Y H:i:s O');

    $formattedSender = format_email_address($senderName, $senderEmail);
    $formattedRecipient = format_email_address($recipientName, $recipientEmail);

    $headers = [
        'From: ' . $formattedSender,
        'To: ' . $formattedRecipient,
        'Date: ' . $dateHeader,
        'Message-ID: ' . $messageId,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: MusicDistro Newsletter',
    ];

    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . format_email_address('', $replyTo);
    }

    $smtpHeaders = $headers;
    array_splice($smtpHeaders, 2, 0, 'Subject: ' . $encodedSubject);

    $smtpData = implode("\r\n", $smtpHeaders) . "\r\n\r\n" . $body;

    return [
        'subject' => $encodedSubject,
        'mail_headers' => implode("\r\n", $headers),
        'smtp_headers' => implode("\r\n", $smtpHeaders),
        'body' => $body,
        'from_email' => $senderEmail,
        'to_email' => $recipientEmail,
        'data' => $smtpData,
    ];
}

/**
 * @param array{subject: string, mail_headers: string, body: string, to_email: string} $message
 */
function send_newsletter_via_phpmail(array $message): bool
{
    return @mail(
        $message['to_email'],
        $message['subject'],
        $message['body'],
        $message['mail_headers']
    );
}

/**
 * @param array{data: string, from_email: string, to_email: string} $message
 * @param array{host: string, port: int, encryption: string, username: string, password: string} $config
 */
function send_newsletter_via_smtp(array $message, array $config): bool
{
    $host = $config['host'] ?? '';
    $port = (int) ($config['port'] ?? 0);
    $encryption = $config['encryption'] ?? 'tls';
    $username = $config['username'] ?? '';
    $password = $config['password'] ?? '';

    if ($host === '' || $port <= 0) {
        return false;
    }

    $transport = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
    $connection = @stream_socket_client($transport . $host . ':' . $port, $errno, $errstr, 30);

    if (!$connection) {
        return false;
    }

    stream_set_timeout($connection, 30);

    $greeting = smtp_read_response($connection);
    if ($greeting['code'] !== 220) {
        fclose($connection);
        return false;
    }

    $domain = parse_url(APP_URL, PHP_URL_HOST) ?: 'localhost';

    if (!smtp_send_command($connection, 'EHLO ' . $domain, [250])) {
        if (!smtp_send_command($connection, 'HELO ' . $domain, [250])) {
            fclose($connection);
            return false;
        }
    }

    if ($encryption === 'tls') {
        if (!smtp_send_command($connection, 'STARTTLS', [220])) {
            fclose($connection);
            return false;
        }

        if (!stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($connection);
            return false;
        }

        if (!smtp_send_command($connection, 'EHLO ' . $domain, [250])) {
            if (!smtp_send_command($connection, 'HELO ' . $domain, [250])) {
                fclose($connection);
                return false;
            }
        }
    }

    if ($username !== '' && $password !== '') {
        if (!smtp_send_command($connection, 'AUTH LOGIN', [334])) {
            fclose($connection);
            return false;
        }

        if (!smtp_send_command($connection, base64_encode($username), [334])) {
            fclose($connection);
            return false;
        }

        if (!smtp_send_command($connection, base64_encode($password), [235])) {
            fclose($connection);
            return false;
        }
    }

    if (!smtp_send_command($connection, 'MAIL FROM:<' . $message['from_email'] . '>', [250])) {
        fclose($connection);
        return false;
    }

    if (!smtp_send_command($connection, 'RCPT TO:<' . $message['to_email'] . '>', [250, 251])) {
        fclose($connection);
        return false;
    }

    if (!smtp_send_command($connection, 'DATA', [354])) {
        fclose($connection);
        return false;
    }

    fwrite($connection, $message['data']);
    fwrite($connection, "\r\n.\r\n");

    $dataResult = smtp_read_response($connection);
    if ($dataResult['code'] !== 250) {
        fclose($connection);
        return false;
    }

    smtp_send_command($connection, 'QUIT', [221]);
    fclose($connection);

    return true;
}

/**
 * @return array{code: int, message: string}
 */
function smtp_read_response($connection): array
{
    $message = '';

    while (($line = fgets($connection)) !== false) {
        $message .= $line;
        if (preg_match('/^[0-9]{3} /', $line) === 1) {
            break;
        }
    }

    $code = 0;
    if (preg_match('/^([0-9]{3})/', $message, $matches) === 1) {
        $code = (int) $matches[1];
    }

    return [
        'code' => $code,
        'message' => trim($message),
    ];
}

function smtp_send_command($connection, string $command, array $expectedCodes): bool
{
    fwrite($connection, $command . "\r\n");
    $response = smtp_read_response($connection);

    return in_array($response['code'], $expectedCodes, true);
}
