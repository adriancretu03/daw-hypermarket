<?php

require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/captcha.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/mail.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /contact.php');
    exit;
}

if (!captcha_verify($_POST['g-recaptcha-response'] ?? '')) {
    flash_set('Please complete the CAPTCHA.');
    header('Location: /contact.php');
    exit;
}

$errors = validate_contact($_POST);

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: /contact.php');
    exit;
}

$sent = send_contact_message(
    trim((string) $_POST['name']),
    trim((string) $_POST['email']),
    trim((string) $_POST['message'])
);

flash_set($sent
    ? 'Thanks for reaching out — we will get back to you soon.'
    : 'Sorry, your message could not be sent right now. Please try again later.');
header('Location: /contact.php');
exit;
