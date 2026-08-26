<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/captcha.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/registration.php';
require_once __DIR__ . '/../lib/auth.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /register.php');
    exit;
}

if (!captcha_verify($_POST['g-recaptcha-response'] ?? '')) {
    flash_set('Please complete the CAPTCHA.');
    header('Location: /register.php');
    exit;
}

$errors = validate_registration($_POST);
$pdo = get_pdo();

if ($errors === [] && email_taken($pdo, trim((string) $_POST['email']))) {
    $errors[] = 'That email is already registered.';
}

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: /register.php');
    exit;
}

$id = register_user($pdo, trim((string) $_POST['name']), trim((string) $_POST['email']), (string) $_POST['password']);

login_user(['id' => $id, 'name' => trim((string) $_POST['name']), 'role' => 'customer']);
header('Location: /index.php');
exit;
