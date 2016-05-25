<?php

namespace Illuminated\Console\Overlapping;

class Preventer
{
    private $strategy;

    public function __construct($strategy)
    {
        switch ($strategy) {
            case 'database':
                $this->strategy = new DatabaseStrategy();
                break;

            case 'file':
            default:
                $this->strategy = new FileStrategy();
                break;
        }
    }
}
