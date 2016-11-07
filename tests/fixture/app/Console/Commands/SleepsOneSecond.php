<?php

use Illuminate\Console\Command;

class SleepsOneSecond extends Command
{
    protected $signature = 'sleeps-one-second';

    public function handle()
    {
        $this->info('Done!');
    }
}
