<?php

$router->add(
    'home',
    new Viloveul\Router\Route('GET /', [
        App\Controller\HomeController::class, 'index',
    ])
);
