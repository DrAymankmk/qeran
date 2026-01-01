<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.contact_us_reply') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
        }
        .header {
            background-color: #556ee6;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .subject-section {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .subject-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .original-message {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #556ee6;
            margin: 20px 0;
            border-radius: 5px;
        }
        .reply-message {
            background-color: #e8f4f8;
            padding: 15px;
            border-left: 3px solid #28a745;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'Modern Invitation') }}</h1>
        </div>
        <div class="content">
            <div class="greeting">
                {{ __('Hello') }} {{ $contact->name }},
            </div>
            
            <p>{{ __('admin.contact_reply_greeting') }}</p>
            
            <div class="subject-section">
                <div class="subject-label">{{ __('admin.subject') }}:</div>
                <div>{{ $contact->subject }}</div>
            </div>
            
            @if($contact->message)
            <div class="original-message">
                <strong>{{ __('admin.your_message') }}:</strong><br>
                {!! nl2br(e($contact->message)) !!}
            </div>
            @endif
            
            <div class="reply-message">
                <strong>{{ __('admin.our_reply') }}:</strong><br>
                {!! nl2br(e($replyMessage)) !!}
            </div>
            
            <p style="margin-top: 20px;">{{ __('admin.contact_reply_closing') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Modern Invitation') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>






































