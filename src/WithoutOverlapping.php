<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mutex = new Mutex($this);

        return parent::execute($input, $output);
    }

    public function getMutexStrategy()
    {
        return (isset($this->mutexStrategy) ? $this->mutexStrategy : 'file');
    }

    public function setMutexStrategy($strategy)
    {
        $this->mutexStrategy = $strategy;
    }
}
