<?php

namespace App;

use App\Middleware\Auth;
use Viloveul\Kernel\Application;
use Viloveul\Database\Contracts\Manager as Database;

class Kernel extends Application
{
    public function initialize()
    {
        // load database manager
        $this->uses(function (Database $db) {
            $db->load();
        });
        // make middleware for authenticaion
        $this->middleware(Auth::class);
    }
}
