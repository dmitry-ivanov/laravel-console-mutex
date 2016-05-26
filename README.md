# Laravel console mutex

[![StyleCI](https://styleci.io/repos/59570052/shield)](https://styleci.io/repos/59570052)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7/mini.png)](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7)

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

Overlapping can be prevented by various strategies. `file` by default, strategy should be chosen according to your context.
If your application is deployed on a single server, then using defaults is okay. Just use trait, and that's it.
But if your application is deployed on a several nodes, which can run artisan commands, then you should use `database` strategy.

You can change strategy in your console command class, by specifying `$overlappingStrategy` field:

```php
class Foo extends Command
{
    use WithoutOverlapping;

    // ...

    protected $overlappingStrategy = 'database';

    // ...
}

```

Or by using `setOverlappingStrategy()` method:

```php
class Foo extends Command
{
    use WithoutOverlapping;

    // ...

    public function __construct()
    {
        parent::__construct();

        $strategy = config('foo.overlapping');
        $this->setOverlappingStrategy($strategy);
    }

    // ...
}
```
