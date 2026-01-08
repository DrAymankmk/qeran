<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'New Payment Received' }}</title>
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
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
            padding: 30px;
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
            color: #ff9800;
        }
        .body {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .payment-info {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-info h3 {
            margin-top: 0;
            color: #ff9800;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ffeaa7;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #212529;
            font-size: 18px;
            font-weight: bold;
        }
        .amount {
            font-size: 32px;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ffc107;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">💰 New Payment Received</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">A new payment has been received</p>
        </div>
        <div class="content">
            <div class="title">{{ $title ?? 'New Payment Received' }}</div>
            <div class="body">
                {!! nl2br(e($body ?? '')) !!}
            </div>
            
            @if(isset($targetId))
            <div class="payment-info">
                <h3>Payment Details</h3>
                <div class="info-row">
                    <span class="info-label">Payment ID:</span>
                    <span class="info-value">#{{ $targetId }}</span>
                </div>
                @if(isset($params['amount']))
                <div class="amount">
                    ${{ number_format($params['amount'], 2) }}
                </div>
                @endif
                @if(isset($emailData['payment']))
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ $emailData['payment']->method ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: #28a745;">{{ $emailData['payment']->status ?? 'Completed' }}</span>
                </div>
                @endif
            </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/admin/financial" class="button">View Payment Details</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Modern Invitation') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>


















































