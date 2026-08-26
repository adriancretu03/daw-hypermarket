<?php

require_once __DIR__ . '/db.php';

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
