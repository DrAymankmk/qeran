<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for various webhook handlers
    | used in the application for external service integrations.
    |
    */

    'whatsapp' => [
        /*
        |--------------------------------------------------------------------------
        | WhatsApp Webhook Settings
        |--------------------------------------------------------------------------
        |
        | Configuration for WhatsApp webhook handlers including UltraMessage
        | and other WhatsApp Business API providers.
        |
        */
        
        'enabled' => env('WHATSAPP_WEBHOOK_ENABLED', true),
        'webhook_token' => env('WHATSAPP_WEBHOOK_TOKEN'),
        'verify_signature' => env('WHATSAPP_VERIFY_SIGNATURE', true),
        
        // Allowed IP addresses for webhook requests
        'allowed_ips' => [
            '185.37.37.37', // UltraMessage - update with actual IPs
            '127.0.0.1',    // Local development
        ],
        
        // UltraMessage specific settings
        'ultramsg' => [
            'instance_id' => env('ULTRAMSG_INSTANCE_ID', 'instance78179'),
            'token' => env('ULTRAMSG_TOKEN', 'mrmm9ckrsa8ojdef'),
            'api_url' => env('ULTRAMSG_API_URL', 'https://api.ultramsg.com'),
        ],

        // Response settings
        'default_language' => 'ar', // Arabic by default
        'response_delay' => 1, // Delay in seconds before responding
        'max_message_length' => 1000,
        
        // Features
        'features' => [
            'auto_reply' => env('WHATSAPP_AUTO_REPLY', true),
            'invitation_lookup' => true,
            'help_commands' => true,
            'status_updates' => true,
        ],
    ],

    'payment' => [
        /*
        |--------------------------------------------------------------------------
        | Payment Webhook Settings
        |--------------------------------------------------------------------------
        |
        | Configuration for payment provider webhooks like Stripe, PayPal, etc.
        |
        */
        
        'stripe' => [
            'enabled' => env('STRIPE_WEBHOOK_ENABLED', false),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'verify_signature' => true,
        ],
        
        'paypal' => [
            'enabled' => env('PAYPAL_WEBHOOK_ENABLED', false),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
            'verify_signature' => true,
        ],
    ],

    'notifications' => [
        /*
        |--------------------------------------------------------------------------
        | Notification Webhook Settings
        |--------------------------------------------------------------------------
        |
        | Configuration for push notification service webhooks.
        |
        */
        
        'pusher' => [
            'enabled' => env('PUSHER_WEBHOOK_ENABLED', false),
            'webhook_secret' => env('PUSHER_WEBHOOK_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Global settings that apply to all webhooks.
    |
    */
    
    'global' => [
        'log_all_requests' => env('WEBHOOK_LOG_ALL_REQUESTS', true),
        'log_failed_requests' => env('WEBHOOK_LOG_FAILED_REQUESTS', true),
        'timeout' => env('WEBHOOK_TIMEOUT', 30),
        'retry_attempts' => env('WEBHOOK_RETRY_ATTEMPTS', 3),
        
        // Security
        'rate_limit' => [
            'requests' => 60,
            'per_minutes' => 1,
        ],
        
        'default_response' => [
            'success' => ['status' => 'success', 'message' => 'Webhook processed successfully'],
            'error' => ['status' => 'error', 'message' => 'Webhook processing failed'],
            'unauthorized' => ['status' => 'error', 'message' => 'Unauthorized webhook request'],
        ],
    ],
]; 
