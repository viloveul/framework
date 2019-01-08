<?php

use Viloveul\Auth\Contracts\Authentication as IAuthentication;
use Viloveul\Kernel\Contracts\Configuration as IConfiguration;
use Viloveul\Http\Contracts\ServerRequest as IServerRequest;

try {

    $app = require __DIR__ . '/../bootstrap.php';

    $app->uses(function (IServerRequest $request, IConfiguration $configs, IAuthentication $auth) {
        [$name, $token] = sscanf($request->getServer('HTTP_AUTHORIZATION'), "%s %s");
        if ($configs->get('auth.name') === $name && !empty($token)) {
            $auth->setToken($token);
        }
    });

    $app->serve();

    $app->terminate(true);

} catch (Exception $e) {
    throw $e;
}
