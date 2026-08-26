<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function validate_registration(array $input): array
{
    $errors = [];

    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $password = (string) ($input['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    return $errors;
}

function email_taken(PDO $pdo, string $email): bool
{
    $rows = db_query($pdo, 'SELECT id FROM users WHERE email = ?', [$email]);

    return $rows !== [];
}

function register_user(PDO $pdo, string $name, string $email, string $password): int
{
    db_execute(
        $pdo,
        'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
        [$name, $email, hash_password($password), 'customer']
    );

    return (int) $pdo->lastInsertId();
}
