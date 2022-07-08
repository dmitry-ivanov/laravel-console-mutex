<?php

namespace Illuminated\Console\Tests;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade;
use Illuminated\Console\Mutex;
use Illuminated\Console\Tests\App\Console\Commands\GenericCommand;
use Mockery\Mock;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MemcachedLock;
use NinjaMutex\Lock\MySQLPDOLock;
use NinjaMutex\Lock\PhpRedisLock;
use NinjaMutex\Lock\PredisRedisLock;
use Predis\Client as PredisClient;
use Redis;

class MutexTest extends TestCase
{
    /**
     * The console command mock.
     */
    private Mock|Command $command;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = mock(GenericCommand::class)->makePartial();
        $this->command->expects('getName')->andReturn('icm:generic');
        $this->command->expects('argument')->andReturn(['foo' => 'bar']);
    }

    /** @test */
    public function it_requires_command_as_constructor_parameter()
    {
        $mutex = new Mutex($this->command);
        $this->assertInstanceOf(Mutex::class, $mutex);
    }

    /** @test */
    public function it_determines_ninja_mutex_lock_once_while_creation()
    {
        $mutex = new Mutex($this->command);
        $this->assertSame($mutex->getNinjaMutexLock(), $mutex->getNinjaMutexLock());
    }

    /** @test */
    public function it_has_default_strategy_which_is_file()
    {
        $this->command->expects('getMutexStrategy')->andReturn('foobar');

        $mutex = new Mutex($this->command);
        $expectedLock = new FlockLock(storage_path('app'));
        $this->assertEquals($expectedLock, $mutex->getNinjaMutexLock());
    }

    /** @test */
    public function it_supports_mysql_strategy()
    {
        $this->command->expects('getMutexStrategy')->andReturn('mysql');

        $mutex = new Mutex($this->command);
        $expectedLock = new MySqlPdoLock(
            'mysql:' . implode(';', [
                'host=' . config('database.connections.mysql.host'),
                'port=' . config('database.connections.mysql.port', 3306),
            ]),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.options')
        );
        $this->assertEquals($expectedLock, $mutex->getNinjaMutexLock());
    }

    /** @test */
    public function it_supports_redis_strategy_with_phpredis_client_which_is_default()
    {
        $this->command->expects('getMutexStrategy')->andReturn('redis');

        $redis = mock(Redis::class);
        RedisFacade::shouldReceive('connection->client')->once()->andReturn($redis);

        $mutex = new Mutex($this->command);
        $expectedLock = new PhpRedisLock($redis);
        $this->assertEquals($expectedLock, $mutex->getNinjaMutexLock());
    }

    /** @test */
    public function it_supports_redis_strategy_with_predis_client()
    {
        config(['database.redis.client' => 'predis']);

        $this->command->expects('getMutexStrategy')->andReturn('redis');

        $predis = mock(PredisClient::class);
        RedisFacade::shouldReceive('connection->client')->once()->andReturn($predis);

        $mutex = new Mutex($this->command);
        $expectedLock = new PredisRedisLock($predis);
        $this->assertEquals($expectedLock, $mutex->getNinjaMutexLock());
    }

    /** @test */
    public function it_supports_memcached_strategy()
    {
        Cache::shouldReceive('getStore')->withNoArgs()->twice()->andReturnSelf();
        Cache::shouldReceive('getMemcached')->withNoArgs()->twice()->andReturnSelf();

        $this->command->expects('getMutexStrategy')->andReturn('memcached');

        $mutex = new Mutex($this->command);
        $expectedLock = new MemcachedLock(Cache::getStore()->getMemcached());
        $this->assertEquals($expectedLock, $mutex->getNinjaMutexLock());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_delegates_public_method_calls_to_ninja_mutex()
    {
        $ninja = mock('overload:NinjaMutex\Mutex');
        $ninja->expects('isLocked');

        $mutex = new Mutex($this->command);
        $mutex->isLocked();
    }
}
