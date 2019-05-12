<?php

namespace App\Command;

use App\Message\Mail;
use Viloveul\Console\Command;
use Viloveul\Container\ContainerAwareTrait;
use Viloveul\Transport\Contracts\Bus as IBus;
use Viloveul\Container\Contracts\ContainerAware;

class MailCommand extends Command implements ContainerAware
{
    use ContainerAwareTrait;

    /**
     * @param string $name
     */
    public function __construct(string $name = 'mail:test')
    {
        parent::__construct($name);
    }

    public function handle()
    {
        $this->getContainer()->get(IBus::class)->process(new Mail());
    }
}
