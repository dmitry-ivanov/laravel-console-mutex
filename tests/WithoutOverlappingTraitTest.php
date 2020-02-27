<?php

namespace Illuminated\Console\Tests;

use Illuminated\Console\MutexRuntimeException;
use Illuminated\Console\Tests\App\Console\Commands\GenericCommand;
use Illuminated\Console\Tests\App\Console\Commands\MysqlStrategyCommand;
use Illuminated\Console\Tests\App\Console\Commands\NullTimeoutCommand;
use Illuminated\Console\Tests\App\Console\Commands\TimeoutCommand;

class WithoutOverlappingTraitTest extends TestCase
{
    /** @test */
    public function it_adds_mutex_strategy_which_is_file_by_default()
    {
        $this->assertEquals('file', (new GenericCommand)->getMutexStrategy());
    }

    /** @test */
    public function mutex_strategy_can_be_overloaded_by_protected_field()
    {
        $this->assertEquals('mysql', (new MysqlStrategyCommand)->getMutexStrategy());
    }

    /** @test */
    public function mutex_strategy_can_be_set_by_the_public_method()
    {
        $command = new GenericCommand;
        $command->setMutexStrategy('redis');

        $this->assertEquals('redis', $command->getMutexStrategy());
    }

    /** @test */
    public function it_adds_mutex_timeout_which_is_zero_by_default()
    {
        $this->assertEquals(0, (new GenericCommand)->getMutexTimeout());
    }

    /** @test */
    public function mutex_timeout_can_be_overloaded_by_protected_field()
    {
        $this->assertEquals(3000, (new TimeoutCommand)->getMutexTimeout());
    }

    /** @test */
    public function mutex_timeout_can_be_set_to_null_by_protected_field()
    {
        $this->assertNull((new NullTimeoutCommand)->getMutexTimeout());
    }

    /** @test */
    public function mutex_timeout_can_be_set_by_the_public_method()
    {
        $command = new GenericCommand;
        $command->setMutexTimeout(5000);

        $this->assertEquals(5000, $command->getMutexTimeout());
    }

    /** @test */
    public function mutex_timeout_can_be_set_to_null_by_the_public_method()
    {
        $command = new GenericCommand;
        $command->setMutexTimeout(null);

        $this->assertNull($command->getMutexTimeout());
    }

    /** @test */
    public function it_generates_mutex_name_based_on_the_command_name_and_arguments()
    {
        /** @var \Mockery\Mock|\Illuminated\Console\WithoutOverlapping $command */
        $command = mock(GenericCommand::class)->makePartial();
        $command->expects('getName')->andReturn('icm:generic');
        $command->expects('argument')->andReturn(['foo' => 'bar', 'baz' => 'faz']);

        $hash = md5(json_encode(['foo' => 'bar', 'baz' => 'faz']));
        $this->assertEquals("icmutex-icm:generic-{$hash}", $command->getMutexName());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_allows_to_run_command_if_there_is_no_other_running_instances()
    {
        $mutex = mock('overload:Illuminated\Console\Mutex');
        $mutex->expects('acquireLock')->with(0)->andReturn(true);
        $mutex->allows('releaseLock');

        $this->artisan('icm:generic');
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_blocks_if_trying_to_run_another_instance_of_the_command()
    {
        $this->willSeeException(MutexRuntimeException::class, 'Command is running now!');

        $mutex = mock('overload:Illuminated\Console\Mutex');
        $mutex->expects('acquireLock')->with(0)->andReturn(false);

        $this->artisan('icm:generic');
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_is_releasing_the_lock_after_command_execution()
    {
        /** @var \Mockery\Mock|\Illuminated\Console\Mutex $mutex */
        $mutex = mock('overload:Illuminated\Console\Mutex');
        $mutex->expects('releaseLock');

        $command = new GenericCommand;
        $command->releaseMutexLock($mutex);
    }
}
