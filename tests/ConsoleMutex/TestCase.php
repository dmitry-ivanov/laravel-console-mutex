<?php

namespace Illuminated\Console\ConsoleMutex\Tests;

use Kernel;
use Mockery;
use Illuminated\Testing\TestingTools;
use Illuminate\Contracts\Console\Kernel as KernelContract;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use TestingTools;

    public $mockConsoleOutput = false;

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(KernelContract::class, Kernel::class);

        app(KernelContract::class);
    }
}
