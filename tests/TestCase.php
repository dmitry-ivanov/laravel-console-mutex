<?php

namespace Illuminated\Console\Tests;

use Mockery;
use Illuminated\Testing\TestingTools;
use Illuminated\Console\Tests\App\Console\Kernel;
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
