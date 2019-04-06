<?php

namespace App;

use Closure;
use Exception;
use Viloveul\Cache\Cache;
use Viloveul\Http\Response;
use Viloveul\Transport\Bus;
use Viloveul\Media\Uploader;
use Viloveul\Console\Console;
use Viloveul\Middleware\Stack;
use Viloveul\Cache\ApcuAdapter;
use Viloveul\Cache\RedisAdapter;
use Viloveul\Auth\Authentication;
use PHPMailer\PHPMailer\PHPMailer;
use Viloveul\Router\NotFoundException;
use Viloveul\Cache\Contracts\Cache as ICache;
use Viloveul\Transport\Contracts\Bus as IBus;
use Viloveul\Event\Dispatcher as EventDispatcher;
use Viloveul\Http\Contracts\Response as IResponse;
use Viloveul\Router\Collection as RouteCollection;
use Viloveul\Router\Dispatcher as RouteDispatcher;
use Viloveul\Console\Contracts\Console as IConsole;
use Viloveul\Media\Contracts\Uploader as IUploader;
use Viloveul\Container\Contracts\Container as IContainer;
use Viloveul\Http\Server\RequestFactory as RequestFactory;
use Viloveul\Middleware\Collection as MiddlewareCollection;
use Viloveul\Event\Contracts\Dispatcher as IEventDispatcher;
use Viloveul\Http\Contracts\ServerRequest as IServerRequest;
use Viloveul\Router\Contracts\Collection as IRouteCollection;
use Viloveul\Router\Contracts\Dispatcher as IRouteDispatcher;
use Viloveul\Auth\Contracts\Authentication as IAuthentication;
use Viloveul\Config\Contracts\Configuration as IConfiguration;
use Viloveul\Middleware\Contracts\Collection as IMiddlewareCollection;

class Kernel
{
    /**
     * @var mixed
     */
    protected $container = null;

    /**
     * @param  IContainer     $container
     * @param  IConfiguration $config
     * @return mixed
     */
    public function __construct(IContainer $container, IConfiguration $config)
    {
        $this->container = $container;

        $this->container->set(IResponse::class, Response::class);

        $this->container->set(IRouteCollection::class, RouteCollection::class);

        $this->container->set(IEventDispatcher::class, EventDispatcher::class);

        $this->container->set(IMiddlewareCollection::class, MiddlewareCollection::class);

        $this->container->set(IConfiguration::class, function () use ($config) {
            return $config;
        });

        $this->container->set(IServerRequest::class, function () {
            return RequestFactory::fromGlobals();
        });

        $this->container->set(IUploader::class, function (IConfiguration $config, IServerRequest $request) {
            return new Uploader($request, $config->get('upload'));
        });

        $this->container->set(IRouteDispatcher::class, function (IConfiguration $config, IRouteCollection $routes) {
            $router = new RouteDispatcher($routes);
            $router->setBase($config->get('basepath') ?: '/');
            return $router;
        });

        $this->container->set(Database::class, function (IConfiguration $config) {
            $capsule = new Database();
            $capsule->initialize();
            $capsule->addConnection($config->get('db') ?: [], 'default');
            return $capsule;
        });

        $this->container->set(IBus::class, function (IConfiguration $config) {
            $bus = new Bus();
            $bus->initialize();
            $bus->addConnection($config->get('transport') ?: [], 'default');
            return $bus;
        });

        $this->container->set(IAuthentication::class, function (IConfiguration $config, IServerRequest $request) {
            $auth = new Authentication(
                $config->get('auth.phrase'),
                $request->getServer('HTTP_HOST')
            );
            $auth->setPrivateKey($config->get('auth.private'));
            $auth->setPublicKey($config->get('auth.public'));
            return $auth;
        });

        $this->container->set(ICache::class, function (IConfiguration $config) {
            $adapter = $config->get('cache.adapter') ?: 'apcu';
            $lifetime = $config->get('cache.lifetime') ?: 3600;
            $prefix = $config->get('cache.prefix') ?: 'viloveul';
            if ('redis' === $adapter) {
                $host = $config->get('cache.host') ?: '127.0.0.1';
                $port = $config->get('cache.port') ?: 6379;
                $pass = $config->get('cache.pass') ?: null;
                $cache = new RedisAdapter($host, $port, $pass);
            } else {
                $cache = new ApcuAdapter();
            }
            $cache->setDefaultLifeTime($lifetime);
            $cache->setPrefix($prefix);
            return new Cache($cache);
        });

        $this->container->set(PHPMailer::class, function (IConfiguration $config) {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->isHTML(true);
            $mailer->SMTPAuth = true;
            $mailer->SMTPSecure = $config->get('smtpmail.secure');
            $mailer->Host = $config->get('smtpmail.host');
            $mailer->Username = $config->get('smtpmail.username');
            $mailer->Password = $config->get('smtpmail.password');
            $mailer->Port = $config->get('smtpmail.port');
            $mailer->setFrom(
                $config->get('smtpmail.username'),
                $config->get('smtpmail.name')
            );
            return $mailer;
        });
    }

