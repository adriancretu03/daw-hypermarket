<?php

require_once __DIR__ . '/db.php';

function list_reviews_for_product(PDO $pdo, int $productId): array
{
    return db_query(
        $pdo,
        'SELECT reviews.id, reviews.user_id, reviews.rating, reviews.comment, reviews.created_at, users.name AS reviewer_name
         FROM reviews
         JOIN users ON users.id = reviews.user_id
         WHERE reviews.product_id = ?
         ORDER BY reviews.created_at DESC',
        [$productId]
    );
}
