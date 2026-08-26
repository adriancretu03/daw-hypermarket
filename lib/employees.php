<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function list_employees(PDO $pdo): array
{
    return db_query(
        $pdo,
        "SELECT id, name, email, role, created_at FROM users WHERE role IN ('employee', 'admin') ORDER BY name"
    );
}

function find_employee(PDO $pdo, int $id): ?array
{
    $rows = db_query(
        $pdo,
        "SELECT id, name, email, role, created_at FROM users WHERE id = ? AND role IN ('employee', 'admin')",
        [$id]
    );

    return $rows[0] ?? null;
}

function count_admins(PDO $pdo): int
{
    $rows = db_query($pdo, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");

    return (int) $rows[0]['total'];
}

function is_last_admin(PDO $pdo, int $id): bool
{
    $employee = find_employee($pdo, $id);

    if ($employee === null || $employee['role'] !== 'admin') {
        return false;
    }

    return count_admins($pdo) <= 1;
}

function delete_employee(PDO $pdo, int $id): bool
{
    if (is_last_admin($pdo, $id)) {
        return false;
    }

    db_execute($pdo, 'DELETE FROM users WHERE id = ?', [$id]);

    return true;
}

function validate_employee(array $input, bool $requirePassword): array
{
    $errors = [];
    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $role = (string) ($input['role'] ?? '');
    $password = (string) ($input['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (!in_array($role, ['employee', 'admin'], true)) {
        $errors[] = 'Role must be Employee or Admin.';
    }

    if ($requirePassword && strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif (!$requirePassword && $password !== '' && strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    return $errors;
}

function employee_email_taken(PDO $pdo, string $email, ?int $excludeId = null): bool
{
    if ($excludeId !== null) {
        $rows = db_query($pdo, 'SELECT id FROM users WHERE email = ? AND id != ?', [$email, $excludeId]);
    } else {
        $rows = db_query($pdo, 'SELECT id FROM users WHERE email = ?', [$email]);
    }

    return $rows !== [];
}

function would_orphan_admins(PDO $pdo, int $id, string $newRole): bool
{
    if ($newRole === 'admin') {
        return false;
    }

    $employee = find_employee($pdo, $id);

    if ($employee === null || $employee['role'] !== 'admin') {
        return false;
    }

    return count_admins($pdo) <= 1;
}

function save_employee(PDO $pdo, ?int $id, string $name, string $email, string $role, ?string $password): int
{
    if ($id !== null) {
        if ($password !== null) {
            db_execute(
                $pdo,
                'UPDATE users SET name = ?, email = ?, role = ?, password_hash = ? WHERE id = ?',
                [$name, $email, $role, hash_password($password), $id]
            );
        } else {
            db_execute(
                $pdo,
                'UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?',
                [$name, $email, $role, $id]
            );
        }

        return $id;
    }

    db_execute(
        $pdo,
        'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
        [$name, $email, hash_password((string) $password), $role]
    );

    return (int) $pdo->lastInsertId();
}
