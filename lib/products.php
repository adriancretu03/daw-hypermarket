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
