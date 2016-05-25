# Laravel console commands overlapping

[![StyleCI](https://styleci.io/repos/59570052/shield)](https://styleci.io/repos/59570052)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fd433eb8-d523-4e75-b6c3-9bd60e5f0171/mini.png)](https://insight.sensiolabs.com/projects/fd433eb8-d523-4e75-b6c3-9bd60e5f0171)

Prevents overlapping for artisan console commands.

## Dependencies
- `PHP >=7.0.0`
- `Laravel >=5.2`

## Usage

1. Install package through `composer`:
    ```shell
    composer require illuminated/console-overlapping
    ```

2. Use `Illuminated\Console\WithoutOverlapping` trait in your console command class:
    ```php
    namespace App\Console\Commands;
    
    use Illuminate\Console\Command;
    use Illuminated\Console\WithoutOverlapping;
    
    class Foo extends Command
    {
        use WithoutOverlapping;
    
        protected $signature = 'foo';
        protected $description = 'Some dummy command';
    
        public function handle()
        {
            $this->info('Foo! Bar! Baz!');
        }
    }
    ```

## Strategies

Overlapping can be prevented by various strategies. Strategy should be chosen according to your context.
If your application is deployed on a single server, then `file` strategy is okay for you. This is default.
But if your application is deployed on a several nodes, and each node can run artisan commands, then you should use `database` strategy.
