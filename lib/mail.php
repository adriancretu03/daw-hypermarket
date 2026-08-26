<?php

function contact_mail_transport(string $to, string $subject, string $body, string $headers): bool
{
    return mail($to, $subject, $body, $headers);
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
