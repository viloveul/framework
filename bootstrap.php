<?php

defined('VILOVEUL_WORKDIR') or define('VILOVEUL_WORKDIR', __DIR__);

// require composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// initialize container object
$container = Viloveul\Container\ContainerFactory::instance();

// setup configuration
$configuration = new Viloveul\Config\Configuration([
    /* your app name */
    'name' => 'Viloveul',
    /* app version */
    'version' => '1.0',
    /* root application */
    'root' => __DIR__,
    /* base pathinfo on url */
    'basepath' => '/',
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
    'transports' => [
        'default' => 'amqp://localhost:5672//'
    ],
    'commands' => [
        App\Command\HelloCommand::class,
    ],
]);

// initialize application object with existing $container and $configs
$app = new App\Kernel($container, $configuration);

/**
 * Register middleware
 */
$app->middleware(
    function ($request, $next) {
        return $next->handle($request);
    }
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
 * Load event listener
 */
$app->uses(function (Viloveul\Event\Contracts\Dispatcher $event) {
    foreach (glob(__DIR__ . '/hooks/*.php') as $file) {
        require $file;
    }
});

return $app;
