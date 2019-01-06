<?php

error_reporting(-1);

ini_set('display_errors', 'On');

// require composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// load dot env variable
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// load configuration data
$configs = require __DIR__ . '/config/main.php';

// initialize application object with existing $container and $configs
$app = new Viloveul\Kernel\Application($container, $configs);

/**
 * Load all routes
 */
$app->prepare(function (Viloveul\Router\Contracts\Collection $router) {
    foreach (glob(__DIR__ . '/routes/*.php') as $file) {
        require $file;
    }
});

/**
 * Load all middlewares
 */
$app->prepare(function (Viloveul\Kernel\Contracts\Middleware $middleware) {
    foreach (glob(__DIR__ . '/hooks/*.php') as $file) {
        require $file;
    }
});

return $app;
