<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/login.php';
require_once __DIR__ . '/../lib/auth.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /login.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$user = find_user_by_email(get_pdo(), $email);

if ($user === null || !verify_password($password, $user['password_hash'])) {
    flash_set('Invalid email or password.');
    header('Location: /login.php');
    exit;
}

login_user(['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']]);
header('Location: /index.php');
exit;
