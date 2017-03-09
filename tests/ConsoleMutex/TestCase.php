<?php

namespace Illuminated\Console\ConsoleMutex\Tests;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminated\Testing\TestingTools;
use Kernel;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use TestingTools;

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(KernelContract::class, Kernel::class);
    }
}
