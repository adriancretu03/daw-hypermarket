<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/categories.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/nav.php';
require_once __DIR__ . '/../../includes/footer.php';

try {
    $user = require_role('employee', 'admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_pdo();
$categories = list_categories($pdo);
$message = flash_get();

echo render_header('Manage categories');
echo render_nav($user);

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h1>Categories</h1>';

foreach ($categories as $category) {
    echo '<form method="post" action="/actions/category_save.php" style="display:inline">'
        . csrf_field()
        . '<input type="hidden" name="id" value="' . (int) $category['id'] . '">'
        . '<input type="text" name="name" value="' . e($category['name']) . '">'
        . '<button type="submit">Rename</button>'
        . '</form>'
        . '<form method="post" action="/actions/category_delete.php" style="display:inline">'
        . csrf_field()
        . '<input type="hidden" name="id" value="' . (int) $category['id'] . '">'
        . '<button type="submit">Delete</button>'
        . '</form><br>';
}

if ($categories === []) {
    echo '<p>No records found.</p>';
}

echo '<h2>Add category</h2>';
echo '<form method="post" action="/actions/category_save.php">'
    . csrf_field()
    . '<input type="text" name="name" placeholder="Category name">'
    . '<button type="submit">Add</button>'
    . '</form>';

echo render_footer();
