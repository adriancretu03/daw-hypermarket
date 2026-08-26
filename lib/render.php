<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function render_list(array $rows, array $columns): string
{
    if ($rows === []) {
        return '<p>No records found.</p>';
    }

    $header = '';
    foreach ($columns as $label) {
        $header .= '<th>' . e($label) . '</th>';
    }

    $body = '';
    foreach ($rows as $row) {
        $body .= '<tr>';
        foreach (array_keys($columns) as $key) {
            $body .= '<td>' . e((string) ($row[$key] ?? '')) . '</td>';
        }
        $body .= '</tr>';
    }

    return "<table><thead><tr>{$header}</tr></thead><tbody>{$body}</tbody></table>";
}
