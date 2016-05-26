<?php

namespace Illuminated\Console;

use Illuminate\Console\Command;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Mutex as Ninja;
use NinjaMutex\MutexException;

class Mutex
{
    private $command;
    private $strategy;
    private $ninja;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->strategy = $this->strategy();
        $this->ninja = new Ninja('test', $this->strategy);
    }

    private function strategy()
    {
        if (!empty($this->strategy)) {
            return $this->strategy;
        }

        switch ($this->command->getMutexStrategy()) {
            case 'mysql':
                throw new MutexException('Strategy `mysql` is not implemented yet.');

            case 'redis':
                throw new MutexException('Strategy `redis` is not implemented yet.');

            case 'memcache':
                throw new MutexException('Strategy `memcache` is not implemented yet.');

            case 'memcached':
                throw new MutexException('Strategy `memcached` is not implemented yet.');

            case 'file':
            default:
                return new FlockLock(storage_path('framework'));
        }
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ninja, $method], $parameters);
    }
}
