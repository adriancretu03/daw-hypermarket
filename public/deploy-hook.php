<?php

$secret = getenv('DEPLOY_SECRET');
$provided = $_SERVER['HTTP_X_DEPLOY_SECRET'] ?? '';

if ($secret === false || $secret === '' || !hash_equals($secret, $provided)) {
    http_response_code(403);
    exit('Forbidden');
}

$repoPath = '/home/acretuss/domains/acretu.daw.ssmr.ro/repo';
$command = 'cd ' . escapeshellarg($repoPath) . ' && git fetch origin main 2>&1 && git reset --hard origin/main 2>&1';

exec($command, $output, $exitCode);

header('Content-Type: text/plain');
http_response_code($exitCode === 0 ? 200 : 500);
echo implode("\n", $output);
