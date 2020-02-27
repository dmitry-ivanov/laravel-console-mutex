<?php

namespace Illuminated\Console\Tests\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class MysqlStrategyCommand extends Command
{
    use WithoutOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icm:mysql-strategy-command';

    /**
     * The mutex strategy.
     *
     * @var string
     */
    protected $mutexStrategy = 'mysql';

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
