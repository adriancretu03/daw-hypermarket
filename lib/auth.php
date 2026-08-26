<?php

final class UnauthorizedException extends RuntimeException
{
}

function hash_password(string $plain): string
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

function verify_password(string $plain, string $hash): bool
{
    return password_verify($plain, $hash);
}

function login_user(array $user): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role'],
    ];
}

function logout_user(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    unset($_SESSION['user']);
}

function current_user(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return $_SESSION['user'] ?? null;
}

function require_role(string ...$allowedRoles): array
{
    $user = current_user();

    if ($user === null || !in_array($user['role'], $allowedRoles, true)) {
        throw new UnauthorizedException('Access denied for this role.');
    }

    return $user;
}
