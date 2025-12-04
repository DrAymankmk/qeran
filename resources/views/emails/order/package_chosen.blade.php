<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title ?? __('admin.package-chosen') }}</title>
	@php
	$isRTL = app()->getLocale() == 'ar';
	$fontFamily = $isRTL ? "'Arial', 'Tahoma', sans-serif" : "'Arial', sans-serif";
	$direction = $isRTL ? 'rtl' : 'ltr';
	$textAlign = $isRTL ? 'right' : 'left';
	$borderSide = $isRTL ? 'right' : 'left';
	$flexDirection = $isRTL ? 'row-reverse' : 'row';
	@endphp
	<style>
	body {
		font-family: Arial, sans-serif;
		direction: rtl;
		text-align: right;
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
		background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
		color: white;
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
		color: #6f42c1;

		text-align: right;

	}

	.body {
		font-size: 16px;
		color: #666;
		margin-bottom: 20px;

		text-align: right;
	}

	.order-box {
		background-color: #e7d5ff;

		border-right: 4px solid #6f42c1;
		border-left: 4px solid #6f42c1;
		padding: 15px;
		margin: 20px 0;
		border-radius: 4px;

		text-align: right;
	}

	.order-box strong {
		display: inline-block;

		min-width: 140px;
		margin-right: 10px;
		color: #495057;
	}

	.user-info {
		background-color: #f8f9fa;
		border: 2px solid #6f42c1;
		border-radius: 8px;
		padding: 20px;
		margin: 20px 0;
	}

	.user-info h3 {
		margin-top: 0;
		color: #6f42c1;

		text-align: right;
	}

	.info-row {
		display: flex;

		flex-direction: row-reverse;
		justify-content: space-between;
		padding: 8px 0;
		border-bottom: 1px solid #dee2e6;
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
		background-color: #6f42c1;
		color: white;
		text-decoration: none;
		border-radius: 5px;
		margin-top: 20px;
	}
	</style>
</head>

<body>
	<div class="container">
		<div class="header">
			<h1 style="margin: 0;">{{ __('admin.package-chosen') }}</h1>
			<p style="margin: 10px 0 0 0; font-size: 16px;">
				{{ __('admin.package-chosen-description') }}</p>
		</div>
		<div class="content">
			<div class="title">{{ $title ?? __('admin.package-chosen') }}</div>
			<div class="body">
				{!! nl2br(e($body ?? '')) !!}
			</div>
			@if(isset($user))
			<div class="order-box">
				<strong>{{ __('admin.user-id') }}:</strong> #{{ $user->id }}
			</div>
			<div class="order-box">
				<strong>{{ __('admin.user-name') }}:</strong> {{ $user->name }}
			</div>
			@endif
			@if(isset($invitation))
			<div class="order-box">
				<strong>{{ __('admin.invitation-id') }}:</strong> #{{ $invitation->id }}
			</div>
			<div class="order-box">
				<strong>{{ __('admin.invitation-name') }}:</strong>
				{{ $invitation->event_name ?? $invitation->name }}
			</div>
			@endif
			@if(isset($params['invitation_type']))
			<div class="order-box">
				<strong>{{ __('admin.invitation-type') }}:</strong>
				{{ $params['invitation_type'] }}
			</div>
			@endif
			@if(isset($params['status']))
			<div class="order-box">
				<strong>{{ __('admin.invitation-status') }}:</strong> {{ $params['status'] }}
			</div>
			@endif
			@if(isset($params['step']))
			<div class="order-box">
				<strong>{{ __('admin.step') }}:</strong> {{ $params['step'] }}
			</div>
			@endif
			@if(isset($package))
			<div class="order-box">
				<strong>{{ __('admin.package-price') }}:</strong>
				{{ number_format($package->price ?? 0, 2) }} {{ __('admin.sar') }}
			</div>
			<div class="order-box">
				<strong>{{ __('admin.package-count') }}:</strong> {{ $package->count ?? 0 }}
			</div>
			@endif
			@if(isset($invitation))
			<div class="order-box">
				<strong>{{ __('admin.created_at') }}:</strong>
				{{ $invitation->created_at->format('Y-m-d H:i:s') }}
			</div>
			@endif
		</div>
	</div>
	<div class="footer">
		<p>&copy; {{ date('Y') }} {{ config('app.name', 'Modern Invitation') }}.
			{{ __('All rights reserved.') }}</p>
	</div>
</body>

</html>