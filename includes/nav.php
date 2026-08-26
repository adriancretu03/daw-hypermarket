<?php

require_once __DIR__ . '/../lib/render.php';

function render_nav(?array $user): string
{
    $links = ['<a href="/index.php">Catalog</a>'];

    if ($user === null) {
        $links[] = '<a href="/login.php">Login</a>';
        $links[] = '<a href="/register.php">Register</a>';

        return '<nav>' . implode(' ', $links) . '</nav>';
    }

    $links[] = '<span>Welcome, ' . e($user['name']) . '</span>';

    if ($user['role'] === 'employee' || $user['role'] === 'admin') {
        $links[] = '<a href="/employee/products.php">Manage products</a>';
        $links[] = '<a href="/employee/categories.php">Manage categories</a>';
    }

    if ($user['role'] === 'admin') {
        $links[] = '<a href="/admin/employees.php">Manage employees</a>';
        $links[] = '<a href="/admin/reports.php">Reports</a>';
    }

    $links[] = '<a href="/logout.php">Logout</a>';

    return '<nav>' . implode(' ', $links) . '</nav>';
}
