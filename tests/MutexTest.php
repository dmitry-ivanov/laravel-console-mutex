<?php

use Illuminated\Console\Mutex;

class MutexTest extends TestCase
{
    /** @test */
    public function it_has_one_required_constructor_argument_which_is_command()
    {
        $command = Mockery::mock(GenericCommand::class)->makePartial();
        $command->shouldReceive('argument')->withNoArgs()->once()->andReturn(['foo' => 'bar']);

        $mutex = new Mutex($command);
        $this->assertInstanceOf(Mutex::class, $mutex);
    }
}
