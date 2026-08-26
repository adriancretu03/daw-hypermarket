<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/employees.php';

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$formUrl = '/admin/employee_form.php' . ($id !== null ? '?id=' . $id : '');

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: ' . $formUrl);
    exit;
}

try {
    require_role('admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_pdo();
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$role = (string) ($_POST['role'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$errors = validate_employee($_POST, $id === null);

if ($errors === [] && employee_email_taken($pdo, $email, $id)) {
    $errors[] = 'That email is already in use.';
}

if ($errors === [] && $id !== null && would_orphan_admins($pdo, $id, $role)) {
    $errors[] = 'Cannot change role: at least one Admin account must remain.';
}

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: ' . $formUrl);
    exit;
}

save_employee($pdo, $id, $name, $email, $role, $password === '' ? null : $password);

flash_set('Employee saved.');
header('Location: /admin/employees.php');
exit;
