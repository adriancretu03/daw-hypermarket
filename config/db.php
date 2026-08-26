<?php

require_once __DIR__ . '/../lib/db.php';

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'hypermarket';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

        $pdo = db_connect($dsn, $user, $pass);
    }

    return $pdo;
}
