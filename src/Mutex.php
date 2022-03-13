<?php

namespace Illuminated\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\LockAbstract;
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
     */
    private Command $command;

    /**
     * The NinjaMutex.
     */
    private NinjaMutex $ninjaMutex;

    /**
     * The NinjaMutex lock.
     */
    private LockAbstract $ninjaMutexLock;

    /**
     * Create a new instance of the mutex.
     */
    public function __construct(Command $command)
    {
        /** @var WithoutOverlapping $command */
        $this->command = $command;

        $mutexName = $command->getMutexName();
        $this->ninjaMutexLock = $this->getNinjaMutexLock();
        $this->ninjaMutex = new NinjaMutex($mutexName, $this->ninjaMutexLock);
    }

    /**
     * Get the NinjaMutex lock.
     */
    public function getNinjaMutexLock(): LockAbstract
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
     */
    private function getRedisLock(string $client): LockAbstract
    {
        $redis = RedisFacade::connection()->client();

        return $client === 'phpredis'
            ? new PhpRedisLock($redis)
            : new PredisRedisLock($redis);
    }

    /**
     * Forward method calls to NinjaMutex.
     */
    public function __call(string $method, mixed $parameters): mixed
    {
        return call_user_func_array([$this->ninjaMutex, $method], $parameters);
    }
}
