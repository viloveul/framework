<?php

namespace App\Message;

use Viloveul\Transport\Passenger;

class Mail extends Passenger
{
    public function handle(): void
    {
        $this->setAttribute('data', [
            'email' => 'fajrulaz@gmail.com',
            'subject' => 'Testing',
            'body' => 'dor aja',
        ]);
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
