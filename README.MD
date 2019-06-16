<h1 align="center">Viloveul Framework</h1>
<p align="center"><img src="viloveul.png"></p>

<p align="center">
<a href="https://travis-ci.com/viloveul/project-framework">
<img src="https://travis-ci.com/viloveul/project-framework.svg" alt="Build Status">
</a>
<a href="https://packagist.org/packages/viloveul/project-framework">
<img src="https://poser.pugx.org/viloveul/project-framework/d/total" alt="Total Downloads">
</a>
<a href="https://packagist.org/packages/viloveul/project-framework">
<img src="https://poser.pugx.org/viloveul/project-framework/v/stable" alt="Latest Stable Version">
</a>
</p>

## 1. Requirement
- PHP7 (direkomendasikan 7.3)
- Composer
- OpenSSL
- ext: php-amqp (Optional)
- ext: php-redis (Optional)
- ext: php-apcu (Optional)

## 2. Installation
Instalasi menggunakan composer :
```shell
composer create-project viloveul/project-framework direktori-project-anda
cd direktori-project-anda
```
Instalasi dengan git clone :
```shell
git clone https://github.com/viloveul/project-framework.git direktori-project-anda
cd direktori-project-anda
composer install
composer run fresh
composer run bootstrap
composer run genrsakey
```

## 3. PREVIEW

### 3.1. Check Command

hello command :
```shell
sbin/viloveulc say:hello
```

### 3.2. Check HTTP

Run development server with php built-in server
```shell
php -S localhost:19911 -t public
```

@see http://localhost:19911

### 3.3 Check worker

run rabbitmq-server under docker (broker message)
```
docker run --rm -p 5672:5672 rabbitmq:3
```

edit your .env file for email :
```
VILOVEUL_SMTP_HOST=
VILOVEUL_SMTP_PORT=
VILOVEUL_SMTP_NAME=
VILOVEUL_SMTP_SECURE=
VILOVEUL_SMTP_USERNAME=
VILOVEUL_SMTP_PASSWORD=
```

install dependencies for worker
```
npm install
```
Go Action

> Anda perlu menjalankan dalam 2 terminal/console untuk melihatnya:

1: Jalankan worker
```
sbin/workerc
```

2: Kirim data ke worker
```
sbin/viloveulc mail:test
```
## Dependencies (No-Dev, Recursive)
- enqueue/amqp-lib
- enqueue/amqp-tools
- enqueue/dsn
- lcobucci/jwt
- paragonie/random_compat
- php-amqplib/php-amqplib
- psr/container
- psr/http-factory
- psr/http-message
- psr/http-server-handler
- psr/http-server-middleware
- psr/log
- psr/simple-cache
- queue-interop/amqp-interop
- queue-interop/queue-interop
- ramsey/uuid
- symfony/console
- symfony/contracts
- symfony/polyfill-ctype
- symfony/polyfill-mbstring
- symfony/polyfill-php72
- symfony/var-dumper
- viloveul/auth
- viloveul/cache
- viloveul/config
- viloveul/console
- viloveul/container
- viloveul/database
- viloveul/event
- viloveul/http
- viloveul/kernel
- viloveul/log
- viloveul/media
- viloveul/middleware
- viloveul/mysql
- viloveul/pagination
- viloveul/router
- viloveul/support
- viloveul/transport
- viloveul/validation
- vlucas/phpdotenv
- vlucas/valitron
- zendframework/zend-diactoros
