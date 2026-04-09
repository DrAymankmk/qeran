<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Request Tracker
    |--------------------------------------------------------------------------
    |
    | When enabled, selected API routes can log a structured entry for each
    | request (success/failure + duration + context).
    |
    */

    'enabled' => env('API_TRACKER_ENABLED', false),

    // Which log channel to write to. Default uses Laravel's default channel.
    'channel' => env('API_TRACKER_LOG_CHANNEL', null),

    // If true, logs request payload keys (NOT values). Useful for debugging.
    'log_payload_keys' => env('API_TRACKER_LOG_PAYLOAD_KEYS', false),
];

