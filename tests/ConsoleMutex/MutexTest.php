<?php

namespace Illuminated\Console\ConsoleMutex\Tests;

use GenericCommand;
use Illuminate\Support\Facades\Cache;
use Illuminated\Console\Mutex;
use Mockery;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MemcachedLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PredisRedisLock;
use Predis\Client;

class MutexTest extends TestCase
{
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->command = Mockery::mock(GenericCommand::class)->makePartial();
        $this->command->shouldReceive('argument')->withNoArgs()->once()->andReturn(['foo' => 'bar']);
    }

    /** @test */
    public function it_requires_command_as_constructor_parameter()
    {
        $mutex = new Mutex($this->command);
        $this->assertInstanceOf(Mutex::class, $mutex);
    }

    /** @test */
    public function it_determines_mutex_strategy_once_while_creation()
    {
        $mutex = new Mutex($this->command);
        $this->assertSame($mutex->getStrategy(), $mutex->getStrategy());
    }

    /** @test */
    public function it_has_default_strategy_which_is_file()
    {
        $this->command->shouldReceive('getMutexStrategy')->withNoArgs()->once()->andReturn('foobar');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new FlockLock(storage_path('app'));
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_supports_mysql_strategy()
    {
        $this->command->shouldReceive('getMutexStrategy')->withNoArgs()->once()->andReturn('mysql');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new MySqlLock(env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_HOST'));
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_has_get_redis_client_method()
    {
        $mutex = new Mutex($this->command);
        $this->assertInstanceOf(Client::class, $mutex->getRedisClient());
    }

    /** @test */
    public function it_supports_redis_strategy()
    {
        $this->command->shouldReceive('getMutexStrategy')->withNoArgs()->once()->andReturn('redis');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new PredisRedisLock($mutex->getRedisClient());
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_supports_memcached_strategy()
    {
        Cache::shouldReceive('getStore')->withNoArgs()->twice()->andReturnSelf();
        Cache::shouldReceive('getMemcached')->withNoArgs()->twice()->andReturnSelf();
        $this->command->shouldReceive('getMutexStrategy')->withNoArgs()->once()->andReturn('memcached');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new MemcachedLock(Cache::getStore()->getMemcached());
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_delegates_public_method_calls_to_ninja_mutex()
    {
        $ninja = Mockery::mock('overload:NinjaMutex\Mutex');
        $ninja->shouldReceive('isLocked')->once();

        $mutex = new Mutex($this->command);
        $mutex->isLocked();
    }
}
