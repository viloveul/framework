<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    /**
     * @var mixed
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $connection = 'viloveul';
}
