<?php

namespace Illuminated\Console;

use Illuminate\Console\Command;

class Mutex
{
    private $command;
    private $strategy;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->strategy = $this->strategy();
    }

    private function strategy()
    {
        if (!empty($this->strategy)) {
            return $this->strategy;
        }

        switch ($this->command->getMutexStrategy()) {
            case 'mysql':
                break;

            case 'file':
            default:
                break;
        }
    }
}
