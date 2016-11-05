<?php

use Illuminate\Console\Command;

class FooCommand extends Command
{
    protected $signature = 'foo';

    public function handle()
    {
        $this->info('Yep!');
    }
}
