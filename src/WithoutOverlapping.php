<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $strategy = $this->getOverlappingStrategy();

        return parent::execute($input, $output);
    }

    private function getOverlappingStrategy()
    {
        return (isset($this->overlappingStrategy) ? $this->overlappingStrategy : 'file');
    }
}
