@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{ __('admin.whatsapp-system-title') }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.dashboard') }}</a></li>
					<li class="breadcrumb-item active">{{ __('admin.whatsapp-system-title') }}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
	{{ session('success') }}
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
	{{ session('error') }}
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-body">
				@if(!$configured)
				<div class="alert alert-warning mb-0">
					{{ __('admin.whatsapp-gateway-not-configured') }}
					<p class="mb-0 mt-2 small">BAILEYS_GATEWAY_URL, BAILEYS_GATEWAY_SECRET</p>
				</div>
				@else
				<p class="text-muted">{{ __('admin.whatsapp-system-description') }}</p>
				<p class="mb-1"><strong>{{ __('admin.whatsapp-session-id') }}:</strong> <code>{{ $sessionId }}</code></p>
				<p class="mb-3"><strong>{{ __('admin.whatsapp-gateway-url') }}:</strong> <code>{{ $gatewayUrl }}</code></p>

				<div id="wa-status-box" class="mb-4 p-3 rounded border">
					@include('admin.whatsapp-system._status', [
						'status' => $status,
						'qr' => $qr,
					])
				</div>

				<div class="d-flex flex-wrap gap-2">
					<form method="post" action="{{ route('admin.whatsapp-system.refresh-qr') }}">
						@csrf
						<button type="submit" class="btn btn-primary">
							<i class="mdi mdi-qrcode-scan me-1"></i>
							{{ __('admin.whatsapp-generate-qr') }}
						</button>
					</form>
					<form method="post" action="{{ route('admin.whatsapp-system.disconnect') }}"
						onsubmit="return confirm(@json(__('admin.whatsapp-disconnect-confirm')));">
						@csrf
						<button type="submit" class="btn btn-outline-danger">
							<i class="mdi mdi-link-off me-1"></i>
							{{ __('admin.whatsapp-disconnect') }}
						</button>
					</form>
				</div>

				<hr>
				<h6>{{ __('admin.whatsapp-scan-instructions-title') }}</h6>
				<ol class="text-muted mb-0">
					<li>{{ __('admin.whatsapp-scan-step-1') }}</li>
					<li>{{ __('admin.whatsapp-scan-step-2') }}</li>
					<li>{{ __('admin.whatsapp-scan-step-3') }}</li>
				</ol>
				@endif
			</div>
		</div>
	</div>
</div>

@if($configured)
<script>
(function () {
	const statusUrl = @json(route('admin.whatsapp-system.status'));
	const pollMs = 3000;
	let timer = null;

	function renderStatus(payload) {
		const box = document.getElementById('wa-status-box');
		if (!box || !payload || !payload.ok || !payload.data) {
			return;
		}
		const d = payload.data;
		const status = d.status || 'disconnected';
		const phone = d.phone || '—';
		let html = '<p class="mb-2"><strong>{{ __('admin.whatsapp-connection-status') }}:</strong> ';
		if (status === 'connected') {
			html += '<span class="badge bg-success">{{ __('admin.whatsapp-status-connected') }}</span>';
			html += '</p><p class="mb-0"><strong>{{ __('admin.whatsapp-linked-phone') }}:</strong> ' + phone + '</p>';
			stopPoll();
		} else if (status === 'pending_qr') {
			html += '<span class="badge bg-warning text-dark">{{ __('admin.whatsapp-status-pending-qr') }}</span>';
			html += '</p><p class="mb-0 text-muted">{{ __('admin.whatsapp-waiting-scan') }}</p>';
			const existingQr = document.getElementById('wa-qr-image');
			if (existingQr) {
				html += '<div class="text-center my-3">' + existingQr.outerHTML + '</div>';
			}
		} else {
			html += '<span class="badge bg-secondary">' + status + '</span></p>';
		}
		box.innerHTML = html;
	}

	function poll() {
		fetch(statusUrl, {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(r => r.json())
			.then(renderStatus)
			.catch(() => {});
	}

	function stopPoll() {
		if (timer) {
			clearInterval(timer);
			timer = null;
		}
	}

	const initial = @json($status['data']['status'] ?? 'disconnected');
	if (initial !== 'connected') {
		timer = setInterval(poll, pollMs);
	}
})();
</script>
@endif
@endsection
