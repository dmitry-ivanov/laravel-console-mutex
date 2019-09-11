<?php

namespace Illuminated\Console\Tests\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class NullTimeoutCommand extends Command
{
    use WithoutOverlapping;

    protected $signature = 'icm:null-timeout-command';
    protected $mutexTimeout = null;

    public function handle()
    {
        $this->info('Done!');
    }
}
