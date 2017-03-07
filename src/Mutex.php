<?php

namespace Illuminated\Console;

use Cache;
use Illuminate\Console\Command;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MemcachedLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PredisRedisLock;
use NinjaMutex\Mutex as Ninja;
use Predis\Client;
use Redis;

class Mutex
{
    private $command;
    private $strategy;
    private $ninja;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->strategy = $this->getStrategy();
        $this->ninja = new Ninja($command->getMutexName(), $this->strategy);
    }

    public function getStrategy()
    {
        if (!empty($this->strategy)) {
            return $this->strategy;
        }

        switch ($this->command->getMutexStrategy()) {
            case 'mysql':
                return new MySqlLock(
                    config('database.connections.mysql.username'),
                    config('database.connections.mysql.password'),
                    config('database.connections.mysql.host')
                );

            case 'redis':
                return new PredisRedisLock($this->getRedisClient());

            case 'memcached':
                return new MemcachedLock(Cache::getStore()->getMemcached());

            case 'file':
            default:
                return new FlockLock(storage_path('app'));
        }
    }

    public function getRedisClient()
    {
        $connection = Redis::connection();
        return ($connection instanceof Client) ? $connection : $connection->client();
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ninja, $method], $parameters);
    }
}
