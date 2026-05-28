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
					<button type="button" id="wa-generate-btn" class="btn btn-primary">
						<i class="mdi mdi-qrcode-scan me-1"></i>
						<span id="wa-generate-label">{{ __('admin.whatsapp-generate-qr') }}</span>
					</button>
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
	const prepareUrl = @json(route('admin.whatsapp-system.prepare'));
	const qrUrl = @json(route('admin.whatsapp-system.qr'));
	const csrfToken = @json(csrf_token());
	const autoGenerate = @json((bool) ($autoGenerateQr ?? false));
	const labels = {
		generate: @json(__('admin.whatsapp-generate-qr')),
		generating: @json(__('admin.whatsapp-qr-generating')),
		loading: @json(__('admin.whatsapp-qr-loading')),
		connected: @json(__('admin.whatsapp-status-connected')),
		pendingQr: @json(__('admin.whatsapp-status-pending-qr')),
		disconnected: @json(__('admin.whatsapp-status-disconnected')),
		statusLoading: @json(__('admin.whatsapp-status-loading')),
		gatewayUnreachable: @json(__('admin.whatsapp-gateway-unreachable')),
		connectionStatus: @json(__('admin.whatsapp-connection-status')),
		linkedPhone: @json(__('admin.whatsapp-linked-phone')),
		waitingScan: @json(__('admin.whatsapp-waiting-scan')),
		qrExpires: @json(__('admin.whatsapp-qr-expires-hint')),
		clickGenerate: @json(__('admin.whatsapp-click-generate-qr')),
	};
	const pollMs = 3000;
	const qrPollMs = 2500;
	const maxQrAttempts = 30;
	let statusTimer = null;
	let qrRunning = false;

	function stopStatusPoll() {
		if (statusTimer) {
			clearInterval(statusTimer);
			statusTimer = null;
		}
	}

	function setGenerateBusy(busy) {
		const btn = document.getElementById('wa-generate-btn');
		const label = document.getElementById('wa-generate-label');
		if (!btn || !label) {
			return;
		}
		btn.disabled = busy;
		label.textContent = busy ? labels.generating : labels.generate;
	}

	function renderQrImage(qrImage) {
		return '<div class="text-center my-3">'
			+ '<img id="wa-qr-image" src="' + qrImage + '" alt="WhatsApp QR" width="320" class="border rounded">'
			+ '<p class="text-muted small mt-2 mb-0">' + labels.qrExpires + '</p>'
			+ '</div>';
	}

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	function renderStatusError(errorMsg) {
		const box = document.getElementById('wa-status-box');
		if (!box) {
			return;
		}
		box.innerHTML = '<p class="mb-2"><strong>' + labels.connectionStatus + ':</strong> '
			+ '<span class="badge bg-danger">' + labels.gatewayUnreachable + '</span></p>'
			+ '<p class="text-danger mb-0 small">' + escapeHtml(errorMsg || labels.gatewayUnreachable) + '</p>';
	}

	function renderStatusBox(connectionStatus, phone, qrImage, message) {
		const box = document.getElementById('wa-status-box');
		if (!box) {
			return;
		}

		let html = '<p class="mb-2"><strong>' + labels.connectionStatus + ':</strong> ';
		if (connectionStatus === 'connected') {
			html += '<span class="badge bg-success">' + labels.connected + '</span></p>';
			html += '<p class="mb-0"><strong>' + labels.linkedPhone + ':</strong> ' + escapeHtml(phone || '—') + '</p>';
			stopStatusPoll();
			setGenerateBusy(false);
		} else if (connectionStatus === 'pending_qr') {
			html += '<span class="badge bg-warning text-dark">' + labels.pendingQr + '</span></p>';
			html += '<p class="mb-2 text-muted">' + labels.waitingScan + '</p>';
			if (qrImage) {
				html += renderQrImage(qrImage);
			}
		} else {
			const badgeLabel = connectionStatus === 'disconnected' ? labels.disconnected : connectionStatus;
			html += '<span class="badge bg-secondary">' + escapeHtml(badgeLabel) + '</span></p>';
			html += '<p class="mb-2 text-muted">' + escapeHtml(message || labels.clickGenerate) + '</p>';
			if (qrImage) {
				html += renderQrImage(qrImage);
			}
		}

		box.innerHTML = html;
	}

	function applyStatusPayload(payload) {
		if (!payload || !payload.ok) {
			renderStatusError(payload?.error || labels.gatewayUnreachable);
			return;
		}
		const d = payload.data || {};
		const status = d.status || 'disconnected';
		const existingQr = document.getElementById('wa-qr-image');
		renderStatusBox(status, d.phone || null, existingQr ? existingQr.src : null, null);
		if (status === 'connected') {
			stopStatusPoll();
		}
	}

	function pollStatus() {
		fetch(statusUrl, {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(r => r.json())
			.then(applyStatusPayload)
			.catch(() => renderStatusError(labels.gatewayUnreachable));
	}

	function loadStatus() {
		return fetch(statusUrl, {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(r => r.json())
			.then(applyStatusPayload)
			.catch(() => renderStatusError(labels.gatewayUnreachable));
	}

	function fetchQrOnce(waitMs) {
		return fetch(qrUrl + '?waitMs=' + encodeURIComponent(waitMs), {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
		}).then(r => r.json());
	}

	async function generateQr() {
		if (qrRunning) {
			return;
		}
		qrRunning = true;
		setGenerateBusy(true);
		renderStatusBox('starting', null, null, labels.loading);

		try {
			await fetch(prepareUrl, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: '{}',
			});
		} catch (e) {
			/* prepare may time out while gateway still starts — keep polling */
		}

		for (let attempt = 0; attempt < maxQrAttempts; attempt++) {
			const waitMs = attempt === 0 ? 0 : 8000;
			let payload;
			try {
				payload = await fetchQrOnce(waitMs);
			} catch (e) {
				await new Promise(r => setTimeout(r, qrPollMs));
				continue;
			}

			if (!payload || !payload.ok) {
				if (payload?.error && attempt >= 2) {
					renderStatusError(payload.error);
					qrRunning = false;
					setGenerateBusy(false);
					return;
				}
				await new Promise(r => setTimeout(r, qrPollMs));
				continue;
			}

			const d = payload.data || {};
			if (d.status === 'connected') {
				renderStatusBox('connected', d.phone || null, null, null);
				qrRunning = false;
				setGenerateBusy(false);
				return;
			}

			if (d.qrImage) {
				renderStatusBox('pending_qr', null, d.qrImage, null);
				qrRunning = false;
				setGenerateBusy(false);
				return;
			}

			await new Promise(r => setTimeout(r, qrPollMs));
		}

		renderStatusBox('disconnected', null, null, labels.clickGenerate);
		qrRunning = false;
		setGenerateBusy(false);
	}

	const generateBtn = document.getElementById('wa-generate-btn');
	if (generateBtn) {
		generateBtn.addEventListener('click', generateQr);
	}

	loadStatus().then(function () {
		statusTimer = setInterval(pollStatus, pollMs);
		if (autoGenerate) {
			generateQr();
		}
	});
})();
</script>
@endif
@endsection
