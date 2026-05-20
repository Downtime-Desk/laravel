<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Downtime Desk Webhook Reporting Laravel Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for Downtime Desk webhook reporting from your Laravel
    | application. This integration allows you to easily report service status
    | changes to DowntimeDesk using Laravel's built-in HTTP client.
    |
    */

    'enabled' => env('DOWNTIME_DESK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Monitor Name
    |--------------------------------------------------------------------------
    |
    | The default monitor name to use when no specific monitor is provided.
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Downtime Desk base URL
    |--------------------------------------------------------------------------
    |
    | Base URL used when reporting back to Downtime Desk.
    |
    */

    'base_url' => 'https://app.downtimedesk.com/api/heartbeat/{uuid}',

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | When working with queues, this setting determines the name of the queue
    | used for monitoring tasks. Adjust as needed for your application's queue configuration.
    |
    */

    'queue' => env('DOWNTIME_DESK_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Monitors
    |--------------------------------------------------------------------------
    |
    | Configured named monitors. Add additional monitors to report() them
    | by name within your application.
    |
    */

    'monitors' => [

        'default' => [
            'id' => env('DOWNTIME_DESK_WEBHOOK_ID'),
            'secret' => env('DOWNTIME_DESK_WEBHOOK_SECRET'),
            'interval' => env('DOWNTIME_DESK_WEBHOOK_INTERVAL', 60),
        ],

    ],

];
