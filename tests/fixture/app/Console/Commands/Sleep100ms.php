<?php

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class Sleep100ms extends Command
{
    use WithoutOverlapping;

    protected $signature = 'sleep-100ms';

    public function handle()
    {
        usleep(100000);
        $this->info('Done!');
    }
}
