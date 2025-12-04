<?php

namespace App\Services;

use App\Traits\SendsNotificationAndEmail;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class PusherNotificationService
{
    use SendsNotificationAndEmail;

    /**
     * Send notification to admin dashboard via Pusher
     *
     * @param string $type - Notification type
     * @param int $targetId - Target ID
     * @param string $title - Notification title
     * @param string $body - Notification body
     * @param string|null $category - Notification category
     * @param string|null $notificationType - Notification type
     * @return void
     */
    public function notifyAdmin(
        string $type,
        int $targetId,
        string $title,
        string $body,
        ?string $category = null,
        ?string $notificationType = null
    ): void {
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $data = [
                'type' => $type,
                'target_id' => $targetId,
                'title' => $title,
                'body' => $body,
                'category' => $category,
                'notification_type' => $notificationType,
                'timestamp' => now()->utc()->toIso8601String(),
            ];

            $pusher->trigger('admin-notifications', 'new-notification', $data);
        } catch (\Exception $e) {
            Log::error("Error sending Pusher notification to admin: " . $e->getMessage());
        }
    }

    /**
     * Send notification to specific user channel
     *
     * @param string $userType - User type (e.g., 'users', 'admins')
     * @param int $userId - User ID
     * @param string $event - Event name
     * @param array $data - Data to send
     * @return void
     */
    public function notifyUser(string $userType, int $userId, string $event, array $data): void
    {
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $channel = $userType . '-' . $userId;
            $pusher->trigger($channel, $event, $data);
        } catch (\Exception $e) {
            Log::error("Error sending Pusher notification to user: " . $e->getMessage());
        }
    }
}


