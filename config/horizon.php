<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
    ],


    // Middleware

    'middleware' => ['web', 'horizon'],
    
    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'orders' => [
                'connection' => 'redis',
                'queue' => ['orders'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'conditionals' => [
                'connection' => 'redis',
                'queue' => ['conditionals'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'trades' => [
                'connection' => 'redis',
                'queue' => ['trades'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'stops' => [
                'connection' => 'redis',
                'queue' => ['stops'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'profits' => [
                'connection' => 'redis',
                'queue' => ['profits'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'portfolios' => [
                'connection' => 'redis',
                'queue' => ['portfolios'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'assets' => [
                'connection' => 'redis',
                'queue' => ['assets'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
        ],

        'local' => [
            'default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'orders' => [
                'connection' => 'redis',
                'queue' => ['orders'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'conditionals' => [
                'connection' => 'redis',
                'queue' => ['conditionals'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'trades' => [
                'connection' => 'redis',
                'queue' => ['trades'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'stops' => [
                'connection' => 'redis',
                'queue' => ['stops'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'profits' => [
                'connection' => 'redis',
                'queue' => ['profits'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'portfolios' => [
                'connection' => 'redis',
                'queue' => ['portfolios'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 1,
            ],
            'assets' => [
                'connection' => 'redis',
                'queue' => ['assets'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 1,
            ],
        ],
    ],
];
