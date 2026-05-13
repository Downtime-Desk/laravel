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

    'default' => 'default',

    'base_url' => env(
        'DOWNTIME_DESK_BASE_URL',
        'https://app.downtimedesk.com'
    ),

    'queue' => env('DOWNTIME_DESK_QUEUE', 'monitoring'),

    'timeout' => env('DOWNTIME_DESK_TIMEOUT', 5),

    'auto_schedule' => env('DOWNTIME_DESK_AUTO_SCHEDULE', true),

    'webhooks' => [

        'default' => [
            'id' => env('DOWNTIME_DESK_WEBHOOK_ID'),
            'secret' => env('DOWNTIME_DESK_WEBHOOK_SECRET'),
            'interval' => env('DOWNTIME_DESK_WEBHOOK_INTERVAL', 60),
        ],

    ],

];
