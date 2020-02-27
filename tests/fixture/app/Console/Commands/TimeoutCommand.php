<?php

namespace Illuminated\Console\Tests\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class TimeoutCommand extends Command
{
    use WithoutOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icm:timeout-command';

    /**
     * The mutex timeout.
     *
     * @var int|null
     */
    protected $mutexTimeout = 3000;

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Done!');
    }
}
