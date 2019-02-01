<?php

use Illuminate\Console\Command;
use Illuminated\Console\Mutex\WithoutOverlapping;

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
