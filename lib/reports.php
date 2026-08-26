<?php

require_once __DIR__ . '/db.php';

function inventory_report(PDO $pdo): array
{
    return db_query(
        $pdo,
        'SELECT c.name AS category_name,
                COUNT(p.id) AS item_count,
                COALESCE(SUM(p.price * p.stock_qty), 0) AS stock_value
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.id
         GROUP BY c.id, c.name
         ORDER BY c.name'
    );
}

function visits_report(PDO $pdo): array
{
    return db_query(
        $pdo,
        'SELECT page, COUNT(*) AS visit_count
         FROM site_visits
         GROUP BY page
         ORDER BY visit_count DESC'
    );
}
