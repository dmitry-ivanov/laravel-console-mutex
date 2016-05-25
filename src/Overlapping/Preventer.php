<?php

namespace Illuminated\Console\Overlapping;

class Preventer
{
    private $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }
}
