<?php

use Illuminate\Console\Command;

class SleepOneSecond extends Command
{
    protected $signature = 'sleep-one-second';

    public function handle()
    {
        sleep(1);
        $this->info('Done!');
    }
}
