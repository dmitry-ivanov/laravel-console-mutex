<?php

class Kernel extends \Orchestra\Testbench\Console\Kernel
{
    protected $commands = [
        Sleep100ms::class,
    ];
}
