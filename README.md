# laravel-logs

This will log in json format to laravel.log with extra information, and when used via CLI it will log in human-readable format in console output, and also in json format in laravel.log.

## Installation

Install the latest version with

```bash
composer require florinmotoc/laravel-logs
```

## Basic Usage

```php
<?php
// laravel's config/logging.php file:

return [
    'default' => env('LOG_CHANNEL', 'fm_stack'),
    'channels' => [
        'fm_stack' => [
            'driver' => 'stack',
            'channels' => ['fm_console', 'fm_file'],
            'ignore_exceptions' => false,
        ],

        'fm_console' => [
            'driver' => 'monolog',
            'handler' => \FlorinMotoc\LaravelLogs\LaravelMonologTap\Handler\ConsoleHandler::class,
            'with' => [
                'verbosity' => env('CONSOLE_VERBOSITY'), // \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG
            ]
        ],

        'fm_file' => [
            'driver' => 'monolog',
            'formatter' => Monolog\Formatter\JsonFormatter::class,
            'handler' => Monolog\Handler\StreamHandler::class,
            'with' => [
                'stream' => storage_path('logs/laravel.log'),
                'level' => 'debug',
            ],
            'tap' => [
                \FlorinMotoc\LaravelLogs\LaravelMonologTap\LaravelMonologTap::class
            ],
        ],
    ]
]
```

```dotenv
LOG_CHANNEL=fm_stack
FM_LARAVEL_LOGS_USE_EXTRA_PID=true
FM_LARAVEL_LOGS_USE_EXTRA_JOB_ID=true

# change this to one of \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_* values for more logs - 256 is very verbose!
CONSOLE_VERBOSITY=256
```

- set `LOG_CHANNEL=fm_stack` in your `.env` file to activate `LaravelMonologTap`
    - also need to change laravel's `config/logging.php` file with above contents!
- set `FM_LARAVEL_LOGS_USE_EXTRA_PID=true` in your `.env` file if you want to add the PID to the monolog extra array.
- set `FM_LARAVEL_LOGS_USE_EXTRA_JOB_ID=true` in your `.env` file if you want to add the laravel queue jobs id to the monolog extra array.
- optionally set `CONSOLE_VERBOSITY=` in your `.env` file to control verbosity
    - change this to one of `\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_*` values for more logs - 256 is very verbose!
      - VERBOSITY_QUIET = 16;
      - VERBOSITY_NORMAL = 32;
      - VERBOSITY_VERBOSE = 64;
      - VERBOSITY_VERY_VERBOSE = 128;
      - VERBOSITY_DEBUG = 256;
