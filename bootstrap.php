<?php

defined('VILOVEUL_WORKDIR') or define('VILOVEUL_WORKDIR', __DIR__);

// require composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// initialize application object with existing $container and $configs
$app = new Viloveul\Kernel\Application(
    new Viloveul\Container\Container(),
    new Viloveul\Config\Configuration([
        /* your app name */
        'name' => 'Viloveul',
        /* app version */
        'version' => '1.0',
        /* root application */
        'root' => __DIR__,
        /* base pathinfo on url */
        'basepath' => '/api/v1',
        /* uploader */
        'upload' => [
            /* target upload */
            'target' => __DIR__ . '/public/uploads',
        ],
        'auth' => [
            /* token name on header for jwt */
            'name' => 'Bearer',
            /* private key for generate jwt */
            'private' => __DIR__ . '/config/private.pem',
            /* public key for read jwt */
            'public' => __DIR__ . '/config/public.pem',
            /* phrase */
            'phrase' => 'something',
        ],
        /* db config (@see eloquent orm) */
        'db' => [
            'default' => [
                // database driver
                'driver' => 'mysql',
                // database host
                'host' => 'localhost',
                // database port
                'port' => 3306,
                'database' => 'viloveul',
                'username' => 'dev',
                'password' => 'something',
                'prefix' => 'tbl_',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
            ],
        ],
        'cache' => [
            /* ADAPTER (APCU|REDIS) */
            'adapter' => 'apcu',
            /* LIFETIME CACHE BEFORE DELETE */
            'lifetime' => 3600,
            /* CACHE PREFIX */
            'prefix' => 'viloveul_',
            /* CACHE HOST (REDIS) */
            'host' => '127.0.0.1',
            /* CACHE PORT (REDIS) */
            'port' => 6379,
        ],
        'commands' => [
            App\Command\HelloCommand::class,
        ],
    ])
);

/**
 * Load all routes
 */
$app->uses(function (Viloveul\Router\Contracts\Collection $router) {
    foreach (glob(__DIR__ . '/routes/*.php') as $file) {
        require $file;
    }
});

/**
 * Load all middlewares
 */
$app->uses(function (Viloveul\Kernel\Contracts\Middleware $middleware) {
    foreach (glob(__DIR__ . '/hooks/*.php') as $file) {
        require $file;
    }
});

return $app;
