<?php

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class GenericCommand extends Command
{
    use WithoutOverlapping;

    protected $signature = 'icm:generic';

    public function handle()
    {
        $this->info('Done!');
    }
}
