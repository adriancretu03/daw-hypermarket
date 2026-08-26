<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/employees.php';
require_once __DIR__ . '/../../lib/analytics.php';
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
track_visit($pdo, '/admin/employee_form.php');
$id = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : null;
$employee = $id !== null ? find_employee($pdo, $id) : null;

if ($id !== null && $employee === null) {
    flash_set('Employee not found.');
    header('Location: /admin/employees.php');
    exit;
}

$message = flash_get();
$title = $employee !== null ? 'Edit employee' : 'New employee';
$currentRole = $employee['role'] ?? 'employee';

echo render_header($title);
echo render_nav($user);

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h1>' . e($title) . '</h1>';

echo '<form method="post" action="/actions/employee_save.php">' . csrf_field();

if ($id !== null) {
    echo '<input type="hidden" name="id" value="' . $id . '">';
}

echo '<label>Name <input type="text" name="name" value="' . e($employee['name'] ?? '') . '"></label>';
echo '<label>Email <input type="text" name="email" value="' . e($employee['email'] ?? '') . '"></label>';
echo '<label>Role <select name="role">';

foreach (['employee' => 'Employee', 'admin' => 'Admin'] as $value => $label) {
    $selected = $currentRole === $value ? ' selected' : '';
    echo '<option value="' . e($value) . '"' . $selected . '>' . e($label) . '</option>';
}

echo '</select></label>';
echo '<label>Password'
    . ($employee !== null ? ' (leave blank to keep current password)' : '')
    . ' <input type="password" name="password"></label>';
echo '<button type="submit">Save</button>';
echo '</form>';

echo render_footer();
