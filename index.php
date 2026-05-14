<?php

declare(strict_types=1);

$indexFile = __DIR__ . '/index.html';
$assetFiles = [
    $indexFile,
    __DIR__ . '/assets/css/style.css',
    __DIR__ . '/assets/js/main.js',
    __DIR__ . '/assets/img/logo.png',
    __DIR__ . '/assets/img/favicon.png',
    __DIR__ . '/assets/img/apple-touch-icon.png',
];

$assetVersion = 0;
foreach ($assetFiles as $assetFile) {
    if (is_file($assetFile)) {
        $assetVersion = max($assetVersion, (int) filemtime($assetFile));
    }
}

if ($assetVersion === 0) {
    $assetVersion = time();
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$html = file_get_contents($indexFile);

if ($html === false) {
    http_response_code(500);
    echo 'Unable to load site content.';
    exit;
}

$html = preg_replace_callback(
    '/\b(href|src)=("|\")(assets\/[^"\']+?)(?:\?v=\d+)?\2/',
    static function (array $matches) use ($assetVersion): string {
        $attribute = $matches[1];
        $quote = $matches[2];
        $url = $matches[3];

        return $attribute . '=' . $quote . $url . '?v=' . $assetVersion . $quote;
    },
    $html
);

echo $html;