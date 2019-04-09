<?php

error_reporting(0);

ini_set('display_errors', 'Off');

header('Access-Control-Allow-Origin: *');

try {

    $app = require __DIR__ . '/../bootstrap.php';
    $app->serve();

    $app->terminate();

} catch (Throwable $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    header('Content-Type: application/json');
    echo json_encode([
        'errors' => [
            [
                'source' => [
                    'pointer' => $e->getFile() . ':' . $e->getLine(),
                ],
                'detail' => $e->getMessage(),
            ],
        ],
    ]);
}
