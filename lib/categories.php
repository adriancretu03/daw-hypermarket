<?php

require_once __DIR__ . '/db.php';

function list_categories(PDO $pdo): array
{
    return db_query($pdo, 'SELECT id, name FROM categories ORDER BY name');
}

function validate_category(array $input): array
{
    $errors = [];
    $name = trim((string) ($input['name'] ?? ''));

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    return $errors;
}

function category_name_taken(PDO $pdo, string $name, ?int $excludeId = null): bool
{
    if ($excludeId !== null) {
        $rows = db_query($pdo, 'SELECT id FROM categories WHERE name = ? AND id != ?', [$name, $excludeId]);
    } else {
        $rows = db_query($pdo, 'SELECT id FROM categories WHERE name = ?', [$name]);
    }

    return $rows !== [];
}

function save_category(PDO $pdo, ?int $id, string $name): int
{
    if ($id !== null) {
        db_execute($pdo, 'UPDATE categories SET name = ? WHERE id = ?', [$name, $id]);

        return $id;
    }

    db_execute($pdo, 'INSERT INTO categories (name) VALUES (?)', [$name]);

    return (int) $pdo->lastInsertId();
}

function category_has_products(PDO $pdo, int $categoryId): bool
{
    $rows = db_query($pdo, 'SELECT COUNT(*) AS total FROM products WHERE category_id = ?', [$categoryId]);

    return (int) $rows[0]['total'] > 0;
}

function delete_category(PDO $pdo, int $categoryId): bool
{
    if (category_has_products($pdo, $categoryId)) {
        return false;
    }

    db_execute($pdo, 'DELETE FROM categories WHERE id = ?', [$categoryId]);

    return true;
}
