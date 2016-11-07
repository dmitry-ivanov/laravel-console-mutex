<?php

class Kernel extends \Orchestra\Testbench\Console\Kernel
{
    protected $commands = [
        SleepsOneSecond::class,
    ];
}
