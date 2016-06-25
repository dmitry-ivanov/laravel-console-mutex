# Laravel console mutex

[![StyleCI](https://styleci.io/repos/59570052/shield)](https://styleci.io/repos/59570052)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7/mini.png)](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7)

Prevents overlapping for Laravel console commands.

## Dependencies
- `PHP >=5.5.9`
- `Laravel >=5.2`

## Usage

1. Install package through `composer`:
    ```shell
    composer require illuminated/console-mutex
    ```

2. Use `Illuminated\Console\WithoutOverlapping` trait in your console command class:
    ```php
    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminated\Console\WithoutOverlapping;

    class Foo extends Command
    {
        use WithoutOverlapping;

        // ...
    }
    ```

3. Now your command is protected against overlapping:
    ```shell
    ➜ php artisan foo:bar baz
    Command is running now!
    ```

## Strategies

Overlapping can be prevented by various strategies:

- `file` (default)
- `mysql`
- `redis`
- `memcached`

Default `file` strategy is fine for a small applications, which are deployed on a single server.
If your application is more complex and, for example, is deployed on a several nodes, then you probably would like to use some other mutex strategy.

You can change mutex strategy by specifying `$mutexStrategy` field:

```php
class Foo extends Command
{
    use WithoutOverlapping;

    protected $mutexStrategy = 'mysql';

    // ...
}
```

Or by using `setMutexStrategy` method:

```php
class Foo extends Command
{
    use WithoutOverlapping;

    public function __construct()
    {
        parent::__construct();
    
        $this->setMutexStrategy('mysql');
    }

    // ...
}

```
