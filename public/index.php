<?php

error_reporting(-1);

ini_set('display_errors', 'On');

try {

    $app = require __DIR__ . '/../bootstrap.php';

    $app->serve();

    $app->terminate(true);

} catch (Exception $e) {
    throw $e;
}
