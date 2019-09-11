<?php

namespace Illuminated\Console\Tests\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class MysqlStrategyCommand extends Command
{
    use WithoutOverlapping;

    protected $signature = 'icm:mysql-strategy-command';
    protected $mutexStrategy = 'mysql';

    public function handle()
    {
        $this->info('Done!');
    }
}
