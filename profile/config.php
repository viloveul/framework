<?php

return [
    # application name
    'name' => 'VILOVEUL FRAMEWORK',
    # version
    'version' => '1.0',
    # root application
    'root' => dirname(__DIR__),
    # base pathinfo on url
    'basepath' => '/',
    # uploader
    'upload' => [
        # target upload
        'target' => dirname(__DIR__) . '/public/uploads',
        'baseurl' => env('VILOVEUL_BASEURL') . '/uploads',
    ],
    'auth' => [
        # token name on header for jwt
        'name' => env('VILOVEUL_AUTH_NAME', 'Bearer'),
        # private key for generate jwt
        'private' => env('VILOVEUL_AUTH_PRIVATE_KEY', realpath(dirname(__DIR__) . '/var/private.pem')),
        # public key for read jwt
        'public' => env('VILOVEUL_AUTH_PUBLIC_KEY', realpath(dirname(__DIR__) . '/var/public.pem')),
        # phrase
        'phrase' => env('VILOVEUL_AUTH_PASSPHRASE', 'viloveul'),
    ],
    'db' => [
        'host' => env('VILOVEUL_DB_HOST', 'localhost'),
        'port' => env('VILOVEUL_DB_PORT', 3306),
        'database' => env('VILOVEUL_DB_NAME', 'viloveul_framework'),
        'username' => env('VILOVEUL_DB_USERNAME', 'viloveul'),
        'password' => env('VILOVEUL_DB_PASSWD', 'viloveul'),
        'prefix' => env('VILOVEUL_DB_PREFIX', 'tbl_'),
        'charset' => env('VILOVEUL_DB_CHARSET', 'utf8'),
        'collation' => env('VILOVEUL_DB_COLLATION', 'utf8_general_ci'),
    ],
    'cache' => [
        /* ADAPTER (APCU|REDIS) */
        'adapter' => env('VILOVEUL_CACHE_ADAPTER', 'apcu'),
        /* LIFETIME CACHE BEFORE DELETE */
        'lifetime' => env('VILOVEUL_CACHE_LIFETIME', 3600),
        /* CACHE PREFIX */
        'prefix' => env('VILOVEUL_CACHE_PREFIX', 'viloveul_'),
        /* CACHE HOST (REDIS) */
        'host' => env('VILOVEUL_CACHE_HOST', '127.0.0.1'),
        /* CACHE PORT (REDIS) */
        'port' => env('VILOVEUL_CACHE_PORT', 6379),
        /* CACHE PASS (REDIS) */
        'pass' => env('VILOVEUL_CACHE_PASS', null),
    ],
    'smtpmail' => [
        'host' => env('VILOVEUL_SMTP_HOST', 'your.server.com'),
        'port' => env('VILOVEUL_SMTP_PORT', 465),
        'name' => env('VILOVEUL_SMTP_NAME', 'Viloveul'),
        'secure' => env('VILOVEUL_SMTP_SECURE', 'ssl'),
        'username' => env('VILOVEUL_SMTP_USERNAME', 'your@server.com'),
        'password' => env('VILOVEUL_SMTP_PASSWORD', 'yourP@sSw0rd.'),
    ],
    'transport' => env('VILOVEUL_BROKER_DSN', 'amqp://localhost:5672/%2f'),
    'commands' => [
        App\Command\HelloCommand::class,
        App\Command\MailCommand::class,
    ],
];
