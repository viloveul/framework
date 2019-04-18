<?php

namespace App\Entity;

use Viloveul\Database\Model;

class User extends Model
{
    public function relations(): array
    {
        return [];
    }

    public function table(): string
    {
        return '{{ user }}';
    }
}
