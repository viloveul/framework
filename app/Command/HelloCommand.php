<?php

namespace App\Command;

use Viloveul\Console\Command;
use Viloveul\Container\ContainerAwareTrait;
use Viloveul\Container\Contracts\ContainerAware;

class HelloCommand extends Command implements ContainerAware
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'say:hello';

    public function handle()
    {
        $this->writeNormal('Halo Dunia');
        $this->writeError('Halo Dunia');
        $this->writeComment('Halo Dunia');
        $this->writeInfo('Halo Dunia');
        $this->writeQuestion('Halo Dunia');
    }
}
