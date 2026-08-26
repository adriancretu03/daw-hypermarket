<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/products.php';
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
$products = list_products_with_category($pdo);
$message = flash_get();

echo render_header('Manage products');
echo render_nav($user);

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h1>Products</h1><p><a href="/employee/product_form.php">New product</a></p>';

foreach ($products as $product) {
    echo '<p>' . e($product['name']) . ' (' . e($product['category_name']) . ') — '
        . e((string) $product['price']) . ' — stock: ' . (int) $product['stock_qty']
        . ' <a href="/employee/product_form.php?id=' . (int) $product['id'] . '">Edit</a>'
        . '<form method="post" action="/actions/product_delete.php" style="display:inline">'
        . csrf_field()
        . '<input type="hidden" name="id" value="' . (int) $product['id'] . '">'
        . '<button type="submit">Delete</button>'
        . '</form></p>';
}

if ($products === []) {
    echo '<p>No records found.</p>';
}

echo render_footer();
