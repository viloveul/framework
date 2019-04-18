<?php

namespace App\Command;

use Viloveul\Console\Command;

class HelloCommand extends Command
{
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
