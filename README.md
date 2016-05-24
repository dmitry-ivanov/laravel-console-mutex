# Laravel console commands overlapping

[![StyleCI](https://styleci.io/repos/59570052/shield)](https://styleci.io/repos/59570052)

Prevents overlapping for artisan console commands.

## Dependencies
- `PHP >=7.0.0`
- `Laravel >=5.2`

## Installation

1. Install package through `composer`:
    ```shell
    composer require dmitry-ivanov/laravel-console-overlapping
    ```

2. Add `ServiceProvider` to `config/app.php`:
    ```php
    'providers' => [
        // ...
        DmitryIvanov\LaravelConsoleOverlapping\ServiceProvider::class,
    ],
    ```
