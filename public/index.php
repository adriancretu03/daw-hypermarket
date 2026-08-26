<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$categories = db_query(get_pdo(), 'SELECT id, name FROM categories ORDER BY name');

$user = current_user();
echo render_header('Catalog');
echo render_nav($user);
echo '<h1>Categories</h1>';
echo render_list($categories, ['name' => 'Category']);
echo render_footer();
