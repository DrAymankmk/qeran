<?php

namespace App\Traits;

use App\Models\Notification as NotificationModel;
use App\Models\User;
use App\Services\External\NotifyTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;

trait SendsNotificationAndEmail
{
    /**
     * Send notification via Pusher and email sequentially
     *
     * @param  string  $userType - Type of user (e.g., 'users', 'admins')
     * @param  array|int  $userIds - User ID(s) to notify
     * @param  string  $notifyType - Type of notification
     * @param  int  $targetId - Target ID (e.g., invitation_id, order_id)
     * @param  string  $notificationKey - Translation key or plain text title
     * @param  array  $params - Additional parameters for translation or plain text body
     * @param  bool  $useTranslation - Whether to use translation (default: true)
     * @param  string|null  $category - Notification category
     * @param  string|null  $notificationType - Notification type
     * @param  string|null  $emailView - Email view name (optional)
     * @param  array  $emailData - Email data (optional)
     * @param  string|null  $emailSubject - Email subject (optional)
     * @param  string|null  $emailTo - Email recipient (optional, defaults to user email)
     */
    public function sendNotificationAndEmail(
        string $userType,
        $userIds,
        string $notifyType,
        int $targetId,
        string $notificationKey,
        array $params = [],
        bool $useTranslation = true,
        ?string $category = null,
        ?string $notificationType = null,
        ?string $emailView = null,
        array $emailData = [],
        ?string $emailSubject = null,
        ?string $emailTo = 'Qeraninvitation@gmail.com'
    ): void {
        // Ensure userIds is an array
        $userIds = is_array($userIds) ? $userIds : [$userIds];

        foreach ($userIds as $userId) {
            try {
                Log::info("Starting notification process for user: {$userId}", [
                    'notification_key' => $notificationKey,
                    'user_type' => $userType,
                ]);

                $user = User::find($userId);
                if (! $user) {
                    Log::warning("User not found for notification: {$userId}");

                    continue;
                }

                // Step 1: Send real-time notification via Pusher
                try {
                    $this->sendPusherNotification($userType, $userId, $notifyType, $targetId, $notificationKey, $params, $useTranslation);
                    Log::info("Pusher notification sent for user: {$userId}");
                } catch (\Exception $e) {
                    Log::error("Failed to send Pusher notification for user {$userId}: ".$e->getMessage());
                    // Continue with other steps even if Pusher fails
                }

                // Step 2: Create notification in database
                try {
                    $notification = $this->createDatabaseNotification(
                        $userId,
                        $notifyType,
                        $targetId,
                        $notificationKey,
                        $params,
                        $useTranslation,
                        $category,
                        $notificationType
                    );
                    Log::info("Database notification created for user: {$userId}, notification ID: {$notification->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to create database notification for user {$userId}: ".$e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e; // Re-throw as this is critical
                }

                // Step 3: Send push notification (existing system)
                try {
                    $this->sendPushNotification($userType, $userId, $notifyType, $targetId, $notificationKey, $params, $useTranslation);
                    Log::info("Push notification sent for user: {$userId}");
                } catch (\Exception $e) {
                    Log::error("Failed to send push notification for user {$userId}: ".$e->getMessage());
                    // Continue with email even if push fails
                }

                // Step 4: Send email after notification
                if ($emailView || $emailSubject) {
                    try {
                        $this->sendEmail(
                            $user,
                            $emailView,
                            $emailData,
                            $emailSubject,
                            $emailTo,
                            $notificationKey,
                            $params,
                            $useTranslation
                        );
                        Log::info("Email sent for user: {$userId}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send email for user {$userId}: ".$e->getMessage(), [
                            'email_to' => $emailTo ?? $user->email,
                            'trace' => $e->getTraceAsString(),
                        ]);
                        // Don't throw - email failure shouldn't break the flow
                    }
                }

                Log::info("Notification process completed successfully for user: {$userId}");
            } catch (\Exception $e) {
                Log::error("Error sending notification and email for user {$userId}: ".$e->getMessage(), [
                    'user_id' => $userId,
                    'notification_key' => $notificationKey,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Re-throw to see the error in the response
                throw $e;
            }
        }
    }

    /**
     * Send real-time notification via Pusher
     */
    protected function sendPusherNotification(
        string $userType,
        int $userId,
        string $notifyType,
        int $targetId,
        string $notificationKey,
        array $params,
        bool $useTranslation
    ): void {
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $user = User::find($userId);
            if (! $user) {
                return;
            }

            // Get translated content
            if ($useTranslation) {
                $userLanguage = $user->language ?? 'ar';
                $translatedContent = $this->getTranslatedNotification($notificationKey, $userLanguage, $params);
                $title = $translatedContent['title'];
                $body = $translatedContent['body'];
            } else {
                $title = $notificationKey;
                $body = is_array($params) ? implode(' ', $params) : $params;
            }

            $channel = $userType.'-'.$userId;
            $event = 'notification-received';

            $data = [
                'type' => $notifyType,
                'target_id' => $targetId,
                'title' => $title,
                'body' => $body,
                'timestamp' => now()->utc()->toIso8601String(),
            ];

            $pusher->trigger($channel, $event, $data);

            // Also trigger for admin dashboard if userType is 'users'
            if ($userType === 'users') {
                $pusher->trigger('admin-notifications', 'new-notification', [
                    'user_id' => $userId,
                    'user_name' => $user->name ?? 'User',
                    'type' => $notifyType,
                    'target_id' => $targetId,
                    'title' => $title,
                    'body' => $body,
                    'timestamp' => now()->utc()->toIso8601String(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending Pusher notification: '.$e->getMessage());
        }
    }

    /**
     * Create notification in database
     */
    protected function createDatabaseNotification(
        int $userId,
        string $notifyType,
        int $targetId,
        string $notificationKey,
        array $params,
        bool $useTranslation,
        ?string $category,
        ?string $notificationType
    ): NotificationModel {
        $user = User::find($userId);

        if ($useTranslation) {
            // Get translated content for both languages
            $arContent = $this->getTranslatedNotification($notificationKey, 'ar', $params);
            $enContent = $this->getTranslatedNotification($notificationKey, 'en', $params);

            $notification = NotificationModel::create([
                'user_id' => $userId,
                'type' => $notifyType,
                'target_id' => $targetId,
                'category' => $category,
                'notification_type' => $notificationType,
            ]);

            $notification->translateOrNew('ar')->title = $arContent['title'];
            $notification->translateOrNew('en')->title = $enContent['title'];
            $notification->translateOrNew('ar')->description = $arContent['body'];
            $notification->translateOrNew('en')->description = $enContent['body'];
            $notification->save();
        } else {
            $title = $notificationKey;
            $body = is_array($params) ? implode(' ', $params) : $params;

            $notification = NotificationModel::create([
                'user_id' => $userId,
                'type' => $notifyType,
                'target_id' => $targetId,
                'category' => $category,
                'notification_type' => $notificationType,
            ]);

            $notification->translateOrNew('ar')->title = $title;
            $notification->translateOrNew('en')->title = $title;
            $notification->translateOrNew('ar')->description = $body;
            $notification->translateOrNew('en')->description = $body;
            $notification->save();
        }

        // Update user's notifications count
        $user->update(['notifications_count' => ($user->notifications_count + 1)]);

        return $notification;
    }

    /**
     * Send push notification (existing system)
     */
    protected function sendPushNotification(
        string $userType,
        int $userId,
        string $notifyType,
        int $targetId,
        string $notificationKey,
        array $params,
        bool $useTranslation
    ): void {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        if ($useTranslation) {
            $userLanguage = $user->language ?? 'ar';
            $translatedContent = $this->getTranslatedNotification($notificationKey, $userLanguage, $params);
            $title = $translatedContent['title'];
            $body = $translatedContent['body'];
        } else {
            $title = $notificationKey;
            $body = is_array($params) ? implode(' ', $params) : $params;
        }

        $data = [
            'type' => $notifyType,
            'target_id' => (int) $targetId,
            'title' => $title,
            'body' => $body,
            'sound' => 'default',
        ];

        NotifyTo::send($userType, $userId, $data);
    }

    /**
     * Send email after notification
     */
    protected function sendEmail(
        User $user,
        ?string $emailView,
        array $emailData,
        ?string $emailSubject,
        ?string $emailTo,
        string $notificationKey,
        array $params,
        bool $useTranslation
    ): void {
        try {
            $recipientEmail = $emailTo ?? $user->email;

            if (! $recipientEmail) {
                Log::warning("No email address found for user {$user->id}");

                return;
            }

            // Get translated content for email
            if ($useTranslation) {
                $userLanguage = $user->language ?? 'ar';
                $translatedContent = $this->getTranslatedNotification($notificationKey, $userLanguage, $params);
                $emailTitle = $translatedContent['title'];
                $emailBody = $translatedContent['body'];
            } else {
                $emailTitle = $notificationKey;
                $emailBody = is_array($params) ? implode(' ', $params) : $params;
            }

            // If custom view is provided, use it; otherwise use default
            if ($emailView) {
                Mail::send($emailView, array_merge($emailData, [
                    'user' => $user,
                    'title' => $emailTitle,
                    'body' => $emailBody,
                ]), function ($message) use ($recipientEmail, $emailSubject, $emailTitle) {
                    $message->to($recipientEmail)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($emailSubject ?? $emailTitle);
                });
            } else {
                // Use default notification email template
                Mail::send('emails.notification', [
                    'user' => $user,
                    'title' => $emailTitle,
                    'body' => $emailBody,
                ], function ($message) use ($recipientEmail, $emailSubject, $emailTitle) {
                    $message->to($recipientEmail)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($emailSubject ?? $emailTitle);
                });
            }
        } catch (\Exception $e) {
            Log::error("Error sending email for user {$user->id}: ".$e->getMessage());
        }
    }

    /**
     * Get translated notification content
     */
    protected function getTranslatedNotification(string $notificationKey, string $language, array $params = []): array
    {
        $originalLocale = app()->getLocale();
        app()->setLocale($language);

        try {
            $title = __("notifications.{$notificationKey}.title", $params);
            $body = __("notifications.{$notificationKey}.body", $params);

            if (str_contains($title, 'notifications.')) {
                $title = __('notifications.default.title', $params);
                $body = __('notifications.default.body', $params);
            }

            return [
                'title' => $title,
                'body' => $body,
            ];
        } finally {
            app()->setLocale($originalLocale);
        }
    }

    /**
     * Get email template path based on category and type
     *
     * @param  int|null  $category - Notification category constant
     * @param  int|null  $notificationType - Notification type constant
     * @return string - Email template path
     */
    protected function getEmailTemplate(?int $category = null, ?int $notificationType = null): string
    {
        $constants = \App\Helpers\Constant::class;

        // Determine category name
        $categoryName = null;
        if ($category) {
            $categories = $constants::NOTIFICATION_CATEGORY;
            $categoryName = array_search($category, $categories);
        }

        // Determine type name
        $typeName = null;
        if ($category && $notificationType) {
            $types = [];
            switch ($category) {
                case $constants::NOTIFICATION_CATEGORY['Order'] ?? 1:
                    $types = $constants::NOTIFICATION_ORDER_TYPES ?? [];
                    break;
                case $constants::NOTIFICATION_CATEGORY['Payment'] ?? 2:
                    $types = $constants::NOTIFICATION_PAYMENT_TYPES ?? [];
                    break;
                case $constants::NOTIFICATION_CATEGORY['User'] ?? 3:
                    $types = $constants::NOTIFICATION_USER_TYPES ?? [];
                    break;
                case $constants::NOTIFICATION_CATEGORY['Contact Us'] ?? 4:
                    $types = $constants::NOTIFICATION_CONTACT_TYPES ?? [];
                    break;
            }

            if (! empty($types)) {
                $typeName = array_search($notificationType, $types);
            }
        }

        // Build template path: emails/category/type or emails/category/default or emails/default
        $templatePath = 'emails.notification'; // Default fallback

        if ($categoryName && $typeName) {
            // Try specific template: emails/category/type (e.g., emails.order.new_order_created)
            $specificTemplate = 'emails.'.strtolower(str_replace(' ', '_', $categoryName)).'.'.strtolower(str_replace(' ', '_', $typeName));
            if (view()->exists($specificTemplate)) {
                return $specificTemplate;
            }
        }

        if ($categoryName) {
            // Try category template: emails/category/default (e.g., emails.order.default)
            $categoryTemplate = 'emails.'.strtolower(str_replace(' ', '_', $categoryName)).'.default';
            if (view()->exists($categoryTemplate)) {
                return $categoryTemplate;
            }

            // Try category main template: emails/category (e.g., emails.order)
            $categoryMainTemplate = 'emails.'.strtolower(str_replace(' ', '_', $categoryName));
            if (view()->exists($categoryMainTemplate)) {
                return $categoryMainTemplate;
            }
        }

        // Return default template
        return $templatePath;
    }

    /**
     * Send admin notification (simplified method for admin notifications)
     *
     * This method creates an admin notification (user_id = null), sends Pusher notification,
     * and optionally sends an email. Perfect for system events like new user registration.
     *
     * @param  string  $notificationKey - Translation key from lang/notifications.php
     * @param  int  $targetId - Target ID (e.g., user_id, invitation_id)
     * @param  array  $params - Parameters for translation
     * @param  int|null  $category - Notification category constant
     * @param  int|null  $notificationType - Notification type constant
     * @param  string|null  $emailTo - Email recipient (optional)
     * @param  string|null  $emailSubject - Custom email subject (optional)
     * @param  string|null  $emailView - Custom email view (optional, will auto-detect if not provided)
     * @param  array  $emailData - Additional data for email template (optional)
     */
    public function sendAdminNotification(
        string $notificationKey,
        int $targetId,
        array $params = [],
        ?int $category = null,
        ?int $notificationType = null,
        ?string $emailTo = null,
        ?string $emailSubject = null,
        ?string $emailView = null,
        array $emailData = []
    ): void {
        try {
            // Get translated content for both languages
            $arContent = $this->getTranslatedNotification($notificationKey, 'ar', $params);
            $enContent = $this->getTranslatedNotification($notificationKey, 'en', $params);

            // Create admin notification (user_id = null for admin notifications)
            $notification = \App\Models\Notification::create([
                'user_id' => null,
                'type' => \App\Helpers\Constant::NOTIFICATIONS_TYPE['Admin'] ?? 0,
                'target_id' => $targetId,
                'category' => $category,
                'notification_type' => $notificationType,
            ]);

            // Store translations
            $notification->translateOrNew('ar')->title = $arContent['title'];
            $notification->translateOrNew('en')->title = $enContent['title'];
            $notification->translateOrNew('ar')->description = $arContent['body'];
            $notification->translateOrNew('en')->description = $enContent['body'];
            $notification->save();

            Log::info('Admin notification created', [
                'notification_id' => $notification->id,
                'target_id' => $targetId,
                'category' => $category,
            ]);

            // Send Pusher notification to admin dashboard (skip if disabled or in local dev with time sync issues)
            $pusherKey = config('broadcasting.connections.pusher.key');
            $pusherSecret = config('broadcasting.connections.pusher.secret');
            $pusherAppId = config('broadcasting.connections.pusher.app_id');

            // Check if Pusher is disabled via environment variable (useful for local development)
            $pusherEnabled = env('PUSHER_ENABLED', true);
            if (is_string($pusherEnabled)) {
                $pusherEnabled = filter_var($pusherEnabled, FILTER_VALIDATE_BOOLEAN);
            }

            if ($pusherEnabled && ! empty($pusherKey) && ! empty($pusherSecret) && ! empty($pusherAppId)) {
                try {
                    $pusherOptions = config('broadcasting.connections.pusher.options', []);

                    // Ensure cluster is set in options
                    $cluster = env('PUSHER_APP_CLUSTER', 'mt1');
                    if (! isset($pusherOptions['cluster'])) {
                        $pusherOptions['cluster'] = $cluster;
                    }

                    // Create fresh Pusher instance to avoid timestamp caching issues
                    $pusher = new Pusher(
                        $pusherKey,
                        $pusherSecret,
                        $pusherAppId,
                        $pusherOptions
                    );

                    // Generate current timestamp for payload
                    $currentTimestamp = now()->utc()->toIso8601String();

                    $pusher->trigger('admin-notifications', 'new-notification', [
                        'user_id' => null,
                        'user_type' => 'admin',
                        'type' => \App\Helpers\Constant::NOTIFICATIONS_TYPE['Admin'] ?? 0,
                        'target_id' => $targetId,
                        'title' => $enContent['title'],
                        'body' => $enContent['body'],
                        'category' => $category,
                        'notification_type' => $notificationType,
                        'timestamp' => $currentTimestamp,
                    ]);

                    Log::info('Pusher notification sent to admin dashboard', [
                        'target_id' => $targetId,
                        'timestamp' => $currentTimestamp,
                    ]);
                } catch (\Exception $e) {
                    // Check if it's a timestamp error (server time sync issue)
                    $isTimestampError = str_contains($e->getMessage(), 'Timestamp expired') ||
                                       str_contains($e->getMessage(), 'timestamp');

                    if ($isTimestampError) {
                        // Log as warning - this is a server configuration issue, not a code issue
                        $isLocal = app()->environment('local') || app()->environment('development');
                        $logMessage = 'Pusher timestamp error (server time out of sync): '.$e->getMessage();

                        if ($isLocal) {
                            $logMessage .= ' | Running locally - add PUSHER_ENABLED=false to .env to disable Pusher notifications';
                        }

                        Log::warning($logMessage, [
                            'target_id' => $targetId,
                            'server_time' => now()->utc()->toIso8601String(),
                            'server_timestamp' => time(),
                            'environment' => app()->environment(),
                            'note' => $isLocal
                                ? 'Local development: Sync Windows time or add PUSHER_ENABLED=false to .env'
                                : 'Email notification will still be sent. Please sync server time with NTP.',
                        ]);
                    } else {
                        // Log other errors normally
                        Log::error('Failed to send Pusher notification: '.$e->getMessage(), [
                            'target_id' => $targetId,
                            'error_trace' => substr($e->getTraceAsString(), 0, 500), // Limit trace length
                            'server_time' => now()->utc()->toIso8601String(),
                        ]);
                    }
                    // Continue execution - Pusher failure shouldn't prevent email notification
                }
            } else {
                if (! $pusherEnabled) {
                    Log::debug('Pusher notifications disabled via PUSHER_ENABLED environment variable', [
                        'target_id' => $targetId,
                    ]);
                } else {
                    Log::debug('Pusher not configured, skipping real-time notification', [
                        'target_id' => $targetId,
                    ]);
                }
            }

            // Send email if recipient is provided
            if ($emailTo) {
                try {
                    // Get sender email from configuration - prioritize MAIL_USERNAME to avoid blocked domains
                    $senderEmail = env('MAIL_USERNAME');
                    if (empty($senderEmail)) {
                        $senderEmail = config('mail.mailers.smtp.username');
                    }
                    if (empty($senderEmail)) {
                        $senderEmail = env('MAIL_FROM_ADDRESS');
                    }
                    // Only use config('mail.from.address') as last resort if it's not the blocked domain
                    if (empty($senderEmail)) {
                        $fallbackEmail = config('mail.from.address');
                        // Avoid using blocked domain
                        if ($fallbackEmail && ! str_contains($fallbackEmail, 'modern-invitation.com') && ! str_contains($fallbackEmail, 'example.com')) {
                            $senderEmail = $fallbackEmail;
                        }
                    }

                    $senderName = config('mail.from.name', 'Modern Invitation');
                    if (empty($senderName) || $senderName === 'Example') {
                        $senderName = config('app.name', 'Modern Invitation');
                    }

                    if (empty($senderEmail) || ! filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception('No valid sender email configured. Please set MAIL_USERNAME in .env file');
                    }

                    // Log the sender email being used for debugging
                    Log::info('Using sender email for admin notification', [
                        'sender_email' => $senderEmail,
                        'mail_username' => env('MAIL_USERNAME'),
                        'mail_from_address' => env('MAIL_FROM_ADDRESS'),
                    ]);

                    // Determine email template
                    $template = $emailView ?? $this->getEmailTemplate($category, $notificationType);

                    // Prepare email data
                    $mailData = array_merge([
                        'title' => $enContent['title'],
                        'body' => $enContent['body'],
                        'params' => $params,
                        'category' => $category,
                        'notificationType' => $notificationType,
                        'targetId' => $targetId,
                    ], $emailData);

                    Mail::send($template, $mailData, function ($message) use ($emailTo, $senderEmail, $senderName, $emailSubject, $enContent) {
                        $message->to($emailTo)
                            ->from($senderEmail, $senderName)
                            ->subject($emailSubject ?? $enContent['title']);
                    });

                    Log::info('Email template used', ['template' => $template]);

                    Log::info('Admin notification email sent', [
                        'to' => $emailTo,
                        'from' => $senderEmail,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send admin notification email: '.$e->getMessage(), [
                        'error' => $e->getMessage(),
                        'mail_username' => config('mail.mailers.smtp.username'),
                        'mail_from_address' => config('mail.from.address'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: '.$e->getMessage(), [
                'notification_key' => $notificationKey,
                'target_id' => $targetId,
                'error' => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw to allow caller to handle if needed
        }
    }
}
