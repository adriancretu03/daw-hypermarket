<?php

require_once __DIR__ . '/../lib/render.php';

function render_nav(?array $user): string
{
    $logo = '<a href="/index.php" class="brand"><img src="/images/logo.svg" alt="Hypermarket logo" width="140" height="34"></a>';

    $primaryLinks = [
        '<a href="/index.php">Catalog</a>',
        '<a href="/about.php">About</a>',
        '<a href="/contact.php">Contact</a>',
    ];

    if ($user === null) {
        $primaryLinks[] = '<a href="/login.php">Login</a>';
        $primaryLinks[] = '<a href="/register.php">Register</a>';

        return '<nav class="site-nav"><div class="nav-inner"><div class="nav-row">'
            . $logo . implode(' ', $primaryLinks) . '</div></div></nav><main>';
    }

    $actionLinks = [];

    if ($user['role'] === 'employee' || $user['role'] === 'admin') {
        $actionLinks[] = '<a href="/employee/products.php">Manage products</a>';
        $actionLinks[] = '<a href="/employee/categories.php">Manage categories</a>';
    }

    if ($user['role'] === 'admin') {
        $actionLinks[] = '<a href="/admin/employees.php">Manage employees</a>';
        $actionLinks[] = '<a href="/admin/reports.php">Reports</a>';
    }

    return '<nav class="site-nav"><div class="nav-inner">'
        . '<div class="nav-row">' . $logo . implode(' ', $primaryLinks)
        . '<span class="nav-welcome">Welcome, ' . e($user['name']) . '</span>'
        . '<a href="/logout.php">Logout</a></div>'
        . '<div class="nav-row nav-actions">' . implode(' ', $actionLinks) . '</div>'
        . '</div></nav><main>';
}
