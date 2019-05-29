<?php

define('VILOVEUL_START', microtime(true));

define('VILOVEUL_WORKDIR', __DIR__);

$_ENV['VILOVEUL_WORKDIR'] = VILOVEUL_WORKDIR;

// clear all timezone setting to UTC
date_default_timezone_set('UTC');

// require composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// load dot env variable
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$config = Viloveul\Config\ConfigFactory::load(__DIR__ . '/config/common.php');

// load commands
$commands = require_once __DIR__ . '/config/command.php';

// register commands
$config->set('commands', $commands, true);

// load components
$components = require_once __DIR__ . '/config/component.php';

// initialize container with several components
$container = Viloveul\Container\ContainerFactory::instance($components);

// initialize application object
$app = new App\Kernel($container, $config);

/**
 * Load all routes
 */
$app->uses(function (Viloveul\Router\Contracts\Collection $router) {
    foreach (glob(__DIR__ . '/route/*.php') as $file) {
        require $file;
    }
});

/**
 * Load all hooks
 */
$app->uses(function (Viloveul\Event\Contracts\Dispatcher $event) {
    foreach (glob(__DIR__ . '/hook/*.php') as $file) {
        require $file;
    }
});

return $app;
