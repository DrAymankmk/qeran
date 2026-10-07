<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="UTF-8">
	<title>{{ __('admin.invitation-contacts-qr') }}</title>
	<style>
		@if(app()->getLocale() == 'ar')
		body { direction: rtl; text-align: right; }
		@else
		body { direction: ltr; text-align: left; }
		@endif
		body {
			font-family: dejavusans, sans-serif;
			font-size: 12px;
			color: #222;
		}
		h1 {
			text-align: center;
			font-size: 18px;
			margin: 0 0 8px;
		}
		.meta {
			text-align: center;
			margin-bottom: 18px;
			color: #555;
		}
		.contact-block {
			border: 1px solid #ddd;
			padding: 12px;
			margin-bottom: 16px;
			page-break-inside: avoid;
		}
		.contact-title {
			font-size: 14px;
			font-weight: bold;
			margin-bottom: 6px;
		}
		.contact-meta {
			margin-bottom: 10px;
			line-height: 1.6;
		}
		.qr-row {
			width: 100%;
		}
		.qr-cell {
			display: inline-block;
			width: 31%;
			vertical-align: top;
			text-align: center;
			margin: 0 1% 12px;
			border: 1px solid #eee;
			padding: 8px 4px;
		}
		.qr-cell img {
			width: 110px;
			height: 110px;
		}
		.qr-name {
			font-size: 11px;
			margin-top: 6px;
		}
		.empty {
			text-align: center;
			padding: 40px 0;
			color: #777;
		}
	</style>
</head>
<body>
	<h1>{{ __('admin.invitation-contacts-qr') }}</h1>
	<div class="meta">
		<div>{{ $invitationName }} ({{ __('admin.id') }}: {{ $invitation->id }})</div>
		<div>{{ __('admin.created_at') }}:
			{{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i') }}
		</div>
	</div>

	@if($contacts->isEmpty())
		<div class="empty">{{ __('admin.invitation-contacts-empty') }}</div>
	@else
		@foreach($contacts as $index => $contact)
			<div class="contact-block">
				<div class="contact-title">{{ $contact['contact_name'] }}</div>
				<div class="contact-meta">
					<div>{{ __('admin.phone') }}: {{ $contact['phone'] ?: __('admin.no-data-available') }}</div>
					<div>{{ __('admin.invitation-count') }}: {{ $contact['invitation_count'] }}</div>
					<div>{{ __('admin.status') }}: {{ $contact['send_status'] }}</div>
					<div>{{ __('admin.acceptance-status') }}: {{ $contact['acceptance_status'] }}</div>
				</div>

				@if(empty($contact['guest_qr_cards']))
					<div>{{ __('admin.ib-preview-qr-missing') }}</div>
				@else
					<div class="qr-row">
						@foreach($contact['guest_qr_cards'] as $guestQr)
							<div class="qr-cell">
								@if(!empty($guestQr['qr_base64']))
									<img src="data:image/png;base64,{{ $guestQr['qr_base64'] }}" alt="QR">
								@else
									<div>{{ __('admin.ib-preview-qr-missing') }}</div>
								@endif
								<div class="qr-name">{{ $guestQr['name'] ?? '' }}</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>

			@if(!$loop->last && ($index + 1) % 2 === 0)
				<pagebreak />
			@endif
		@endforeach
	@endif
</body>
</html>
