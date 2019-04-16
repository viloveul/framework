<?php

$router->add(
    new Viloveul\Router\Route('GET /', [
        App\Controller\HomeController::class, 'index',
    ])
);
