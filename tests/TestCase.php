<?php

namespace Illuminated\Console\Tests;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminated\Console\Tests\App\Console\Kernel;
use Illuminated\Testing\TestingTools;
use Mockery;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use TestingTools;

    /**
     * Resolve application Console Kernel implementation.
     */
    protected function resolveApplicationConsoleKernel($app): void
    {
        $app->singleton(KernelContract::class, Kernel::class);

        app(KernelContract::class);
    }
}
