<?php

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class TimeoutCommand extends Command
{
    use WithoutOverlapping;

    protected $signature = 'icm:timeout-command';
    protected $mutexTimeout = 3000;

    public function handle()
    {
        $this->info('Done!');
    }
}
