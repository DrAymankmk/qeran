# Translated Notification System Usage

## Overview
This notification system automatically translates messages based on the user's preferred language. It supports Arabic (`ar`) and English (`en`) languages.

## Setup

### 1. Migration
Run the migration to add the language column to the users table:
```bash
php artisan migrate
```

### 2. User Language Setting
Set user language (default is 'ar'):
```php
$user = User::find(1);
$user->setPreferredLanguage('en'); // or 'ar'
$user->save();
```

## Usage Examples

### 1. Basic Notification with Translation
```php
use App\Services\External\Notification;

// Send order created notification
Notification::notify(
    'user',                    // user type
    'order',                   // notification type
    [1, 2, 3],                // array of user IDs
    123,                       // target ID (order ID)
    'order_created',           // translation key
    ['order_id' => 123]        // parameters for translation
);
```

### 2. Welcome Message
```php
Notification::notify(
    'user',
    'welcome',
    [1],
    0,
    'welcome',
    ['name' => 'أحمد'] // User name
);
```

### 3. Payment Notification
```php
Notification::notify(
    'user',
    'payment',
    [1, 2],
    456,
    'payment_successful',
    ['order_id' => 456]
);
```

### 4. Interest-based Notification
```php
// Send to all users with specific interest
Notification::notifyFor(
    'orders',                  // interest
    'system_maintenance',      // translation key
    ['time' => '2024-01-15 02:00'], // parameters
    'ar'                       // language (optional, default: 'ar')
);
```

### 5. Custom Messages (Backward Compatibility)
```php
// For existing code that uses custom messages
Notification::notifyWithCustomMessage(
    'user',
    'custom',
    [1],
    0,
    'Custom Title',
    'Custom Body Message'
);
```

## Language Files

### Adding New Notification Types
Add to `lang/ar/notifications.php`:
```php
'new_notification_type' => [
    'title' => 'العنوان بالعربية',
    'body' => 'الرسالة بالعربية مع معامل :parameter'
],
```

Add to `lang/en/notifications.php`:
```php
'new_notification_type' => [
    'title' => 'Title in English',
    'body' => 'Message in English with parameter :parameter'
],
```

## Available Translation Keys

- `welcome` - Welcome message
- `new_message` - New message notification
- `order_created` - Order creation
- `order_updated` - Order update
- `order_completed` - Order completion
- `order_cancelled` - Order cancellation
- `payment_successful` - Successful payment
- `payment_failed` - Failed payment
- `reminder` - General reminder
- `system_maintenance` - System maintenance
- `account_verified` - Account verification
- `password_changed` - Password change

## Method Signatures

### notify()
```php
public static function notify(
    string $user_type,        // 'user', 'admin', etc.
    string $notify_type,      // notification category
    array $users_id,          // array of user IDs
    int $target_id,           // related entity ID
    string $message_key,      // translation key
    array $params = []        // translation parameters
): void
```

### notifyFor()
```php
public static function notifyFor(
    string $interest,         // interest group
    string $message_key,      // translation key
    array $params = [],       // translation parameters
    string $language = 'ar'   // language (optional)
): void
```

## Database Structure

The notification system stores messages in the user's preferred language:
- User language is stored in `users.language` column
- Notifications are stored with translations in user's language
- Translation keys are resolved at runtime based on user preference

## Migration Information

The migration adds a `language` column to the users table:
```php
$table->string('language', 5)->default('ar')->after('email');
```

## Best Practices

1. **Use translation keys** instead of hardcoded messages
2. **Provide parameters** for dynamic content
3. **Keep language files updated** with new notification types
4. **Test with different languages** to ensure proper display
5. **Use meaningful translation keys** that describe the notification purpose 
