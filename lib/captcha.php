<?php

function captcha_widget(): string
{
    $siteKey = htmlspecialchars(getenv('RECAPTCHA_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8');

    return '<div class="g-recaptcha" data-sitekey="' . $siteKey . '"></div>'
        . '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}

function recaptcha_http_post(string $url, array $params): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $body = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode((string) $body, true);

    return is_array($decoded) ? $decoded : ['success' => false];
}

function captcha_verify(string $response, ?callable $transport = null): bool
{
    if ($response === '') {
        return false;
    }

    $transport ??= 'recaptcha_http_post';
    $secret = getenv('RECAPTCHA_SECRET_KEY') ?: '';

    $result = $transport('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $secret,
        'response' => $response,
    ]);

    return $result['success'] ?? false;
}
