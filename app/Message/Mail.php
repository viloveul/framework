<?php

namespace App\Message;

use Viloveul\Transport\Passenger;

class Mail extends Passenger
{
    public function handle(): void
    {
        $this->setAttribute('email', 'fajrulaz@gmail.com');
        $this->setAttribute('subject', 'Testing');
        $this->setAttribute('body', 'Testing doang');
    }

    public function point(): string
    {
        return 'viloveul.system.worker';
    }

    public function task(): string
    {
        return 'system.email';
    }
}
