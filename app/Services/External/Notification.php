<?php

namespace App\Services\External;

use App\Models\Notification as NotificationModel;
use App\Models\PersonalFirebaseToken;
use App\Models\User;
use App\Services\External\NotifyTo;

class Notification
{
    /**
     * Method a query to only notify users with translation support
     * @param  $user_type
     * @param  $notify_type
     * @param  $users_id
     * @param  $target_id
     * @param  $notification_key - Translation key from lang/notifications.php OR plain text title
     * @param  $params - Additional parameters for translation OR plain text body
     * @param  $use_translation - Whether to use translation (default: true)
     * @return void
     */

    public static function notify(
        $user_type,
        $notify_type, $users_id,
        $target_id, $notification_key, $params = [], $use_translation = true): void
    {
        if (count($users_id) > 0) {
            foreach ($users_id as $user_id) {
                $user = User::whereId($user_id)->first();
                if (!$user) continue;
                
                if ($use_translation) {
                    // Get user's language preference, default to 'ar'
                    $userLanguage = $user->language ?? 'ar';
                    
                    // Get translated notification content
                    $translatedContent = self::getTranslatedNotification($notification_key, $userLanguage, $params);
                    
                    // Create notification in database
                    $notification = NotificationModel::create([
                        'user_id' => $user_id,
                        'type' => $notify_type,
                        'target_id' => $target_id,
                    ]);

                    // Store translated content for both languages
                    $notification->translateOrNew('ar')->title = self::getTranslatedNotification($notification_key, 'ar', $params)['title'];
                    $notification->translateOrNew('en')->title = self::getTranslatedNotification($notification_key, 'en', $params)['title'];
                    $notification->translateOrNew('ar')->description = self::getTranslatedNotification($notification_key, 'ar', $params)['body'];
                    $notification->translateOrNew('en')->description = self::getTranslatedNotification($notification_key, 'en', $params)['body'];
                    $notification->save();
                    
                    // Update user's notifications count
                    $user->update(['notifications_count' => ($user->notifications_count + 1)]);

                    // Send push notification with translated content
                    $data = [
                        'type' => $notify_type,
                        'target_id' => (int)$target_id,
                        'title' => $translatedContent['title'],
                        'body' => $translatedContent['body'],
                        'sound' => 'default',
                    ];
                    
                    NotifyTo::send($user_type, $user_id, $data);
                } else {
                    // Use plain text without translation
                    $title = $notification_key;
                    $body = is_array($params) ? implode(' ', $params) : $params;
                    
                    // Create notification in database
                    $notification = NotificationModel::create([
                        'user_id' => $user_id,
                        'type' => $notify_type,
                        'target_id' => $target_id,
                    ]);

                    // Store same content for both languages (plain text)
                    $notification->translateOrNew('ar')->title = $title;
                    $notification->translateOrNew('en')->title = $title;
                    $notification->translateOrNew('ar')->description = $body;
                    $notification->translateOrNew('en')->description = $body;
                    $notification->save();
                    
                    // Update user's notifications count
                    $user->update(['notifications_count' => ($user->notifications_count + 1)]);

                    // Send push notification with plain text
                    $data = [
                        'type' => $notify_type,
                        'target_id' => (int)$target_id,
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                    ];
                    
                    NotifyTo::send($user_type, $user_id, $data);
                }
            }
        }
    }
    public static function notifyFor(
        $interest,
        $notification_key, $params = []): void
    {
        // Use default language for general notifications
        $translatedContent = self::getTranslatedNotification($notification_key, 'ar', $params);
        
        $data = [
            'type' => 'admin',
            'target_id' => 0,
            'title' => $translatedContent['title'],
            'body' => $translatedContent['body'],
            'sound' => 'default',
        ];
        
        NotifyFor::send($interest, $data);
    }

    /**
     * Get translated notification content
     * @param string $notification_key
     * @param string $language
     * @param array $params
     * @return array
     */
    private static function getTranslatedNotification($notification_key, $language, $params = [])
    {
        // Set the application locale temporarily
        $originalLocale = app()->getLocale();
        app()->setLocale($language);
        
        try {
            // Try to get the specific notification translation
            $title = __("notifications.{$notification_key}.title", $params);
            $body = __("notifications.{$notification_key}.body", $params);
            
            // If translation key doesn't exist, use default
            if (str_contains($title, 'notifications.')) {
                $title = __('notifications.default.title', $params);
                $body = __('notifications.default.body', $params);
            }
            
            return [
                'title' => $title,
                'body' => $body
            ];
        } finally {
            // Restore original locale
            app()->setLocale($originalLocale);
        }
    }

    /**
     * Convenient methods for common notification types
     */
    
    public static function sendWelcomeNotification($user_type, $user_ids)
    {
        self::notify($user_type, 'welcome', $user_ids, 0, 'welcome');
    }
    
    public static function sendInvitationNotification($user_type, $user_ids, $invitation_id, $type = 'invitation_created')
    {
        self::notify($user_type, $type, $user_ids, $invitation_id, $type);
    }
    
    public static function sendOrderNotification($user_type, $user_ids, $order_id, $type = 'order_created')
    {
        self::notify($user_type, $type, $user_ids, $order_id, $type);
    }
    
    public static function sendPaymentNotification($user_type, $user_ids, $payment_id, $type = 'payment_success')
    {
        self::notify($user_type, $type, $user_ids, $payment_id, $type);
    }
    
    public static function sendRatingNotification($user_type, $user_ids, $rating_id, $type = 'rating_received')
    {
        self::notify($user_type, $type, $user_ids, $rating_id, $type);
    }
    
    public static function sendMessageNotification($user_type, $user_ids, $message_id, $type = 'new_message')
    {
        self::notify($user_type, $type, $user_ids, $message_id, $type);
    }
    
    public static function sendAdminNotification($interest = 'all', $type = 'admin_message')
    {
        self::notifyFor($interest, $type);
    }

}
