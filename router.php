<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($path, '/actions/')) {
    $file = __DIR__ . $path;

    if (is_file($file)) {
        require $file;

        return true;
    }
}

return false;
