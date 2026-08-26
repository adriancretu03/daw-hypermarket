<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/products.php';

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$formUrl = '/employee/product_form.php' . ($id !== null ? '?id=' . $id : '');

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: ' . $formUrl);
    exit;
}

try {
    require_role('employee', 'admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$errors = validate_product($_POST);

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: ' . $formUrl);
    exit;
}

save_product(
    get_pdo(),
    $id,
    (int) $_POST['category_id'],
    trim((string) $_POST['name']),
    (float) $_POST['price'],
    (int) $_POST['stock_qty'],
    trim((string) ($_POST['description'] ?? ''))
);

flash_set('Product saved.');
header('Location: /employee/products.php');
exit;
