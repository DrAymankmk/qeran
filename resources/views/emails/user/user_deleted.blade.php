<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title ?? __('admin.user-deleted') }}</title>
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
		background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
		color: #dc3545;

		text-align: right;
	}

	.body {
		font-size: 16px;
		color: #666;
		margin-bottom: 20px;

		text-align: right;
	}

	.user-info {
		background-color: #f8f9fa;
		border: 2px solid #dc3545;
		border-radius: 8px;
		padding: 20px;
		margin: 20px 0;
	}

	.user-info h3 {
		margin-top: 0;
		color: #dc3545;

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
		background-color: #dc3545;
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
			<h1 style="margin: 0;">{{ __('admin.user-deleted') }}</h1>
			<p style="margin: 10px 0 0 0; font-size: 16px;">
				{{ __('admin.user-deleted-description') }}</p>
		</div>
		<div class="content">
			<div class="title">{{ $title ?? __('admin.user-deleted') }}</div>
			<div class="body">
				{!! nl2br(e($body ?? '')) !!}
			</div>

			@if(isset($params['user_id']))
			<div class="user-info">
				<h3>{{ __('admin.user-details') }}</h3>
				<div class="info-row">
					<span class="info-label">{{ __('admin.user-id') }}:</span>
					<span class="info-value">#{{ $params['user_id'] }}</span>
				</div>
				@if(isset($params['user_name']))
				<div class="info-row">
					<span class="info-label">{{ __('admin.user-name') }}:</span>
					<span class="info-value">{{ $params['user_name'] }}</span>
				</div>
				@endif
				@if(isset($targetId))
				<div class="info-row">
					<span class="info-label">{{ __('admin.deleted-date') }}:</span>
					<span class="info-value">{{ now()->format('Y-m-d H:i:s') }}</span>
				</div>
				@endif
			</div>
			@endif
		</div>
		<div class="footer">
			<p>&copy; {{ date('Y') }} {{ config('app.name', 'Modern Invitation') }}.
				{{ __('admin.all-rights-reserved') }}</p>
		</div>
	</div>
</body>

</html>