# Laravel console commands overlapping

Prevents overlapping for artisan console commands.

## Dependencies
- `PHP >=7.0.0`
- `Laravel >=5.2`

## Installation

1. Add package using `composer`:
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
