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

function validate_review(array $input): array
{
    $errors = [];
    $rating = $input['rating'] ?? '';

    if (!ctype_digit((string) $rating) || (int) $rating < 1 || (int) $rating > 5) {
        $errors[] = 'Rating must be a whole number from 1 to 5.';
    }

    return $errors;
}

function save_review(PDO $pdo, int $productId, int $userId, int $rating, string $comment): void
{
    $existing = db_query(
        $pdo,
        'SELECT id FROM reviews WHERE product_id = ? AND user_id = ?',
        [$productId, $userId]
    );

    if ($existing !== []) {
        db_execute(
            $pdo,
            'UPDATE reviews SET rating = ?, comment = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?',
            [$rating, $comment, $existing[0]['id']]
        );

        return;
    }

    db_execute(
        $pdo,
        'INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)',
        [$productId, $userId, $rating, $comment]
    );
}

function delete_review(PDO $pdo, int $reviewId, int $userId): bool
{
    $rowsAffected = db_execute(
        $pdo,
        'DELETE FROM reviews WHERE id = ? AND user_id = ?',
        [$reviewId, $userId]
    );

    return $rowsAffected > 0;
}
