<?php

return [
    # root application
    'root' => dirname(__DIR__),
    # base pathinfo on url
    'basepath' => env('BASEPATH', '/api/v1'),
    # uploader
    'upload' => [
        # target upload
        'target' => env('UPLOAD_TARGET', dirname(__DIR__) . '/public/uploads'),
    ],
    'auth' => [
        # token name on header for jwt
        'name' => env('AUTH_NAME', 'Bearer'),
        # private key for generate jwt
        'private' => env('AUTH_PRIVATE_KEY', __DIR__ . '/private.pem'),
        # public key for read jwt
        'public' => env('AUTH_PRIVATE_KEY', __DIR__ . '/public.pem'),
        # phrase
        'phrase' => env('AUTH_PASSPHRASE', 'something'),
    ],
    # db config (@see eloquent orm)
    'db' => [
        'viloveul' => [
            // database driver
            'driver' => env('DB_DRIVER', 'mysql'),
            // database host
            'host' => env('DB_HOST', 'localhost'),
            // database port
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_NAME', 'viloveul'),
            'username' => env('DB_USERNAME', 'dev'),
            'password' => env('DB_PASSWD', 'something'),
            'prefix' => env('DB_PREFIX', 'tbl_'),
            'charset' => env('DB_CHARSET', 'utf8'),
            'collation' => env('DB_COLLATION', 'utf8_general_ci'),
        ],
    ],
    'cache' => [
        /* ADAPTER (APCU|REDIS) */
        'adapter' => env('CACHE_ADAPTER', 'apcu'),
        /* LIFETIME CACHE BEFORE DELETE */
        'lifetime' => env('CACHE_LIFETIME', 3600),
        /* CACHE PREFIX */
        'prefix' => 'viloveul_',
        /* CACHE HOST (REDIS) */
        'host' => env('CACHE_HOST', '127.0.0.1'),
        /* CACHE PORT (REDIS) */
        'port' => env('CACHE_PORT', 6379),
    ],
];
