<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/employees.php';

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /admin/employees.php');
    exit;
}

try {
    require_role('admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$deleted = delete_employee(get_pdo(), (int) ($_POST['id'] ?? 0));

flash_set($deleted ? 'Employee deleted.' : 'Cannot delete the last remaining Admin.');
header('Location: /admin/employees.php');
exit;