    /**
     * @return mixed
     */
    public function console(): IConsole
    {
        $console = $this->container->make(Console::class);
        $console->boot();

        return $console;
    }

    /**
     * @param $middleware
     */
    public function middleware($middleware): void
    {
        if (is_array($middleware) && !is_callable($middleware)) {
            foreach ($middleware as $value) {
                $this->container->get(IMiddlewareCollection::class)->add($value);
            }
        } else {
            $this->middleware([$middleware]);
        }
    }

    /**
     * @return mixed
     */
    public function serve(): void
    {
        try {
            $request = $this->container->get(IServerRequest::class);
            $router = $this->container->get(IRouteDispatcher::class);
            $uri = $request->getUri();
            $router->dispatch($request->getMethod(), $uri->getPath());
            $route = $router->routed();
            $stack = new Stack(
                $this->makeController(
                    $route->getHandler(),
                    $route->getParams()
                ),
                $this->container->get(IMiddlewareCollection::class)
            );
            $response = $stack->handle($request);
        } catch (NotFoundException $e404) {
            $response = $this->container->get(IResponse::class)->withErrors(
                IResponse::STATUS_NOT_FOUND,
                ['404 Page Not Found']
            );
        } catch (Exception $e) {
            $response = $this->container->get(IResponse::class)->withErrors(
                IResponse::STATUS_PRECONDITION_FAILED,
                [$e->getMessage()]
            );
        }
        $response->send();
    }

    /**
     * @param $exit
     */
    public function terminate($exit = false): void
    {
        try {
            $db = $this->container->get(Database::class);
            $db->getConnection('default')->disconnect();
        } catch (Exception $e) {
            // keep silent
        }
        if (true === $exit) {
            exit(1);
        }
    }

    /**
     * @param Closure $handler
     */
    public function uses(Closure $handler): void
    {
        $this->container->invoke($handler);
    }

    /**
     * @return mixed
     */
    protected function makeController($handler, $params)
    {
        return function (IServerRequest $request) use ($handler, $params) {
            if (is_callable($handler) && !is_scalar($handler)) {
                if (is_array($handler) && !is_object($handler[0])) {
                    $result = $this->container->invoke([
                        $this->container->make($handler[0]), $handler[1],
                    ], $params);
                } else {
                    $result = $this->container->invoke($handler, $params);
                }
            } else {
                if (is_scalar($handler) && strpos($handler, '::') === false && is_callable($handler)) {
                    $result = $this->container->invoke($handler, $params);
                } else {
                    if (is_scalar($handler)) {
                        $parts = explode('::', $handler);
                    } else {
                        $parts = (array) $handler;
                    }
                    $class = array_shift($parts);
                    $action = isset($parts[0]) ? $parts[0] : 'handle';
                    $object = is_string($class) ? $this->container->make($class) : $class;
                    $result = $this->container->invoke([$object, $action], $params);
                }
            }
            if ($result instanceof IResponse) {
                return $result;
            } else {
                return $this->container->get(IResponse::class)
                    ->setStatus(IResponse::STATUS_OK)
                    ->withPayload([
                        'data' => $result,
                    ]);
            }
        };
    }
}
