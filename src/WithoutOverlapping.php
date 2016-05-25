<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $strategy = $this->getOverlappingStrategy();
        switch ($strategy) {
            case 'database':
                //
                break;

            case 'file':
            default:
                //
                break;
        }

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
