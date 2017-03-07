<?php

namespace Illuminated\Console\ConsoleMutex\Tests;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Kernel;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(KernelContract::class, Kernel::class);
    }
}
