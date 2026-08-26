<?php

require_once __DIR__ . '/db.php';

function track_visit(PDO $pdo, string $page): void
{
    db_execute($pdo, 'INSERT INTO site_visits (page) VALUES (?)', [$page]);
}
