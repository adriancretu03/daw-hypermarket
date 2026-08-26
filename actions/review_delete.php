<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/reviews.php';

$productId = (int) ($_POST['product_id'] ?? 0);

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    flash_set('Invalid request, please try again.');
    header('Location: /product.php?id=' . $productId);
    exit;
}

try {
    $user = require_role('customer');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$reviewId = (int) ($_POST['review_id'] ?? 0);
$deleted = delete_review(get_pdo(), $reviewId, (int) $user['id']);

if (!$deleted) {
    flash_set('You can only delete your own review.');
}

header('Location: /product.php?id=' . $productId);
exit;
