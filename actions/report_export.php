<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/reports.php';
require_once __DIR__ . '/../lib/pdf.php';

try {
    require_role('admin');
} catch (UnauthorizedException $e) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$format = (string) ($_GET['format'] ?? '');
$pdo = get_pdo();
$inventory = inventory_report($pdo);
$visits = visits_report($pdo);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report.csv"');
    echo report_to_csv($inventory, $visits);
    exit;
}

if ($format === 'pdf') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="report.pdf"');
    echo pdf_from_lines(report_to_pdf_lines($inventory, $visits));
    exit;
}

http_response_code(400);
echo 'Unknown export format.';
