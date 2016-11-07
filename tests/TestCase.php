<?php

use Illuminate\Contracts\Console\Kernel as KernelContract;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(KernelContract::class, Kernel::class);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useStoragePath(__DIR__ . '/fixture/storage');
    }
}
