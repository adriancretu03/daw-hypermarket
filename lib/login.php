<?php

require_once __DIR__ . '/db.php';

function find_user_by_email(PDO $pdo, string $email): ?array
{
    $rows = db_query($pdo, 'SELECT * FROM users WHERE email = ?', [$email]);

    return $rows[0] ?? null;
}
