<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/products.php';
require_once __DIR__ . '/../../lib/categories.php';
require_once __DIR__ . '/../../lib/analytics.php';
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
track_visit($pdo, '/employee/product_form.php');
$id = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : null;
$product = $id !== null ? find_product($pdo, $id) : null;
$categories = list_categories($pdo);
$message = flash_get();
$title = $product !== null ? 'Edit product' : 'New product';

echo render_header($title);
echo render_nav($user);

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h1>' . e($title) . '</h1>';

echo '<form method="post" action="/actions/product_save.php">' . csrf_field();

if ($id !== null) {
    echo '<input type="hidden" name="id" value="' . $id . '">';
}

echo '<label>Name <input type="text" name="name" value="' . e($product['name'] ?? '') . '"></label>';
echo '<label>Category <select name="category_id">';

foreach ($categories as $category) {
    $selected = $product !== null && (int) $product['category_id'] === (int) $category['id'] ? ' selected' : '';
    echo '<option value="' . (int) $category['id'] . '"' . $selected . '>' . e($category['name']) . '</option>';
}

echo '</select></label>';
echo '<label>Price <input type="text" name="price" value="' . e((string) ($product['price'] ?? '')) . '"></label>';
echo '<label>Stock quantity <input type="text" name="stock_qty" value="' . e((string) ($product['stock_qty'] ?? '0')) . '"></label>';
echo '<label>Description <textarea name="description">' . e($product['description'] ?? '') . '</textarea></label>';
echo '<button type="submit">Save</button>';
echo '</form>';

echo render_footer();
