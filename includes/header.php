<?php

require_once __DIR__ . '/../lib/render.php';

function render_header(string $title, ?string $description = null): string
{
    $safeTitle = e($title);
    $safeDescription = e($description ?? 'Hypermarket — browse our catalog, categories, and best deals online.');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{$safeDescription}">
    <title>{$safeTitle} — Hypermarket</title>
</head>
<body>
<main>
HTML;
}
