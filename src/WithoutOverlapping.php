<?php

namespace Illuminated\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait WithoutOverlapping
{
    /**
     * Overwrite the console command initialization.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->initializeMutex();

        parent::initialize($input, $output);
    }

    /**
     * Initialize the mutex.
     */
    protected function initializeMutex(): void
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
     */
    public function getMutexStrategy(): string
    {
        return property_exists($this, 'mutexStrategy')
            ? $this->mutexStrategy
            : 'file';
    }

    /**
     * Set the mutex strategy.
     *
     * Currently supported: "file", "mysql", "redis" and "memcached".
     */
    public function setMutexStrategy(string $strategy): void
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
     */
    public function getMutexTimeout(): int|null
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
     */
    public function setMutexTimeout(int|null $timeout): void
    {
        $this->mutexTimeout = $timeout;
    }

    /**
     * Get the mutex name.
     */
    public function getMutexName(): string
    {
        $name = $this->getName();
        $argumentsHash = md5(json_encode($this->argument()));

        return "icmutex-{$name}-{$argumentsHash}";
    }

    /**
     * Get the mutex file storage path.
     */
    public function getMutexFileStorage(): string
    {
        return storage_path('app');
    }

    /**
     * Release the mutex lock.
     *
     * Called automatically, because it's registered as a shutdown function.
     */
    public function releaseMutexLock(Mutex $mutex): void
    {
        $mutex->releaseLock();
    }
}
