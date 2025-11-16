# Translated Notification System Usage

## Overview

The notification system now supports automatic translation based on user language preferences. Users can have their language set to either `ar` (Arabic) or `en` (English), and notifications will be sent in their preferred language.

## How It Works

1. **User Language**: Each user has a `language` column in the database (default: 'ar')
2. **Translation Files**: Notification messages are stored in language files:
   - `lang/ar/notifications.php` - Arabic translations
   - `lang/en/notifications.php` - English translations
3. **Automatic Translation**: The system automatically sends notifications in the user's preferred language

## Usage Examples

### Basic Notification with Translation Key

```php
use App\Services\External\Notification;

// Send notification using translation key
Notification::notify(
    'user',                    // user_type
    'invitation_created',      // notify_type
    [1, 2, 3],                // user_ids array
    123,                       // target_id (invitation_id)
    'invitation_created'       // notification_key from lang files
);
```

### Notification with Parameters

```php
// Add parameters to translation (if needed in future)
Notification::notify(
    'user',
    'order_completed',
    [1, 2, 3],
    456,
    'order_completed',
    ['order_number' => 'ORD123'] // parameters for translation
);
```

### Convenient Methods

The system provides convenient methods for common notification types:

```php
// Welcome notification
Notification::sendWelcomeNotification('user', [1, 2, 3]);

// Invitation notifications
Notification::sendInvitationNotification('user', [1, 2, 3], $invitation_id, 'invitation_created');
Notification::sendInvitationNotification('user', [1, 2, 3], $invitation_id, 'invitation_shared');

// Order notifications
Notification::sendOrderNotification('user', [1, 2, 3], $order_id, 'order_created');
Notification::sendOrderNotification('user', [1, 2, 3], $order_id, 'order_completed');

// Payment notifications
Notification::sendPaymentNotification('user', [1, 2, 3], $payment_id, 'payment_success');
Notification::sendPaymentNotification('user', [1, 2, 3], $payment_id, 'payment_failed');

// Rating notifications
Notification::sendRatingNotification('user', [1, 2, 3], $rating_id, 'rating_received');

// Message notifications
Notification::sendMessageNotification('user', [1, 2, 3], $message_id, 'new_message');

// Admin notifications to all users
Notification::sendAdminNotification('all', 'admin_message');
```

### Group Notifications

```php
// Send to specific interest groups
Notification::notifyFor('users', 'system_update');
Notification::notifyFor('providers', 'system_maintenance');
Notification::notifyFor('all', 'admin_message');
```

## Available Translation Keys

### General Notifications
- `welcome` - Welcome message for new users
- `default` - Fallback for missing translations

### Invitation Notifications
- `invitation_created` - New invitation created
- `invitation_updated` - Invitation updated
- `invitation_deleted` - Invitation deleted
- `invitation_shared` - Invitation shared with user
- `invitation_reminder` - Reminder about invitation

### User Notifications
- `profile_updated` - Profile updated successfully
- `password_changed` - Password changed successfully

### System Notifications
- `system_maintenance` - System maintenance notification
- `system_update` - System update notification

### Admin Notifications
- `admin_message` - General admin message

### Order/Package Notifications
- `order_created` - New order created
- `order_updated` - Order updated
- `order_completed` - Order completed
- `order_cancelled` - Order cancelled

### Payment Notifications
- `payment_success` - Payment successful
- `payment_failed` - Payment failed

### Rating Notifications
- `rating_received` - New rating received
- `rating_reminder` - Rating reminder

### Message Notifications
- `new_message` - New message received
- `message_reply` - Message reply received

## Database Changes

### Migration Applied
- Added `language` column to `users` table
- Default value: 'ar'
- Length: 5 characters

### User Model Updated
- Added `language` to fillable array
- Users can now have language preference stored

## How Translation Works

1. **User Language Detection**: System gets user's language from database
2. **Translation Lookup**: System looks up the translation key in appropriate language file
3. **Fallback**: If translation key doesn't exist, uses default notification
4. **Locale Management**: Temporarily sets Laravel locale for translation, then restores original

## Migration Instructions

1. Run the migration: `php artisan migrate`
2. Update existing users' language preference if needed
3. Start using the new notification methods with translation keys

## Benefits

- **Automatic Translation**: No need to manually handle translations
- **User Experience**: Users receive notifications in their preferred language
- **Centralized Management**: All notification texts in language files
- **Backward Compatible**: Easy to migrate existing notification calls
- **Extensible**: Easy to add new languages or notification types

## Adding New Notification Types

1. Add translation keys to both `lang/ar/notifications.php` and `lang/en/notifications.php`
2. Use the new key in your notification calls
3. Optionally add a convenient method in the Notification class

## Example Language File Structure

```php
// lang/ar/notifications.php
return [
    'your_new_notification' => [
        'title' => 'عنوان الإشعار',
        'body' => 'محتوى الإشعار'
    ],
];

// lang/en/notifications.php
return [
    'your_new_notification' => [
        'title' => 'Notification Title',
        'body' => 'Notification Body'
    ],
];
```
