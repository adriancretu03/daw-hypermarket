<?php

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/captcha.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$message = flash_get();

echo render_header('Register');
echo render_nav(current_user());
echo '<h1>Register</h1>';

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<form method="post" action="/actions/register.php">'
    . csrf_field()
    . '<label>Name <input type="text" name="name"></label>'
    . '<label>Email <input type="email" name="email"></label>'
    . '<label>Password <input type="password" name="password"></label>'
    . captcha_widget()
    . '<button type="submit">Register</button>'
    . '</form>';

echo render_footer();
