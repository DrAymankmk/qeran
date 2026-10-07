@php
	$renderMode = $renderMode ?? 'full';
	$isRtl = app()->getLocale() === 'ar';
@endphp

@if(in_array($renderMode, ['header', 'full'], true))
<style>
	body {
		font-family: dejavusans, sans-serif;
		font-size: 12px;
		color: #222;
		direction: {{ $isRtl ? 'rtl' : 'ltr' }};
		text-align: {{ $isRtl ? 'right' : 'left' }};
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
	.qr-table {
		width: 100%;
		border-collapse: collapse;
	}
	.qr-table td {
		width: 33%;
		vertical-align: top;
		text-align: center;
		padding: 8px 4px;
		border: 1px solid #eee;
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

<h1>{{ __('admin.invitation-contacts-qr') }}</h1>
<div class="meta">
	<div>{{ $invitationName }} ({{ __('admin.id') }}: {{ $invitation->id }})</div>
	<div>{{ __('admin.created_at') }}:
		{{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i') }}
	</div>
</div>
@endif

@if($renderMode === 'empty')
	<div class="empty">{{ __('admin.invitation-contacts-empty') }}</div>
@endif

@if($renderMode === 'contact' && !empty($contact))
	@include('pages.invitation.partials.contact-pdf-block', ['contact' => $contact])
@endif

@if($renderMode === 'full')
	@if($contacts->isEmpty())
		<div class="empty">{{ __('admin.invitation-contacts-empty') }}</div>
	@else
		@foreach($contacts as $contact)
			@include('pages.invitation.partials.contact-pdf-block', ['contact' => $contact])
		@endforeach
	@endif
@endif
