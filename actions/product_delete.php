<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/products.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /employee/products.php');
    exit;
}

try {
    require_role('employee', 'admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

delete_product(get_pdo(), (int) ($_POST['id'] ?? 0));

flash_set('Product deleted.');
header('Location: /employee/products.php');
exit;
