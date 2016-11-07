<?php

class WithoutOverlappingTraitTest extends TestCase
{
    /** @test */
    public function it_adds_mutex_strategy_property_which_is_file_by_default()
    {
        $command = new GenericCommand;
        $this->assertEquals('file', $command->getMutexStrategy());
    }

    /** @test */
    public function mutex_strategy_can_by_overloaded_by_protected_field()
    {
        $command = new MysqlStrategyCommand;
        $this->assertEquals('mysql', $command->getMutexStrategy());
    }
}
