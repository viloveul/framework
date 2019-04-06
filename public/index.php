<?php

error_reporting(0);

ini_set('display_errors', 'Off');

try {

    header('Access-Control-Allow-Origin: *');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, HEAD');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Request-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Request-Method: GET, POST, PUT, PATCH, DELETE, HEAD');
        return true;
    }

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
