<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/categories.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /employee/categories.php');
    exit;
}

try {
    require_role('employee', 'admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_pdo();
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$name = trim((string) ($_POST['name'] ?? ''));
$errors = validate_category($_POST);

if ($errors === [] && category_name_taken($pdo, $name, $id)) {
    $errors[] = 'That category name is already in use.';
}

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: /employee/categories.php');
    exit;
}

save_category($pdo, $id, $name);

flash_set('Category saved.');
header('Location: /employee/categories.php');
exit;
