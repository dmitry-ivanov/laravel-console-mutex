<?php

use Illuminate\Console\Command;

class Sleep100ms extends Command
{
    protected $signature = 'sleep-100ms';

    public function handle()
    {
        usleep(100000);
        $this->info('Done!');
    }
}
