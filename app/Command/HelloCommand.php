<?php

namespace App\Command;

use Viloveul\Console\Command;
use Viloveul\Container\ContainerAwareTrait;
use Viloveul\Container\Contracts\ContainerAware;
use Viloveul\Router\Contracts\Collection;

class HelloCommand extends Command implements ContainerAware
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'hello';

    public function handle()
    {
        $this->writeNormal('Hello World');
        $this->writeError('Hello World');
        $this->writeComment('Hello World');
        $this->writeInfo('Hello World');
        $this->writeQuestion('Hello World');
    }
}
