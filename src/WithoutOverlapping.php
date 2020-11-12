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
        if (!$mutex->acquireLock($this->getMutexTimeout())) {
            throw new MutexRuntimeException('Command is running now!');
        }

        register_shutdown_function([$this, 'releaseMutexLock'], $mutex);
    }

    public function getMutexStrategy()
    {
        return property_exists($this, 'mutexStrategy') ? $this->mutexStrategy : 'file';
    }

    public function setMutexStrategy($strategy)
    {
        $this->mutexStrategy = $strategy;
    }

    public function getMutexTimeout()
    {
        return property_exists($this, 'mutexTimeout') ? $this->mutexTimeout : 0;
    }

    public function setMutexTimeout($timeout)
    {
        $this->mutexTimeout = $timeout;
    }

    public function getMutexName()
    {
        $name = $this->getName();
        $arguments = json_encode($this->argument());
        return "icmutex-{$name}-" . md5($arguments);
    }

    public function getMutexFileStorage()
    {
        return storage_path('app');
    }

    public function releaseMutexLock(Mutex $mutex)
    {
        $mutex->releaseLock();
    }
}
