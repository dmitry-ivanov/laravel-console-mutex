<?php

use Illuminated\Console\Mutex;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Lock\MemcachedLock;
use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Lock\PredisRedisLock;

class MutexTest extends TestCase
{
    private $command;

    protected function setUp()
    {
        parent::setUp();

        $this->command = Mockery::mock(GenericCommand::class)->makePartial();
        $this->command->shouldReceive('argument')->withNoArgs()->once()->andReturn(['foo' => 'bar']);
    }

    /** @test */
    public function it_has_one_required_constructor_argument_which_is_command()
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
    public function it_supports_redis_strategy()
    {
        $this->command->shouldReceive('getMutexStrategy')->withNoArgs()->once()->andReturn('redis');

        $mutex = new Mutex($this->command);
        $expectedStrategy = new PredisRedisLock(Redis::connection());
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
}
