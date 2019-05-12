<?php

namespace App\Command;

use Viloveul\Console\Command;

class HelloCommand extends Command
{
    /**
     * @param string $name
     */
    public function __construct(string $name = 'say:hello')
    {
        parent::__construct($name);
    }

    public function handle()
    {
        $this->writeNormal('Halo Dunia');
        $this->writeError('Halo Dunia');
        $this->writeComment('Halo Dunia');
        $this->writeInfo('Halo Dunia');
        $this->writeQuestion('Halo Dunia');
    }
}
