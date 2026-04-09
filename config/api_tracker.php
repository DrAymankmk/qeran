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

    // If true, logs request payload values (sanitized/redacted).
    // Prefer enabling only temporarily in production.
    'log_payload' => env('API_TRACKER_LOG_PAYLOAD', false),

    // Keys to redact when logging payload.
    'redact_keys' => [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'authorization',
        'otp',
        'code',
        'pin',
        'secret',
        'api_key',
        'key',
        'signature',
        'file',
        'image',
        'video',
        'audio',
    ],

    // Prevent very large logs (truncate long strings).
    'max_string_length' => env('API_TRACKER_MAX_STRING_LENGTH', 2000),
];

