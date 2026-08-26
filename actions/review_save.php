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

$errors = validate_review($_POST);

if ($errors !== []) {
    flash_set(implode(' ', $errors));
    header('Location: /product.php?id=' . $productId);
    exit;
}

save_review(get_pdo(), $productId, (int) $user['id'], (int) $_POST['rating'], trim((string) ($_POST['comment'] ?? '')));

header('Location: /product.php?id=' . $productId);
exit;
