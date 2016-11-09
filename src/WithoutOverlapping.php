<?php

namespace Illuminated\Console;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->initializeMutex();

        parent::initialize($input, $output);
    }

    protected function initializeMutex()
    {
        $mutex = new Mutex($this);
        if (!$mutex->acquireLock(0)) {
            throw new RuntimeException('Command is running now!');
        }

        register_shutdown_function(function () use ($mutex) {
            $mutex->releaseLock();
        });
    }

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
}
