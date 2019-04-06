<?php

use Viloveul\Http\Contracts\ServerRequest;
use Viloveul\Config\Contracts\Configuration;

return [
    /*
    | REGISTER DATABASE
    | @see config/main.php
     */
    App\Database::class => function (Configuration $config) {
        $db = new App\Database();
        $db->initialize();
        $db->addConnection($config->get('db') ?: [], 'default');
        return $db;
    },

    /*
    | REGISTER EVENT
     */
    Viloveul\Event\Contracts\Dispatcher::class => [
        'class' => Viloveul\Event\Dispatcher::class,
    ],

    /*
    | REGISTER UPLOADER
    | @see config/main.php
     */
    Viloveul\Media\Contracts\Uploader::class => function (Configuration $config, ServerRequest $request) {
        return new Viloveul\Media\Uploader($request, $config->get('upload'));
    },

    /*
    | REGISTER TRAPORTATION
    | @see config/main.php
     */
    Viloveul\Transport\Contracts\Bus::class => function (Configuration $config) {
        $bus = new Viloveul\Transport\Bus();
        $bus->initialize();
        $bus->addConnection($config->get('transport') ?: [], 'default');
        return $bus;
    },

    /*
    | REGISTER AUTH
    | @see config/main.php
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
    | @see config/main.php
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
    | REGISTER PHPMAILER
    | @see config/main.php
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
