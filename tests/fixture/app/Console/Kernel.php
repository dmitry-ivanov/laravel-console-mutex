<?php

class Kernel extends \Orchestra\Testbench\Console\Kernel
{
    protected $commands = [
        GenericCommand::class,
        MysqlStrategyCommand::class,
        NullTimeoutCommand::class,
        TimeoutCommand::class,
    ];
}
