<?php

use Illuminated\Console\Mutex;
use NinjaMutex\Lock\FlockLock;

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
        $this->assertEquals(new FlockLock(storage_path('app')), $mutex->getStrategy());
    }
}
