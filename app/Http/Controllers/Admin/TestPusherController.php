<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PusherNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class TestPusherController extends Controller
{
    /**
     * Test Pusher connection and send a test notification
     */
    public function test(Request $request)
    {
        try {
            // Check configuration
            $config = [
                'driver' => config('broadcasting.default'),
                'key' => config('broadcasting.connections.pusher.key'),
                'secret' => config('broadcasting.connections.pusher.secret'),
                'app_id' => config('broadcasting.connections.pusher.app_id'),
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'options' => config('broadcasting.connections.pusher.options'),
            ];

            // Check if Pusher is configured
            if ($config['driver'] !== 'pusher') {
                return response()->json([
                    'success' => false,
                    'message' => 'BROADCAST_DRIVER is not set to pusher',
                    'config' => $config
                ], 400);
            }

            if (empty($config['key']) || empty($config['secret']) || empty($config['app_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pusher credentials are missing',
                    'config' => [
                        'key_set' => !empty($config['key']),
                        'secret_set' => !empty($config['secret']),
                        'app_id_set' => !empty($config['app_id']),
                        'cluster' => $config['cluster']
                    ]
                ], 400);
            }

            // Initialize Pusher
            $pusher = new Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options']
            );

            // Send test notification
            $testData = [
                'type' => 'test',
                'target_id' => 0,
                'title' => 'Test Notification',
                'body' => 'This is a test notification sent at ' . now()->utc()->toDateTimeString(),
                'category' => 'Test',
                'notification_type' => 'test',
                'timestamp' => now()->utc()->toIso8601String(),
            ];

            $result = $pusher->trigger('admin-notifications', 'new-notification', $testData);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully!',
                'data' => $testData,
                'pusher_response' => $result,
                'config_check' => [
                    'driver' => $config['driver'],
                    'key_length' => strlen($config['key']),
                    'secret_length' => strlen($config['secret']),
                    'app_id' => $config['app_id'],
                    'cluster' => $config['cluster']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Pusher test error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending test notification: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Check Pusher configuration
     */
    public function checkConfig()
    {
        $config = [
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_key' => config('broadcasting.connections.pusher.key') ? 'Set (' . strlen(config('broadcasting.connections.pusher.key')) . ' chars)' : 'Not set',
            'pusher_secret' => config('broadcasting.connections.pusher.secret') ? 'Set (' . strlen(config('broadcasting.connections.pusher.secret')) . ' chars)' : 'Not set',
            'pusher_app_id' => config('broadcasting.connections.pusher.app_id') ?: 'Not set',
            'pusher_cluster' => env('PUSHER_APP_CLUSTER', 'Not set'),
            'pusher_options' => config('broadcasting.connections.pusher.options'),
        ];

        return response()->json($config);
    }
}

