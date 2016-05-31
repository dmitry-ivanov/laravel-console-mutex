<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    public function getMutexStrategy()
    {
        return (isset($this->mutexStrategy) ? $this->mutexStrategy : 'file');
    }

    public function setMutexStrategy($strategy)
    {
        $this->mutexStrategy = $strategy;
    }

    public function getMutexName()
    {
        $name = $this->getName();
        $arguments = json_encode($this->argument());
        return "icmutex-{$name}-" . md5($arguments);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mutex = new Mutex($this);
        if (!$mutex->acquireLock(0)) {
            $this->info('Command is running now!');
            return;
        }

        $code = parent::execute($input, $output);
        $mutex->releaseLock();

        return $code;
    }
}
