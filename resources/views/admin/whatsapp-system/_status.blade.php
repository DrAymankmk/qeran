@php
	$loading = $status['loading'] ?? false;
	$connectionStatus = $status['data']['status'] ?? null;
	$phone = $status['data']['phone'] ?? null;
	$qrImage = $qr['data']['qrImage'] ?? null;
	$qrOk = $qr['ok'] ?? false;
	$gatewayError = (!$loading && isset($status['ok']) && ! $status['ok']) ? ($status['error'] ?? null) : null;
@endphp

<p class="mb-2">
	<strong>{{ __('admin.whatsapp-connection-status') }}:</strong>
	@if($loading)
		<span class="badge bg-info text-dark">{{ __('admin.whatsapp-status-loading') }}</span>
	@elseif($gatewayError)
		<span class="badge bg-danger">{{ __('admin.whatsapp-gateway-unreachable') }}</span>
	@elseif($connectionStatus === 'connected')
		<span class="badge bg-success">{{ __('admin.whatsapp-status-connected') }}</span>
	@elseif($connectionStatus === 'pending_qr')
		<span class="badge bg-warning text-dark">{{ __('admin.whatsapp-status-pending-qr') }}</span>
	@elseif($connectionStatus)
		<span class="badge bg-secondary">{{ $connectionStatus }}</span>
	@else
		<span class="badge bg-secondary">{{ __('admin.whatsapp-status-disconnected') }}</span>
	@endif
</p>

@if($gatewayError)
<p class="text-danger mb-2 small">{{ $gatewayError }}</p>
@endif

@if($connectionStatus === 'connected' && $phone)
<p class="mb-2"><strong>{{ __('admin.whatsapp-linked-phone') }}:</strong> {{ $phone }}</p>
@endif

@if(!$loading && $connectionStatus !== 'connected')
	@if($qrOk && $qrImage)
	<div class="text-center my-3">
		<img id="wa-qr-image" src="{{ $qrImage }}" alt="WhatsApp QR" width="320" class="border rounded">
		<p class="text-muted small mt-2 mb-0">{{ __('admin.whatsapp-qr-expires-hint') }}</p>
	</div>
	@elseif(!$gatewayError)
	<p class="text-muted mb-0">{{ __('admin.whatsapp-click-generate-qr') }}</p>
	@endif
@elseif($loading)
<p class="text-muted mb-0">{{ __('admin.whatsapp-status-loading-hint') }}</p>
@endif
