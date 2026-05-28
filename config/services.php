<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'Beams' => [
        'Beams_Instance_Id' => env('Beams_Instance_Id'),
        'Beams_Secret_key' => env('Beams_Secret_key'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
        'whatsapp_sandbox_enabled' => env('TWILIO_WHATSAPP_SANDBOX_ENABLED', true),
        'whatsapp_messaging_service_sid' => env('TWILIO_WHATSAPP_MESSAGING_SERVICE_SID'),
    ],

    'baileys' => [
        // Public URL (optional). When Laravel runs on the same server as PM2, prefer 127.0.0.1 — see gateway_internal_url.
        'gateway_url' => env('BAILEYS_GATEWAY_URL', 'http://127.0.0.1:3000'),
        // Server-side calls use this when set (avoids HTTPS/nginx timeouts to the public hostname).
        'gateway_internal_url' => env('BAILEYS_GATEWAY_INTERNAL_URL'),
        'gateway_secret' => env('BAILEYS_GATEWAY_SECRET'),
        'system_session' => env('BAILEYS_SYSTEM_SESSION', 'system'),
    ],

];
