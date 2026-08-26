<?php

function http_get(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Hypermarket/1.0');
    $body = curl_exec($ch);
    curl_close($ch);

    return $body === false ? null : $body;
}

function parse_eur_rate(string $json): ?float
{
    $decoded = json_decode($json, true);

    if (!is_array($decoded) || !isset($decoded['rates']['EUR'])) {
        return null;
    }

    return (float) $decoded['rates']['EUR'];
}

function fetch_eur_rate(?callable $transport = null): ?float
{
    $transport ??= 'http_get';
    $body = $transport('https://open.er-api.com/v6/latest/RON');

    if ($body === null) {
        return null;
    }

    return parse_eur_rate($body);
}

function cached_eur_rate(string $cacheFile, int $ttlSeconds, ?callable $transport = null): ?float
{
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttlSeconds) {
        $cached = file_get_contents($cacheFile);

        return $cached === false || $cached === '' ? null : (float) $cached;
    }

    $rate = fetch_eur_rate($transport);

    if ($rate !== null) {
        $dir = dirname($cacheFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($cacheFile, (string) $rate);
    }

    return $rate;
}
