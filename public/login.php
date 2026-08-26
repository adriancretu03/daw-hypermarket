<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/analytics.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
track_visit($pdo, '/login.php');
$message = flash_get();

echo render_header('Login');
echo render_nav(current_user());
echo '<h1>Login</h1>';

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<form method="post" action="/actions/login.php">'
    . csrf_field()
    . '<label>Email <input type="email" name="email"></label>'
    . '<label>Password <input type="password" name="password"></label>'
    . '<button type="submit">Login</button>'
    . '</form>';

echo render_footer();
