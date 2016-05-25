<?php

namespace Illuminated\Console\Overlapping;

use Illuminate\Console\Command;

class Preventer
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

        switch ($this->command->getOverlappingStrategy()) {
            case 'database':
                return new DatabaseStrategy();

            case 'file':
            default:
                return new FileStrategy();
        }
    }
}
