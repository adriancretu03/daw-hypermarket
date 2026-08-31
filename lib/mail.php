<?php

require_once __DIR__ . '/phpmailer/class.phpmailer.php';

function contact_mail_transport(string $to, string $subject, string $body, string $headers): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'ssl';
        $mail->Host = getenv('SMTP_HOST') ?: '';
        $mail->Port = (int) (getenv('SMTP_PORT') ?: 465);
        $mail->Username = getenv('SMTP_USERNAME') ?: '';
        $mail->Password = getenv('SMTP_PASSWORD') ?: '';

        $mail->SetFrom(getenv('SMTP_FROM_EMAIL') ?: $mail->Username, getenv('SMTP_FROM_NAME') ?: 'Hypermarket');
        $mail->AddAddress($to);

        if (preg_match('/Reply-To:\s*(\S+)/i', $headers, $m) === 1) {
            $mail->AddReplyTo($m[1]);
        }
        $mail->IsHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->Send();
    } catch (Exception $e) {
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

function send_contact_message(string $name, string $email, string $message, ?callable $transport = null): bool
{
    $transport ??= 'contact_mail_transport';
    $to = getenv('CONTACT_ADMIN_EMAIL') ?: 'admin@example.com';
    $subject = 'Hypermarket contact form: ' . $name;
    $body = "From: {$name} <{$email}>\n\n{$message}";
    $headers = 'From: no-reply@hypermarket.local' . "\r\n" . 'Reply-To: ' . $email;

    return $transport($to, $subject, $body, $headers);
}
