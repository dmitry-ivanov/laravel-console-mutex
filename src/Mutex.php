<?php

namespace Illuminated\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MemcachedLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PhpRedisLock;
use NinjaMutex\Lock\PredisRedisLock;
use NinjaMutex\Mutex as NinjaMutex;

/**
 * @mixin \NinjaMutex\Mutex
 */
class Mutex
{
    /**
     * The console command.
     *
     * @var \Illuminate\Console\Command
     */
    private $command;

    /**
     * The ninja mutex.
     *
     * @var \NinjaMutex\Mutex
     */
    private $ninjaMutex;

    /**
     * The ninja mutex lock.
     *
     * @var \NinjaMutex\Lock\LockAbstract
     */
    private $ninjaMutexLock;

    /**
     * Create a new instance of the mutex.
     *
     * @param \Illuminate\Console\Command|\Illuminated\Console\WithoutOverlapping $command
     * @return void
     */
    public function __construct(Command $command)
    {
        $this->command = $command;

        $mutexName = $command->getMutexName();
        $this->ninjaMutexLock = $this->getNinjaMutexLock();
        $this->ninjaMutex = new NinjaMutex($mutexName, $this->ninjaMutexLock);
    }

    /**
     * Get the ninja mutex lock.
     *
     * @return \NinjaMutex\Lock\LockAbstract
     */
    public function getNinjaMutexLock()
    {
        if (!empty($this->ninjaMutexLock)) {
            return $this->ninjaMutexLock;
        }

        $strategy = $this->command->getMutexStrategy();
        switch ($strategy) {
            case 'mysql':
                return new MySqlLock(
                    config('database.connections.mysql.username'),
                    config('database.connections.mysql.password'),
                    config('database.connections.mysql.host'),
                    config('database.connections.mysql.port', 3306)
                );

            case 'redis':
                return $this->getRedisLock(config('database.redis.client', 'phpredis'));

            case 'memcached':
                return new MemcachedLock(Cache::getStore()->getMemcached());

            case 'file':
            default:
                return new FlockLock($this->command->getMutexFileStorage());
        }
    }

    /**
     * Get the redis lock.
     *
     * @param string $client
     * @return \NinjaMutex\Lock\LockAbstract
     */
    private function getRedisLock($client)
    {
        $redis = RedisFacade::connection()->client();

        return $client === 'phpredis'
            ? new PhpRedisLock($redis)
            : new PredisRedisLock($redis);
    }

    /**
     * Forward method calls to ninja mutex.
     *
     * @param string $method
     * @param mixed $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ninjaMutex, $method], $parameters);
    }
}
