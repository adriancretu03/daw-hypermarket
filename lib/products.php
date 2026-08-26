<?php

require_once __DIR__ . '/db.php';

function search_products(PDO $pdo, ?string $term, ?int $categoryId): array
{
    $sql = 'SELECT id, category_id, name, price, stock_qty FROM products WHERE 1 = 1';
    $params = [];

    if ($term !== null && $term !== '') {
        $sql .= ' AND name LIKE ?';
        $params[] = '%' . $term . '%';
    }

    if ($categoryId !== null) {
        $sql .= ' AND category_id = ?';
        $params[] = $categoryId;
    }

    $sql .= ' ORDER BY name';

    return db_query($pdo, $sql, $params);
}

function find_product(PDO $pdo, int $id): ?array
{
    $rows = db_query($pdo, 'SELECT id, category_id, name, price, stock_qty, description FROM products WHERE id = ?', [$id]);

    return $rows[0] ?? null;
}

function list_products_with_category(PDO $pdo): array
{
    return db_query(
        $pdo,
        'SELECT p.id, p.name, p.price, p.stock_qty, c.name AS category_name
         FROM products p
         JOIN categories c ON c.id = p.category_id
         ORDER BY p.name'
    );
}

function validate_product(array $input): array
{
    $errors = [];
    $name = trim((string) ($input['name'] ?? ''));
    $categoryId = $input['category_id'] ?? '';
    $price = $input['price'] ?? '';
    $stockQty = $input['stock_qty'] ?? '';

    if ($name === '') {
        $errors[] = 'Product name is required.';
    }

    if (!ctype_digit((string) $categoryId) || (int) $categoryId <= 0) {
        $errors[] = 'A category must be selected.';
    }

    if (!is_numeric($price)) {
        $errors[] = 'Price must be numeric.';
    }

    if (!ctype_digit((string) $stockQty)) {
        $errors[] = 'Stock quantity must be a non-negative whole number.';
    }

    return $errors;
}

function save_product(PDO $pdo, ?int $id, int $categoryId, string $name, float $price, int $stockQty, string $description): int
{
    if ($id !== null) {
        db_execute(
            $pdo,
            'UPDATE products SET category_id = ?, name = ?, price = ?, stock_qty = ?, description = ? WHERE id = ?',
            [$categoryId, $name, $price, $stockQty, $description, $id]
        );

        return $id;
    }

    db_execute(
        $pdo,
        'INSERT INTO products (category_id, name, price, stock_qty, description) VALUES (?, ?, ?, ?, ?)',
        [$categoryId, $name, $price, $stockQty, $description]
    );

    return (int) $pdo->lastInsertId();
}
