<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/products.php';
require_once __DIR__ . '/../lib/reviews.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
$id = (int) ($_GET['id'] ?? 0);
$product = find_product($pdo, $id);

if ($product === null) {
    http_response_code(404);
    echo render_header('Not found');
    echo render_nav(current_user());
    echo '<p>Product not found.</p>';
    echo render_footer();
    exit;
}

$reviews = list_reviews_for_product($pdo, $id);
$user = current_user();
$message = flash_get();

echo render_header($product['name']);
echo render_nav($user);
echo '<h1>' . e($product['name']) . '</h1>';
echo '<p>' . e((string) $product['price']) . ' — ' . e((string) $product['stock_qty']) . ' in stock</p>';
echo '<p>' . e($product['description']) . '</p>';

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<h2>Reviews</h2>';
if ($reviews === []) {
    echo '<p>No records found.</p>';
}
foreach ($reviews as $review) {
    echo '<article><p><strong>' . e($review['reviewer_name']) . '</strong> — '
        . e((string) $review['rating']) . '/5</p><p>' . e($review['comment']) . '</p>';

    if ($user !== null && (int) $review['user_id'] === (int) $user['id']) {
        echo '<form method="post" action="/actions/review_delete.php">'
            . csrf_field()
            . '<input type="hidden" name="review_id" value="' . (int) $review['id'] . '">'
            . '<input type="hidden" name="product_id" value="' . $id . '">'
            . '<button type="submit">Delete my review</button>'
            . '</form>';
    }

    echo '</article>';
}

if ($user !== null && $user['role'] === 'customer') {
    echo '<h2>Leave a review</h2>';
    echo '<form method="post" action="/actions/review_save.php">'
        . csrf_field()
        . '<input type="hidden" name="product_id" value="' . $id . '">'
        . '<label>Rating <select name="rating">'
        . '<option value="1">1</option><option value="2">2</option><option value="3">3</option>'
        . '<option value="4">4</option><option value="5" selected>5</option>'
        . '</select></label>'
        . '<label>Comment <textarea name="comment"></textarea></label>'
        . '<button type="submit">Submit review</button>'
        . '</form>';
}

echo render_footer();
