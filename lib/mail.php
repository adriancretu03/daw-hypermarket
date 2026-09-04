<?php

require_once __DIR__ . '/phpmailer/class.phpmailer.php';

function contact_mail_transport(string $to, string $subject, string $body, string $headers): bool
{
    $mail = new PHPMailer(true);
    $from = getenv('SMTP_FROM_EMAIL') ?: (getenv('SMTP_USERNAME') ?: '');

    try {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'ssl';
        $mail->Host = getenv('SMTP_HOST') ?: '';
        $mail->Port = (int) (getenv('SMTP_PORT') ?: 465);
        $mail->Username = getenv('SMTP_USERNAME') ?: '';
        $mail->Password = getenv('SMTP_PASSWORD') ?: '';

        // Romanian input arrives as UTF-8; quoted-printable keeps it 7-bit clean
        // on servers that never advertise 8BITMIME.
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'quoted-printable';

        // Pin the EHLO name and Message-ID domain to our own domain instead of
        // letting ServerHostname() fall back to localhost.localdomain.
        if (strpos($from, '@') !== false) {
            $mail->Hostname = substr($from, strrpos($from, '@') + 1);
        }

        // Reply-To first: SetFrom($auto=1) only self-appends when ReplyTo is
        // still empty, and we want the visitor to be the sole Reply-To.
        if (preg_match('/Reply-To:\s*(\S+)/i', $headers, $m) === 1) {
            $mail->AddReplyTo($m[1]);
        }

        $mail->SetFrom($from, getenv('SMTP_FROM_NAME') ?: 'Hypermarket');
        $mail->AddAddress($to);
        $mail->IsHTML(false);
        $mail->Subject = $subject;
        // Canonical CRLF hard breaks; quoted-printable would otherwise ship
        // every bare LF as a literal "=0A" escape.
        $mail->Body = preg_replace('/\r\n|\r|\n/', "\r\n", $body);

        return $mail->Send();
    } catch (phpmailerException $e) {
        // Server-side only: ErrorInfo carries the SMTP dialogue (and can echo
        // the username), so it must never reach the visitor's flash message.
        error_log('contact_mail_transport failed: ' . $mail->ErrorInfo);

        return false;
    }
}

function validate_contact(array $input): array
{
    $errors = [];
    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $message = trim((string) ($input['message'] ?? ''));

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'A valid email is required.';
    }

    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    return $errors;
}

/**
 * Formats the notification the admin receives.
 *
 * Must not start with a header-shaped line ("From:", "To:", ...) — a body that
 * opens that way then blanks a line looks like a forged/embedded header block
 * to spam filters, which is what silently ate these messages.
 */
function contact_message_body(string $name, string $email, string $message): string
{
    return "New message from the Hypermarket contact form.\n\n"
        . "Name:  {$name}\n"
        . "Email: {$email}\n\n"
        . "Message:\n{$message}\n";
}

function send_contact_message(string $name, string $email, string $message, ?callable $transport = null): bool
{
    $transport ??= 'contact_mail_transport';
    $to = getenv('CONTACT_ADMIN_EMAIL') ?: 'admin@example.com';
    $subject = 'Hypermarket contact form: ' . $name;
    $body = contact_message_body($name, $email, $message);
    $headers = 'Reply-To: ' . $email;

    return $transport($to, $subject, $body, $headers);
}
