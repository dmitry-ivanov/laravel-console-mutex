<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    /**
     * Overwrite the console command initialization.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->initializeMutex();

        parent::initialize($input, $output);
    }

    /**
     * Initialize the mutex.
     *
     * @return void
     */
    protected function initializeMutex()
    {
        $mutex = new Mutex($this);

        $timeout = $this->getMutexTimeout();
        if (!$mutex->acquireLock($timeout)) {
            throw new MutexRuntimeException('Command is running now!');
        }

        register_shutdown_function([$this, 'releaseMutexLock'], $mutex);
    }

    /**
     * Get the mutex strategy.
     *
     * Currently supported: "file", "mysql", "redis" and "memcached".
     *
     * @return string
     */
    public function getMutexStrategy()
    {
        return property_exists($this, 'mutexStrategy')
            ? $this->mutexStrategy
            : 'file';
    }

    /**
     * Set the mutex strategy.
     *
     * Currently supported: "file", "mysql", "redis" and "memcached".
     *
     * @param string $strategy
     * @return void
     */
    public function setMutexStrategy($strategy)
    {
        $this->mutexStrategy = $strategy;
    }

    /**
     * Get the mutex timeout in milliseconds.
     *
     * Possible values:
     * `0` - check without waiting;
     * `{milliseconds}` - check, and wait for a maximum of milliseconds specified;
     * `null` - wait, till running command finish its execution;
     *
     * @return int|null
     */
    public function getMutexTimeout()
    {
        return property_exists($this, 'mutexTimeout')
            ? $this->mutexTimeout
            : 0;
    }

    /**
     * Set the mutex timeout in milliseconds.
     *
     * Possible values:
     * `0` - check without waiting;
     * `{milliseconds}` - check, and wait for a maximum of milliseconds specified;
     * `null` - wait, till running command finish its execution;
     *
     * @param int|null $timeout
     * @return void
     */
    public function setMutexTimeout($timeout)
    {
        $this->mutexTimeout = $timeout;
    }

    /**
     * Get the mutex name.
     *
     * @return string
     */
    public function getMutexName()
    {
        $name = $this->getName();
        $argumentsHash = md5(json_encode($this->argument()));

        return "icmutex-{$name}-{$argumentsHash}";
    }

    /**
     * Get the mutex file storage path.
     *
     * @return string
     */
    public function getMutexFileStorage()
    {
        return storage_path('app');
    }

    /**
     * Release the mutex lock.
     *
     * Called automatically, because it's registered as a shutdown function.
     *
     * @param \Illuminated\Console\Mutex $mutex
     * @return void
     */
    public function releaseMutexLock(Mutex $mutex)
    {
        $mutex->releaseLock();
    }
}
