<?php

$file = $_GET['file'] ?? '';

if ($file === '' || !preg_match('#^[A-Za-z0-9_\-]+\.php$#', $file)) {
    http_response_code(404);
    exit;
}

$path = __DIR__ . '/../actions/' . $file;

if (!is_file($path)) {
    http_response_code(404);
    exit;
}

require $path;
