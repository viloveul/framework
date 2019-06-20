<?php

use Viloveul\Http\Contracts\ServerRequest;
use Viloveul\Config\Contracts\Configuration;

return [
    /*
    | REGISTER DATABASE
    | @see config/common.php
     */
    Viloveul\Database\Contracts\Manager::class => function (Configuration $config) {
        $charset = $config->get('db.charset');
        $collation = $config->get('db.collation');
        $connection = new Viloveul\MySql\Connection(
            $config->get('db.username'),
            $config->get('db.password'),
            $config->get('db.database'),
            $config->get('db.host'),
            $config->get('db.port'),
            $config->get('db.prefix'),
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}' COLLATE '{$collation}'"]
        );
        $man = Viloveul\Database\DatabaseFactory::instance();
        $man->addConnection($connection, 'default');
        return $man;
    },

    /*
    | REGISTER EVENT DISPATCHER
     */
    Viloveul\Event\Contracts\Dispatcher::class => function(Viloveul\Event\Contracts\Provider $provider) {
        return new Viloveul\Event\Dispatcher($provider);
    },

    /*
    | REGISTER EVENT PROVIDER
     */
    Viloveul\Event\Contracts\Provider::class => Viloveul\Event\Provider::class,

    /*
    | REGISTER MUTATOR MANAGER
     */
    Viloveul\Mutator\Contracts\Context::class => Viloveul\Mutator\Context::class,

    /*
    | REGISTER UPLOADER
    | @see config/common.php
     */
    Viloveul\Media\Contracts\Uploader::class => function (Configuration $config, ServerRequest $request) {
        return new Viloveul\Media\Uploader($request, $config->get('upload'));
    },

    /*
    | REGISTER TRAPORTATION
    | @see config/common.php
     */
    Viloveul\Transport\Contracts\Bus::class => function (Configuration $config) {
        $bus = new Viloveul\Transport\Bus();
        $bus->initialize();
        $bus->addConnection($config->get('transport') ?: [], 'default');
        return $bus;
    },

    /*
    | REGISTER AUTH
    | @see config/common.php
     */
    Viloveul\Auth\Contracts\Authentication::class => function (Configuration $config, ServerRequest $request) {
        $auth = new Viloveul\Auth\Authentication(
            $config->get('auth.phrase'),
            $request->getServer('HTTP_HOST')
        );
        $auth->setPrivateKey($config->get('auth.private'));
        $auth->setPublicKey($config->get('auth.public'));
        return $auth;
    },

    /*
    | REGISTER CACHE
    | @see config/common.php
     */
    Viloveul\Cache\Contracts\Cache::class => function (Configuration $config) {
        $adapter = $config->get('cache.adapter') ?: 'apcu';
        $lifetime = $config->get('cache.lifetime') ?: 3600;
        $prefix = $config->get('cache.prefix') ?: 'viloveul';
        if ('redis' === $adapter) {
            $host = $config->get('cache.host') ?: '127.0.0.1';
            $port = $config->get('cache.port') ?: 6379;
            $pass = $config->get('cache.pass') ?: null;
            $cache = new Viloveul\Cache\RedisAdapter($host, $port, $pass);
        } else {
            $cache = new Viloveul\Cache\ApcuAdapter();
        }
        $cache->setDefaultLifeTime($lifetime);
        $cache->setPrefix($prefix);
        return new Viloveul\Cache\Cache($cache);
    },

    /*
    | REGISTER LOGGER
     */
    Viloveul\Log\Contracts\Logger::class => function () {
        $logger = Viloveul\Log\LoggerFactory::instance();
        $logger->getCollection()->add(
            new Viloveul\Log\Provider\FileProvider(dirname(__DIR__))
        );
        return $logger;
    },

    /*
    | REGISTER PHPMAILER
    | @see config/common.php
     */
    PHPMailer\PHPMailer\PHPMailer::class => function (Configuration $config) {
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
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
    },
];
