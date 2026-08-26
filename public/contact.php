<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/captcha.php';
require_once __DIR__ . '/../lib/flash.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/analytics.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
track_visit($pdo, '/contact.php');
$message = flash_get();

echo render_header(
    'Contact us',
    'Send a message to the hypermarket team — questions, feedback, or support requests.'
);
echo render_nav(current_user());
echo '<h1>Contact us</h1>';

if ($message !== null) {
    echo '<p>' . e($message) . '</p>';
}

echo '<form method="post" action="/actions/contact_send.php">'
    . csrf_field()
    . '<label>Name <input type="text" name="name"></label>'
    . '<label>Email <input type="email" name="email"></label>'
    . '<label>Message <textarea name="message"></textarea></label>'
    . captcha_widget()
    . '<button type="submit">Send message</button>'
    . '</form>';

echo render_footer();
