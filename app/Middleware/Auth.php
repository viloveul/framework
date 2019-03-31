<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Viloveul\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Viloveul\Container\Contracts\ContainerAware;

class Auth implements MiddlewareInterface, ContainerAware
{
    use ContainerAwareTrait;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $next
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        return $next->handle($request);
    }
}
