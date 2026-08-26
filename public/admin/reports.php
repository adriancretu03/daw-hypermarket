<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/render.php';
require_once __DIR__ . '/../../lib/reports.php';
require_once __DIR__ . '/../../lib/analytics.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/nav.php';
require_once __DIR__ . '/../../includes/footer.php';

try {
    $user = require_role('admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$pdo = get_pdo();
track_visit($pdo, '/admin/reports.php');
$inventory = inventory_report($pdo);
$visits = visits_report($pdo);

echo render_header('Reports');
echo render_nav($user);
echo '<h1>Reports</h1>';

echo '<h2>Inventory by category</h2>';
echo render_list($inventory, [
    'category_name' => 'Category',
    'item_count' => 'Items',
    'stock_value' => 'Stock value',
]);

echo '<h2>Page visits</h2>';
echo render_list($visits, [
    'page' => 'Page',
    'visit_count' => 'Visits',
]);

echo '<p><a href="/actions/report_export.php?format=pdf">Export PDF</a>'
    . ' | <a href="/actions/report_export.php?format=csv">Export CSV</a></p>';

echo render_footer();
