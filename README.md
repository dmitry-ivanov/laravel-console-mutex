# Laravel console mutex

[![StyleCI](https://styleci.io/repos/59570052/shield)](https://styleci.io/repos/59570052)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7/mini.png)](https://insight.sensiolabs.com/projects/e4083afa-8ca9-4ac0-8be8-9bfadcb05fa7)

Prevents overlapping for artisan console commands.

## Dependencies
- `PHP >=5.4.0`
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

## Strategies
