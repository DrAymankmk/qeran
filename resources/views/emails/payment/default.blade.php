<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Payment Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
        }
        .header {
            background-color: #ffc107;
            color: #333;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        .body {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .payment-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
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
            <p style="margin: 0; font-size: 14px;">Payment Notification</p>
        </div>
        <div class="content">
            <div class="title">{{ $title ?? 'Payment Notification' }}</div>
            <div class="body">
                {!! nl2br(e($body ?? '')) !!}
            </div>
            @if(isset($targetId))
            <div class="payment-box">
                <strong>Payment ID:</strong> #{{ $targetId }}
            </div>
            @endif
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Modern Invitation') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
























