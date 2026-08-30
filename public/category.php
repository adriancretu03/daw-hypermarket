<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/products.php';
require_once __DIR__ . '/../lib/categories.php';
require_once __DIR__ . '/../lib/external_content.php';
require_once __DIR__ . '/../lib/analytics.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
$user = current_user();
track_visit($pdo, '/category.php');

$id = (int) ($_GET['id'] ?? 0);
$category = find_category($pdo, $id);

if ($category === null) {
    http_response_code(404);
    echo render_header('Not found');
    echo render_nav($user);
    echo '<p>Category not found.</p>';
    echo render_footer();
    exit;
}

$products = search_products($pdo, null, $id);
$rate = cached_eur_rate(__DIR__ . '/../cache/eur_rate.cache', 3600);

echo render_header(
    $category['name'],
    'Browse ' . $category['name'] . ' products at the hypermarket, priced in RON and EUR.'
);
echo render_nav($user);
echo '<p><a href="/index.php">All categories</a></p>';
echo '<h1>' . e($category['name']) . '</h1>';

if ($rate === null) {
    echo '<p>Exchange rate currently unavailable.</p>';
}

if ($products !== []) {
    echo '<div class="item-list">';
    foreach ($products as $product) {
        $line = '<a href="/product.php?id=' . (int) $product['id'] . '">' . e($product['name']) . '</a>'
            . ' — ' . e((string) $product['price']) . ' RON';

        if ($rate !== null) {
            $line .= ' (~' . number_format(((float) $product['price']) * $rate, 2) . ' EUR)';
        }

        echo '<p class="item-row">' . $line . '</p>';
    }
    echo '</div>';
} else {
    echo '<p>No records found.</p>';
}

echo render_footer();
