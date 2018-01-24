<?php

namespace Illuminated\Console\ConsoleMutex\Tests;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminated\Testing\TestingTools;
use Kernel;
use Mockery;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use TestingTools;

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(KernelContract::class, Kernel::class);

        app(KernelContract::class);
    }
}
