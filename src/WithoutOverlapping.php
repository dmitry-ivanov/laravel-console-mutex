<?php

namespace Illuminated\Console;

use Illuminated\Console\Overlapping\Mutex;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mutex = new Mutex($this);

        return parent::execute($input, $output);
    }

    public function getOverlappingStrategy()
    {
        return (isset($this->overlappingStrategy) ? $this->overlappingStrategy : 'file');
    }

    public function setOverlappingStrategy($strategy)
    {
        $this->overlappingStrategy = $strategy;
    }
}
