<?php

namespace App;

use App\Middleware\Auth;
use Viloveul\Kernel\Application;

class Kernel extends Application
{
    public function initialize()
    {
        $this->middleware(Auth::class);
    }
}
