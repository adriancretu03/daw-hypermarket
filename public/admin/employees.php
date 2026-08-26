<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/employees.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/nav.php';
require_once __DIR__ . '/../../includes/footer.php';

try {
    $user = require_role('admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_pdo();
$employees = list_employees($pdo);
$message = flash_get();

echo render_header('Manage employees');
echo render_nav($user);

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h1>Employees</h1><p><a href="/admin/employee_form.php">New employee</a></p>';

foreach ($employees as $employee) {
    echo '<p>' . e($employee['name']) . ' — ' . e($employee['email']) . ' (' . e($employee['role']) . ')'
        . ' <a href="/admin/employee_form.php?id=' . (int) $employee['id'] . '">Edit</a>'
        . '<form method="post" action="/actions/employee_delete.php" style="display:inline">'
        . csrf_field()
        . '<input type="hidden" name="id" value="' . (int) $employee['id'] . '">'
        . '<button type="submit">Delete</button>'
        . '</form></p>';
}

if ($employees === []) {
    echo '<p>No records found.</p>';
}

echo render_footer();
