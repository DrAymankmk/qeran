@php
	$connectionStatus = $status['data']['status'] ?? 'disconnected';
	$phone = $status['data']['phone'] ?? null;
	$qrImage = $qr['data']['qrImage'] ?? null;
	$qrOk = $qr['ok'] ?? false;
@endphp

<p class="mb-2">
	<strong>{{ __('admin.whatsapp-connection-status') }}:</strong>
	@if($connectionStatus === 'connected')
		<span class="badge bg-success">{{ __('admin.whatsapp-status-connected') }}</span>
	@elseif($connectionStatus === 'pending_qr')
		<span class="badge bg-warning text-dark">{{ __('admin.whatsapp-status-pending-qr') }}</span>
	@else
		<span class="badge bg-secondary">{{ $connectionStatus }}</span>
	@endif
</p>

@if($connectionStatus === 'connected' && $phone)
<p class="mb-2"><strong>{{ __('admin.whatsapp-linked-phone') }}:</strong> {{ $phone }}</p>
@endif

@if($connectionStatus !== 'connected')
	@if($qrOk && $qrImage)
	<div class="text-center my-3">
		<img id="wa-qr-image" src="{{ $qrImage }}" alt="WhatsApp QR" width="320" class="border rounded">
		<p class="text-muted small mt-2 mb-0">{{ __('admin.whatsapp-qr-expires-hint') }}</p>
	</div>
	@elseif(isset($status['ok']) && !$status['ok'])
	<p class="text-danger mb-0">{{ $status['error'] ?? __('admin.whatsapp-status-unavailable') }}</p>
	@else
	<p class="text-muted mb-0">{{ __('admin.whatsapp-click-generate-qr') }}</p>
	@endif
@endif
