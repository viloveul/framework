<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database extends Capsule
{
    /**
     * @return mixed
     */
    public function load()
    {
        return $this->bootEloquent();
    }
}
