<?php

namespace Illuminated\Console\Tests\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class NullTimeoutCommand extends Command
{
    use WithoutOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icm:null-timeout-command';

    /**
     * The mutex timeout.
     */
    protected ?int $mutexTimeout = null;

    /**
     * Handle the command.
     */
    public function handle(): void
    {
        $this->info('Done!');
    }
}
