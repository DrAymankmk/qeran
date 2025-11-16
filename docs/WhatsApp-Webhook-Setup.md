# WhatsApp Webhook Handler

This document explains how to set up and use the WhatsApp webhook handler in your Modern Invitation application.

## Overview

The WhatsApp webhook handler allows your application to receive and respond to incoming WhatsApp messages automatically. Users can interact with your invitation system through WhatsApp by sending commands.

## Features

- **Auto-reply to WhatsApp messages**
- **Invitation lookup and status checking**
- **Help commands in Arabic and English**
- **User recognition by phone number**
- **Secure webhook verification**
- **Rate limiting and logging**

## Setup Instructions

### 1. Environment Configuration

Add the following variables to your `.env` file:

```env
# WhatsApp Webhook Settings
WHATSAPP_WEBHOOK_ENABLED=true
WHATSAPP_WEBHOOK_TOKEN=your_webhook_token_here
WHATSAPP_VERIFY_SIGNATURE=true
WHATSAPP_AUTO_REPLY=true

# UltraMessage Settings (if using UltraMessage)
ULTRAMSG_INSTANCE_ID=instance78179
ULTRAMSG_TOKEN=your_ultramsg_token
ULTRAMSG_API_URL=https://api.ultramsg.com

# Webhook Logging
WEBHOOK_LOG_ALL_REQUESTS=true
WEBHOOK_LOG_FAILED_REQUESTS=true
```

### 2. Webhook URL

Configure your WhatsApp provider to send webhooks to:

```
https://yourdomain.com/api/webhooks/whatsapp
```

### 3. IP Whitelist

Update the allowed IPs in `config/webhooks.php` based on your WhatsApp provider:

```php
'allowed_ips' => [
    '185.37.37.37', // UltraMessage IP
    'your.provider.ip.address',
    '127.0.0.1', // Local development
],
```

## Supported Commands

Users can send the following commands via WhatsApp:

### Arabic Commands
- `مساعدة` - Display help message
- `دعوات` - Show user's invitations
- `حالة [رقم الدعوة]` - Check invitation status
- `[رقم الدعوة]` - Quick invitation lookup

### English Commands
- `help` - Display help message
- `invitations` - Show user's invitations
- `status [invitation_id]` - Check invitation status
- `[invitation_id]` - Quick invitation lookup

## How It Works

### 1. Message Reception
When a user sends a WhatsApp message, the webhook receives it and:
- Verifies the webhook signature
- Identifies the user by phone number
- Processes the message content
- Generates an appropriate response

### 2. User Identification
The system matches incoming messages to users by:
- Exact phone number match
- Country code + phone number combination
- Cleaned phone number (digits only)

### 3. Response Generation
Based on the message content, the system:
- Parses commands and parameters
- Queries the database for relevant information
- Formats responses in Arabic (default)
- Sends replies via UltraMessage API

## API Endpoints

### Webhook Endpoint
- **URL**: `POST /api/webhooks/whatsapp`
- **Middleware**: `webhook.verify:whatsapp`, `throttle:60,1`
- **Security**: IP whitelist, token verification

## Database Integration

The webhook integrates with your existing models:
- **User**: Identifies users by phone number
- **Invitation**: Retrieves user's invitations
- **Constants**: Uses invitation status constants

## Error Handling

The webhook handles various error scenarios:
- Unknown users
- Invalid invitation IDs
- Malformed commands
- Network timeouts
- Database errors

All errors are logged for debugging purposes.

## Security Features

### 1. Signature Verification
- Validates webhook signatures
- Checks IP whitelist
- Verifies authentication tokens

### 2. Rate Limiting
- Limits requests to 60 per minute per IP
- Prevents spam and abuse

### 3. Logging
- Logs all webhook requests
- Records processing errors
- Tracks user interactions

## Testing

### Local Development
1. Set `APP_ENV=local` in your `.env`
2. The webhook will accept requests from localhost
3. Use tools like ngrok to expose your local server

### Production Testing
1. Configure your WhatsApp provider's webhook URL
2. Send test messages to verify functionality
3. Monitor logs for any issues

## Troubleshooting

### Common Issues

1. **Webhook not receiving messages**
   - Check webhook URL configuration
   - Verify IP whitelist settings
   - Ensure webhook token is correct

2. **User not found errors**
   - Verify phone number format in database
   - Check country code handling
   - Ensure user exists in system

3. **Messages not sending**
   - Verify UltraMessage credentials
   - Check API rate limits
   - Ensure sufficient account balance

### Log Files
Check the following log files for debugging:
- `storage/logs/laravel.log` - General application logs
- Look for "WhatsApp Webhook" entries

## Customization

### Adding New Commands
To add new commands, modify the `processMessage` method in `WhatsAppController.php`:

```php
case str_contains($messageText, 'new_command'):
    return $this->handleNewCommand($user, $messageData);
```

### Changing Response Language
Modify the response messages in the controller methods or create a translation system.

### Custom Webhook Providers
To support other WhatsApp providers:
1. Update the `extractMessageData` method
2. Modify the signature verification logic
3. Adjust the webhook type detection

## Support

For issues or questions:
1. Check the application logs
2. Verify configuration settings
3. Test with simple commands first
4. Contact your WhatsApp provider for API issues 
