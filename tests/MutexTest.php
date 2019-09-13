<?php

namespace Illuminated\Console\Tests;

use Redis;
use Illuminated\Console\Mutex;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PhpRedisLock;
use NinjaMutex\Lock\MemcachedLock;
use Predis\Client as PredisClient;
use NinjaMutex\Lock\PredisRedisLock;
use Illuminate\Support\Facades\Cache;
use Illuminated\Console\Tests\App\Console\Commands\GenericCommand;

class MutexTest extends TestCase
{
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = mock(GenericCommand::class)->makePartial();
        $this->command->expects()->getName()->andReturn('icm:generic');
        $this->command->expects()->argument()->andReturn(['foo' => 'bar']);
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
        $this->command->expects()->getMutexStrategy()->andReturn('foobar');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new FlockLock(storage_path('app'));
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_supports_mysql_strategy()
    {
        $this->command->expects()->getMutexStrategy()->andReturn('mysql');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new MySqlLock(
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.port', 3306)
        );
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_supports_redis_strategy_with_phpredis_client_which_is_default()
    {
        $this->command->expects()->getMutexStrategy()->andReturn('redis');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new PhpRedisLock($mutex->getPhpRedisClient());
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_supports_redis_strategy_with_predis_client()
    {
        config(['database.redis.client' => 'predis']);

        $this->command->expects()->getMutexStrategy()->andReturn('redis');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new PredisRedisLock($mutex->getPredisClient());
        $this->assertEquals($expectedStrategy, $mutex->getStrategy());
    }

    /** @test */
    public function it_has_get_phpredis_client_method_which_always_returns_an_instance_of_redis_class()
    {
        $mutex = new Mutex($this->command);
        $this->assertInstanceOf(Redis::class, $mutex->getPhpRedisClient());
    }

    /** @test */
    public function it_has_get_predis_client_method_which_always_returns_an_instance_of_predis_client_class()
    {
        config(['database.redis.client' => 'predis']);

        $mutex = new Mutex($this->command);
        $this->assertInstanceOf(PredisClient::class, $mutex->getPredisClient());
    }

    /** @test */
    public function it_supports_memcached_strategy()
    {
        Cache::shouldReceive('getStore')->withNoArgs()->twice()->andReturnSelf();
        Cache::shouldReceive('getMemcached')->withNoArgs()->twice()->andReturnSelf();

        $this->command->expects()->getMutexStrategy()->andReturn('memcached');

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
        $ninja = mock('overload:NinjaMutex\Mutex');
        $ninja->expects()->isLocked();

        $mutex = new Mutex($this->command);
        $mutex->isLocked();
    }
}
