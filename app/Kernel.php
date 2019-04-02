<?php

namespace App;

use Closure;
use Exception;
use Viloveul\Cache\Cache;
use Viloveul\Router\Route;
use Viloveul\Http\Response;
use Viloveul\Transport\Bus;
use Viloveul\Media\Uploader;
use Viloveul\Console\Console;
use Viloveul\Middleware\Stack;
use Viloveul\Cache\ApcuAdapter;
use Viloveul\Cache\RedisAdapter;
use Viloveul\Auth\Authentication;
use PHPMailer\PHPMailer\PHPMailer;
use Viloveul\Config\Configuration;
use Viloveul\Router\NotFoundException;
use Viloveul\Container\ContainerFactory;
use Psr\Http\Message\UriInterface as IUri;
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
    public function __construct(IContainer $container = null, IConfiguration $config = null)
    {
        $this->container = $container ?: ContainerFactory::instance();

        $this->container->set(IConfiguration::class, function () use ($config) {
            return null === $config ? new Configuration() : $config;
        });

        $this->container->set(IRouteCollection::class, RouteCollection::class);

        $this->container->set(IEventDispatcher::class, EventDispatcher::class);

        $this->container->set(IMiddlewareCollection::class, MiddlewareCollection::class);

        $this->container->set(ICache::class, function (IConfiguration $config) {
            $adapter = array_get($config->all(), 'cache.adapter') ?: 'apcu';
            $lifetime = array_get($config->all(), 'cache.lifetime') ?: 3600;
            $prefix = array_get($config->all(), 'cache.prefix') ?: 'viloveul';
            if ('redis' === $adapter) {
                $host = array_get($config->all(), 'cache.host') ?: '127.0.0.1';
                $port = array_get($config->all(), 'cache.port') ?: 6379;
                $pass = array_get($config->all(), 'cache.pass') ?: null;
                $cache = new RedisAdapter($host, $port, $pass);
            } else {
                $cache = new ApcuAdapter();
            }
            $cache->setDefaultLifeTime($lifetime);
            $cache->setPrefix($prefix);

            return new Cache($cache);
        });

        $this->container->set(IRouteDispatcher::class, function (IConfiguration $config, IRouteCollection $routes) {
            $router = new RouteDispatcher($routes);
            $router->setBase($config->get('basepath') ?: '/');

            return $router;
        });

        $this->container->set(Database::class, function (IConfiguration $config) {
            $capsule = new Database();
            $capsule->addConnection($config->get('db') ?: [], 'default');
            return $capsule;
        });

        $this->container->set(IBus::class, function (IConfiguration $config) {
            $bus = new Bus();
            $bus->initialize();
            $bus->addConnection($config->get('transport') ?: [], 'default');
            return $bus;
        });

        $this->container->set(IServerRequest::class, function () {
            return RequestFactory::fromGlobals();
        });

        $this->container->set(IUri::class, function (IServerRequest $request) {
            return $request->getUri();
        });

        $this->container->set(IResponse::class, Response::class);

        $this->container->set(IAuthentication::class, function (IConfiguration $config, IServerRequest $request) {
            $auth = new Authentication(
                array_get($config->all(), 'auth.phrase'),
                $request->getServer('HTTP_HOST')
            );
            $auth->setPrivateKey(array_get($config->all(), 'auth.private'));
            $auth->setPublicKey(array_get($config->all(), 'auth.public'));

            return $auth;
        });

        $this->container->set(IUploader::class, function (IServerRequest $request, IConfiguration $config) {
            return new Uploader($request, $config->get('upload'));
        });

        $this->container->set(IConsole::class, Console::class);

        $this->container->set(PHPMailer::class, function (IConfiguration $config) {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->isHTML(true);
            $mailer->SMTPAuth = true;
            $mailer->SMTPSecure = array_get($config->all(), 'smtpmail.secure');
            $mailer->Host = array_get($config->all(), 'smtpmail.host');
            $mailer->Username = array_get($config->all(), 'smtpmail.username');
            $mailer->Password = array_get($config->all(), 'smtpmail.password');
            $mailer->Port = array_get($config->all(), 'smtpmail.port');
            $mailer->setFrom(
                array_get($config->all(), 'smtpmail.username'),
                array_get($config->all(), 'smtpmail.name')
            );

            return $mailer;
        });
    }

    /**
     * @return mixed
     */
    public function console(): IConsole
    {
        $this->container->get(Database::class)->load();
        $console = $this->container->get(IConsole::class);
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
     * @param string   $name
     * @param string   $pattern
     * @param $param
     */
    public function route(string $name, string $pattern, $param = null): void
    {
        $route = new Route($pattern, $param);
        $this->container->get(IRouteCollection::class)->add($name, $route);
    }

    /**
     * @return mixed
     */
    public function serve(): void
    {
        try {
            $this->container->get(Database::class)->load();
            $request = $this->container->get(IServerRequest::class);
            $uri = $this->container->get(IUri::class);
            $router = $this->container->get(IRouteDispatcher::class);
            $router->dispatch($request->getMethod(), $uri->getPath());
            $route = $router->routed();
            $this->middleware($route->getMiddlewares());
            $controller = $this->makeController();
            $middlewares = $this->container->get(IMiddlewareCollection::class);
            if (count($middlewares->all()) > 0) {
                $stack = new Stack($this->makeController(), $middlewares);
                $response = $stack->handle($request);
            } else {
                $response = $controller();
            }
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
    protected function makeController()
    {
        return function (IServerRequest $request) {
            $route = $this->container->get(IRouteDispatcher::class)->routed();
            $handler = $route->getHandler();
            $params = $route->getParams();

            if (is_callable($handler) && !is_scalar($handler)) {
                if (is_array($handler) && !is_object($handler[0])) {
                    $result = $this->container->invoke([
                        $this->container->make($handler[0]), $handler[1],
                    ], $params);
                } else {
                    $result = $this->container->invoke($handler, $params);
                }
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
