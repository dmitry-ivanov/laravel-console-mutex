<?php

namespace Illuminated\Console;

use Illuminate\Console\Command;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PredisRedisLock;
use NinjaMutex\Mutex as Ninja;
use NinjaMutex\MutexException;
use Redis;

class Mutex
{
    private $command;
    private $strategy;
    private $ninja;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->strategy = $this->strategy();
        $this->ninja = new Ninja($command->getMutexName(), $this->strategy);
    }

    private function strategy()
    {
        if (!empty($this->strategy)) {
            return $this->strategy;
        }

        switch ($this->command->getMutexStrategy()) {
            case 'mysql':
                return new MySqlLock(env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_HOST'));

            case 'redis':
                return new PredisRedisLock(Redis::connection());

            case 'memcache':
                throw new MutexException('Strategy `memcache` is not implemented yet.');

            case 'memcached':
                throw new MutexException('Strategy `memcached` is not implemented yet.');

            case 'file':
            default:
                return new FlockLock(storage_path('app'));
        }
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ninja, $method], $parameters);
    }
}
