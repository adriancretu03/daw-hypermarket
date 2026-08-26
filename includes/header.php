<?php

require_once __DIR__ . '/../lib/render.php';

function render_header(string $title): string
{
    $safeTitle = e($title);

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$safeTitle} — Hypermarket</title>
</head>
<body>
HTML;
}
