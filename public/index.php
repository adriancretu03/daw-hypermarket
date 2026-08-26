<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/products.php';
require_once __DIR__ . '/../lib/analytics.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
track_visit($pdo, '/index.php');
$categories = db_query($pdo, 'SELECT id, name FROM categories ORDER BY name');

$term = isset($_GET['q']) && $_GET['q'] !== '' ? (string) $_GET['q'] : null;
$categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int) $_GET['category_id'] : null;
$products = search_products($pdo, $term, $categoryId);

$user = current_user();
echo render_header('Catalog');
echo render_nav($user);
echo '<h1>Categories</h1>';

$categoryLinks = '<a href="/index.php">All</a>';
foreach ($categories as $category) {
    $categoryLinks .= ' <a href="/category.php?id=' . (int) $category['id'] . '">'
        . e($category['name']) . '</a>';
}
echo '<nav>' . $categoryLinks . '</nav>';

echo '<form method="get" action="/index.php">'
    . '<input type="text" name="q" placeholder="Search products" value="' . e($term ?? '') . '">'
    . '<button type="submit">Search</button>'
    . '</form>';

echo '<h2>Products</h2>';
foreach ($products as $product) {
    echo '<p><a href="/product.php?id=' . (int) $product['id'] . '">' . e($product['name']) . '</a>'
        . ' — ' . e((string) $product['price']) . '</p>';
}
if ($products === []) {
    echo '<p>No records found.</p>';
}

echo render_footer();
