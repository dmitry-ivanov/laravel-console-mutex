<?php

class WithoutOverlappingTraitTest extends TestCase
{
    /** @test */
    public function it_adds_mutex_strategy_field_to_the_command_which_is_file_by_default()
    {
        $command = new GenericCommand;
        $this->assertEquals('file', $command->getMutexStrategy());
    }
}
