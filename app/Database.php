<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database extends Capsule
{
    /**
     * @return mixed
     */
    public function initialize()
    {
        return $this->bootEloquent();
    }
}
