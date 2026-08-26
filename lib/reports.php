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

function report_to_csv(array $inventory, array $visits): string
{
    $handle = fopen('php://temp', 'r+');

    fputcsv($handle, ['Inventory by category']);
    fputcsv($handle, ['Category', 'Items', 'Stock value']);
    foreach ($inventory as $row) {
        fputcsv($handle, [$row['category_name'], $row['item_count'], $row['stock_value']]);
    }

    fputcsv($handle, []);
    fputcsv($handle, ['Page visits']);
    fputcsv($handle, ['Page', 'Visits']);
    foreach ($visits as $row) {
        fputcsv($handle, [$row['page'], $row['visit_count']]);
    }

    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return $csv;
}

function report_to_pdf_lines(array $inventory, array $visits): array
{
    $lines = ['Hypermarket Report', '', 'Inventory by category'];

    foreach ($inventory as $row) {
        $lines[] = sprintf(
            '%s - %d items - %.2f stock value',
            $row['category_name'],
            $row['item_count'],
            $row['stock_value']
        );
    }

    $lines[] = '';
    $lines[] = 'Page visits';

    foreach ($visits as $row) {
        $lines[] = sprintf('%s - %d visits', $row['page'], $row['visit_count']);
    }

    if ($visits === []) {
        $lines[] = 'No visits recorded yet.';
    }

    return $lines;
}
