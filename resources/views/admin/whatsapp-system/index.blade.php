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
				<h6 class="mb-3">{{ __('admin.whatsapp-test-otp-title') }}</h6>
				<p class="text-muted small mb-3">{{ __('admin.whatsapp-test-otp-description') }}</p>
				<div id="wa-test-otp-alert" class="alert d-none mb-3" role="alert"></div>
				<div class="row g-2 align-items-end mb-0">
					<div class="col-sm-3">
						<label for="wa-test-country" class="form-label small mb-1">{{ __('admin.whatsapp-test-otp-country') }}</label>
						<input type="text" id="wa-test-country" class="form-control" value="966"
							placeholder="966" maxlength="6" inputmode="numeric">
					</div>
					<div class="col-sm-5">
						<label for="wa-test-phone" class="form-label small mb-1">{{ __('admin.whatsapp-test-otp-phone') }}</label>
						<input type="text" id="wa-test-phone" class="form-control"
							placeholder="{{ __('admin.whatsapp-test-otp-phone-placeholder') }}"
							inputmode="tel" autocomplete="tel">
					</div>
					<div class="col-sm-4">
						<button type="button" id="wa-test-otp-btn" class="btn btn-outline-primary w-100">
							<i class="mdi mdi-send me-1"></i>
							<span id="wa-test-otp-label">{{ __('admin.whatsapp-test-otp-send') }}</span>
						</button>
					</div>
				</div>

				<hr>
				<h6>{{ __('admin.whatsapp-scan-instructions-title') }}</h6>
				<ol class="text-muted mb-2">
					<li>{{ __('admin.whatsapp-scan-step-1') }}</li>
					<li>{{ __('admin.whatsapp-scan-step-2') }}</li>
					<li>{{ __('admin.whatsapp-scan-step-3') }}</li>
				</ol>
				<div class="alert alert-info small mb-0">
					<strong>{{ __('admin.whatsapp-qr-troubleshoot-title') }}</strong>
					<ul class="mb-0 ps-3">
						<li>{{ __('admin.whatsapp-qr-troubleshoot-1') }}</li>
						<li>{{ __('admin.whatsapp-qr-troubleshoot-2') }}</li>
						<li>{{ __('admin.whatsapp-qr-troubleshoot-3') }}</li>
						<li>{{ __('admin.whatsapp-qr-troubleshoot-4') }}</li>
					</ul>
				</div>
				@endif
			</div>
		</div>
	</div>

	@if($configured)
	<div class="col-lg-12 mt-3">
		@include('admin.whatsapp-system._activity_log', ['activityLogs' => $activityLogs ?? collect()])
	</div>
	@endif
</div>

@if($configured)
<div class="modal fade" id="wa-log-details-modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('admin.whatsapp-activity-log-details-title') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<pre id="wa-log-details-body" class="small mb-0 bg-light p-3 rounded" style="white-space: pre-wrap;"></pre>
			</div>
		</div>
	</div>
</div>

@php
	$waLogsInitialPage = ($activityLogs ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator
		? $activityLogs->currentPage()
		: 1;
	$waLogsDestroyUrlTemplate = route('admin.whatsapp-system.logs.destroy', ['log' => '__LOG__']);
	$waLogShowingTemplate = __('admin.whatsapp-activity-log-showing', ['from' => ':from', 'to' => ':to', 'total' => ':total']);
@endphp

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
		reconnecting: @json(__('admin.whatsapp-status-reconnecting')),
		stillLinkedHint: @json(__('admin.whatsapp-still-linked-hint')),
		socketLostAt: @json(__('admin.whatsapp-socket-lost-at')),
		disconnectedAt: @json(__('admin.whatsapp-disconnected-at-label')),
		testOtpSending: @json(__('admin.whatsapp-test-otp-sending')),
		testOtpSend: @json(__('admin.whatsapp-test-otp-send')),
		gatewayRestartReconnecting: @json(__('admin.whatsapp-gateway-restart-reconnecting')),
		gatewayRestartQrLost: @json(__('admin.whatsapp-gateway-restart-qr-lost')),
		statusLoading: @json(__('admin.whatsapp-status-loading')),
		gatewayUnreachable: @json(__('admin.whatsapp-gateway-unreachable')),
		connectionStatus: @json(__('admin.whatsapp-connection-status')),
		linkedPhone: @json(__('admin.whatsapp-linked-phone')),
		waitingScan: @json(__('admin.whatsapp-waiting-scan')),
		qrExpires: @json(__('admin.whatsapp-qr-expires-hint')),
		clickGenerate: @json(__('admin.whatsapp-click-generate-qr')),
		uptime: @json(__('admin.whatsapp-uptime')),
		lastSession: @json(__('admin.whatsapp-last-session-duration')),
		autoReconnect: @json(__('admin.whatsapp-auto-reconnect-hint')),
		adminLocked: @json(__('admin.whatsapp-admin-disconnect-locked')),
	};
	const pollMs = 3000;
	const logsUrl = @json(route('admin.whatsapp-system.logs'));
	const logsDestroyAllUrl = @json(route('admin.whatsapp-system.logs.destroy-all'));
	const logsDestroyUrlTemplate = @json($waLogsDestroyUrlTemplate);
	const logsPerPage = 15;
	let logsCurrentPage = @json($waLogsInitialPage);
	const logLabels = {
		empty: @json(__('admin.whatsapp-activity-log-empty')),
		colTime: @json(__('admin.whatsapp-activity-log-col-time')),
		colEvent: @json(__('admin.whatsapp-activity-log-col-event')),
		colMessage: @json(__('admin.whatsapp-activity-log-col-message')),
		colActor: @json(__('admin.whatsapp-activity-log-col-actor')),
		colDetails: @json(__('admin.whatsapp-activity-log-col-details')),
		colActions: @json(__('admin.whatsapp-activity-log-col-actions')),
		view: @json(__('admin.whatsapp-activity-log-view')),
		delete: @json(__('admin.whatsapp-activity-log-delete')),
		deleteConfirm: @json(__('admin.whatsapp-activity-log-delete-confirm')),
		clearAllConfirm: @json(__('admin.whatsapp-activity-log-clear-all-confirm')),
		showing: @json($waLogShowingTemplate),
		deleted: @json(__('admin.whatsapp-activity-log-deleted')),
		cleared: @json(__('admin.whatsapp-activity-log-cleared')),
		deleteFailed: @json(__('admin.whatsapp-activity-log-delete-failed')),
		actorSystem: @json(__('admin.whatsapp-activity-log-actor-system')),
	};
	const testOtpUrl = @json(route('admin.whatsapp-system.test-otp'));
	const logsRefreshMs = 30000;
	const qrPollMs = 2500;
	const qrRefreshMs = 0;
	const maxQrAttempts = 30;
	let statusTimer = null;
	let qrRefreshTimer = null;
	let qrRunning = false;

	function stopStatusPoll() {
		if (statusTimer) {
			clearInterval(statusTimer);
			statusTimer = null;
		}
	}

	function stopQrRefresh() {
		if (qrRefreshTimer) {
			clearInterval(qrRefreshTimer);
			qrRefreshTimer = null;
		}
	}

	function startQrRefresh() {
		stopQrRefresh();
		qrRefreshTimer = setInterval(async function () {
			try {
				const payload = await fetchQrOnce(8000);
				if (payload?.ok && payload.data?.status === 'connected') {
					renderStatusBox('connected', payload.data.phone || null, null, null, null);
					stopQrRefresh();
					stopStatusPoll();
					return;
				}
				if (payload?.ok && payload.data?.qrImage) {
					renderStatusBox('pending_qr', null, payload.data.qrImage, null, null);
				}
			} catch (e) {
				/* ignore — status poll will surface errors */
			}
		}, qrRefreshMs);
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

	function formatDuration(seconds) {
		if (seconds == null || seconds < 0) {
			return '—';
		}
		const h = Math.floor(seconds / 3600);
		const m = Math.floor((seconds % 3600) / 60);
		const s = seconds % 60;
		if (h > 0) {
			return h + 'h ' + m + 'm';
		}
		if (m > 0) {
			return m + 'm ' + s + 's';
		}
		return s + 's';
	}

	function renderSessionMeta(meta) {
		if (!meta) {
			return '';
		}
		let html = '';
		if (meta.uptime_seconds != null && meta.uptime_seconds >= 0) {
			html += '<p class="mb-1 small text-muted"><strong>' + labels.uptime + ':</strong> '
				+ escapeHtml(formatDuration(meta.uptime_seconds)) + '</p>';
		}
		if (meta.last_session_seconds != null && meta.last_session_seconds > 0) {
			html += '<p class="mb-1 small text-muted"><strong>' + labels.lastSession + ':</strong> '
				+ escapeHtml(formatDuration(meta.last_session_seconds)) + '</p>';
		}
		if (meta.socket_lost_at_display) {
			html += '<p class="mb-1 small text-warning"><strong>' + labels.socketLostAt + ':</strong> '
				+ escapeHtml(meta.socket_lost_at_display) + '</p>';
		}
		if (meta.disconnected_at_display) {
			html += '<p class="mb-1 small text-muted"><strong>' + labels.disconnectedAt + ':</strong> '
				+ escapeHtml(meta.disconnected_at_display) + '</p>';
		}
		if (meta.admin_disconnect_locked) {
			html += '<p class="mb-0 small text-warning">' + labels.adminLocked + '</p>';
		} else {
			html += '<p class="mb-0 small text-muted">' + labels.autoReconnect + '</p>';
		}
		return html;
	}

	function renderStatusBox(connectionStatus, phone, qrImage, recoveryHint, sessionMeta) {
		const box = document.getElementById('wa-status-box');
		if (!box) {
			return;
		}

		let html = '<p class="mb-2"><strong>' + labels.connectionStatus + ':</strong> ';
		if (connectionStatus === 'connected') {
			html += '<span class="badge bg-success">' + labels.connected + '</span></p>';
			html += '<p class="mb-2"><strong>' + labels.linkedPhone + ':</strong> ' + escapeHtml(phone || '—') + '</p>';
			html += renderSessionMeta(sessionMeta);
			stopStatusPoll();
			setGenerateBusy(false);
		} else if (connectionStatus === 'pending_qr') {
			html += '<span class="badge bg-warning text-dark">' + labels.pendingQr + '</span></p>';
			html += '<p class="mb-2 text-muted">' + labels.waitingScan + '</p>';
			if (qrImage) {
				html += renderQrImage(qrImage);
			}
		} else if (connectionStatus === 'reconnecting') {
			html += '<span class="badge bg-warning text-dark">' + labels.reconnecting + '</span></p>';
			html += '<p class="mb-2 text-muted">' + escapeHtml(recoveryHint || labels.autoReconnect) + '</p>';
			html += renderSessionMeta(sessionMeta);
			if (sessionMeta && sessionMeta.socket_lost_at_display) {
				html += '<p class="mb-0 small text-warning">' + labels.stillLinkedHint + '</p>';
			}
		} else {
			const badgeLabel = connectionStatus === 'disconnected' ? labels.disconnected : connectionStatus;
			html += '<span class="badge bg-secondary">' + escapeHtml(badgeLabel) + '</span></p>';
			html += '<p class="mb-2 text-muted">' + escapeHtml(recoveryHint || labels.clickGenerate) + '</p>';
			html += renderSessionMeta(sessionMeta);
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
		renderStatusBox(
			status,
			d.phone || null,
			existingQr ? existingQr.src : null,
			d.recovery_hint || null,
			payload.session_meta || null
		);
		if (status === 'connected') {
			/* keep polling to refresh uptime */
			refreshActivityLogs();
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
		renderStatusBox('starting', null, null, labels.loading, null);

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
			const waitMs = attempt === 0 ? 3000 : 20000;
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
				renderStatusBox('connected', d.phone || null, null, null, null);
				qrRunning = false;
				setGenerateBusy(false);
				refreshActivityLogs();
				return;
			}

			if (d.qrImage) {
				renderStatusBox('pending_qr', null, d.qrImage, null, null);
				qrRunning = false;
				setGenerateBusy(false);
				refreshActivityLogs();
				return;
			}

			await new Promise(r => setTimeout(r, qrPollMs));
		}

		renderStatusBox('disconnected', null, null, labels.clickGenerate, null);
		qrRunning = false;
		setGenerateBusy(false);
	}

	const generateBtn = document.getElementById('wa-generate-btn');
	if (generateBtn) {
		generateBtn.addEventListener('click', generateQr);
	}

	loadStatus().then(function () {
		statusTimer = setInterval(pollStatus, pollMs);
		setInterval(refreshActivityLogs, logsRefreshMs);
		bindLogDetailButtons();
		if (autoGenerate) {
			generateQr();
		}
	});

	function bindLogDetailButtons() {
		document.querySelectorAll('.wa-log-details-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				const encoded = btn.getAttribute('data-context') || '';
				let parsed = encoded;
				try {
					parsed = JSON.stringify(JSON.parse(decodeURIComponent(encoded)), null, 2);
				} catch (e) {
					try {
						parsed = JSON.stringify(JSON.parse(encoded), null, 2);
					} catch (e2) {
						/* keep raw */
					}
				}
				const body = document.getElementById('wa-log-details-body');
				if (body) {
					body.textContent = parsed;
				}
				const modalEl = document.getElementById('wa-log-details-modal');
				if (modalEl && window.bootstrap) {
					window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
				}
			});
		});
	}

	function formatLogShowing(from, to, total) {
		return logLabels.showing
			.replace(':from', String(from ?? 0))
			.replace(':to', String(to ?? 0))
			.replace(':total', String(total ?? 0));
	}

	function logDestroyUrl(id) {
		return logsDestroyUrlTemplate.replace('__LOG__', String(id));
	}

	function buildPaginationHtml(meta) {
		if (!meta || !meta.total) {
			return '';
		}

		const summary = '<p class="small text-muted mb-0" id="wa-activity-log-summary">'
			+ escapeHtml(formatLogShowing(meta.from, meta.to, meta.total)) + '</p>';

		if (!meta.last_page || meta.last_page <= 1) {
			return '<nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3" id="wa-activity-log-pagination" aria-label="Activity log pagination">'
				+ summary + '</nav>';
		}

		let pages = '';
		for (let page = 1; page <= meta.last_page; page++) {
			const active = page === meta.current_page ? ' active' : '';
			pages += '<li class="page-item' + active + '">'
				+ '<button type="button" class="page-link wa-log-page-btn" data-page="' + page + '">' + page + '</button>'
				+ '</li>';
		}

		const prevDisabled = meta.current_page <= 1 ? ' disabled' : '';
		const nextDisabled = meta.current_page >= meta.last_page ? ' disabled' : '';

		return '<nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3" id="wa-activity-log-pagination" aria-label="Activity log pagination">'
			+ summary
			+ '<ul class="pagination pagination-sm mb-0">'
			+ '<li class="page-item' + prevDisabled + '"><button type="button" class="page-link wa-log-page-btn" data-page="' + (meta.current_page - 1) + '"'
			+ (meta.current_page <= 1 ? ' disabled' : '') + ' aria-label="Previous">&laquo;</button></li>'
			+ pages
			+ '<li class="page-item' + nextDisabled + '"><button type="button" class="page-link wa-log-page-btn" data-page="' + (meta.current_page + 1) + '"'
			+ (meta.current_page >= meta.last_page ? ' disabled' : '') + ' aria-label="Next">&raquo;</button></li>'
			+ '</ul></nav>';
	}

	function renderActivityLogs(entries, meta) {
		const wrap = document.getElementById('wa-activity-log-wrap');
		if (!wrap || !Array.isArray(entries)) {
			return;
		}

		if (!meta || !meta.total) {
			wrap.innerHTML = '<p class="text-muted mb-0" id="wa-activity-log-empty">' + escapeHtml(logLabels.empty) + '</p>';
			return;
		}

		let rows = '';
		entries.forEach(function (entry) {
			const contextRaw = encodeURIComponent(JSON.stringify(entry.context || {}));
			const contextBtn = Object.keys(entry.context || {}).length
				? '<button type="button" class="btn btn-link btn-sm p-0 wa-log-details-btn" data-context="' + contextRaw + '">' + escapeHtml(logLabels.view) + '</button>'
				: '<span class="text-muted">—</span>';
			const actor = entry.admin_name
				? escapeHtml(entry.admin_name)
				: escapeHtml(logLabels.actorSystem);
			const created = entry.created_at_display
				? escapeHtml(entry.created_at_display)
				: (entry.created_at ? escapeHtml(entry.created_at.replace('T', ' ').slice(0, 19)) : '—');
			const human = entry.created_at_human ? escapeHtml(entry.created_at_human) : '';
			const deleteBtn = '<button type="button" class="btn btn-link btn-sm p-0 text-danger wa-log-delete-btn" data-id="' + escapeHtml(String(entry.id)) + '"'
				+ ' title="' + escapeHtml(logLabels.delete) + '" aria-label="' + escapeHtml(logLabels.delete) + '">'
				+ '<i class="mdi mdi-delete-outline"></i></button>';

			rows += '<tr>'
				+ '<td class="small text-nowrap"><div>' + created + '</div>'
				+ (human ? '<div class="text-muted">' + human + '</div>' : '') + '</td>'
				+ '<td><span class="badge bg-' + escapeHtml(entry.level_badge || 'secondary') + '">'
				+ escapeHtml(entry.event_label || entry.event || '') + '</span></td>'
				+ '<td class="small">' + escapeHtml(entry.message || '') + '</td>'
				+ '<td class="small text-muted">' + actor + '</td>'
				+ '<td>' + contextBtn + '</td>'
				+ '<td>' + deleteBtn + '</td>'
				+ '</tr>';
		});

		wrap.innerHTML = '<div class="table-responsive">'
			+ '<table class="table table-sm table-hover align-middle mb-0" id="wa-activity-log-table">'
			+ '<thead class="table-light"><tr>'
			+ '<th style="width:140px;">' + escapeHtml(logLabels.colTime) + '</th>'
			+ '<th style="width:120px;">' + escapeHtml(logLabels.colEvent) + '</th>'
			+ '<th>' + escapeHtml(logLabels.colMessage) + '</th>'
			+ '<th style="width:120px;">' + escapeHtml(logLabels.colActor) + '</th>'
			+ '<th style="width:90px;">' + escapeHtml(logLabels.colDetails) + '</th>'
			+ '<th style="width:70px;">' + escapeHtml(logLabels.colActions) + '</th>'
			+ '</tr></thead><tbody>' + rows + '</tbody></table></div>'
			+ buildPaginationHtml(meta);

		bindLogDetailButtons();
	}

	function refreshActivityLogs(page) {
		if (typeof page === 'number' && page > 0) {
			logsCurrentPage = page;
		}

		const url = logsUrl + '?per_page=' + logsPerPage + '&page=' + logsCurrentPage;
		return fetch(url, {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (r) { return r.json(); })
			.then(function (payload) {
				if (payload && payload.ok) {
					if (payload.meta) {
						logsCurrentPage = payload.meta.current_page || logsCurrentPage;
					}
					renderActivityLogs(payload.data || [], payload.meta || null);
				}
				return payload;
			})
			.catch(function () { /* ignore */ });
	}

	async function deleteActivityLog(id) {
		if (!id || !window.confirm(logLabels.deleteConfirm)) {
			return;
		}

		try {
			const response = await fetch(logDestroyUrl(id), {
				method: 'DELETE',
				headers: {
					'Accept': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'X-Requested-With': 'XMLHttpRequest',
				},
			});
			const payload = await response.json();
			if (!response.ok || !payload?.ok) {
				window.alert(logLabels.deleteFailed);
				return;
			}

			await refreshActivityLogs(logsCurrentPage);
		} catch (e) {
			window.alert(logLabels.deleteFailed);
		}
	}

	async function clearAllActivityLogs() {
		if (!window.confirm(logLabels.clearAllConfirm)) {
			return;
		}

		try {
			const response = await fetch(logsDestroyAllUrl, {
				method: 'DELETE',
				headers: {
					'Accept': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'X-Requested-With': 'XMLHttpRequest',
				},
			});
			const payload = await response.json();
			if (!response.ok || !payload?.ok) {
				window.alert(logLabels.deleteFailed);
				return;
			}

			logsCurrentPage = 1;
			await refreshActivityLogs(1);
		} catch (e) {
			window.alert(logLabels.deleteFailed);
		}
	}

	const logsWrap = document.getElementById('wa-activity-log-wrap');
	if (logsWrap) {
		logsWrap.addEventListener('click', function (event) {
			const pageBtn = event.target.closest('.wa-log-page-btn');
			if (pageBtn && !pageBtn.disabled) {
				const page = parseInt(pageBtn.getAttribute('data-page') || '1', 10);
				if (page > 0) {
					refreshActivityLogs(page);
				}
				return;
			}

			const deleteBtn = event.target.closest('.wa-log-delete-btn');
			if (deleteBtn) {
				deleteActivityLog(deleteBtn.getAttribute('data-id'));
			}
		});
	}

	const logsRefreshBtn = document.getElementById('wa-logs-refresh');
	if (logsRefreshBtn) {
		logsRefreshBtn.addEventListener('click', function () {
			refreshActivityLogs(logsCurrentPage);
		});
	}

	const logsClearAllBtn = document.getElementById('wa-logs-clear-all');
	if (logsClearAllBtn) {
		logsClearAllBtn.addEventListener('click', clearAllActivityLogs);
	}

	const testOtpBtn = document.getElementById('wa-test-otp-btn');
	const testOtpPhone = document.getElementById('wa-test-phone');
	const testOtpCountry = document.getElementById('wa-test-country');
	const testOtpAlert = document.getElementById('wa-test-otp-alert');
	const testOtpLabel = document.getElementById('wa-test-otp-label');

	function showTestOtpAlert(type, message) {
		if (!testOtpAlert) {
			return;
		}
		testOtpAlert.className = 'alert alert-' + type + ' mb-3';
		testOtpAlert.textContent = message;
		testOtpAlert.classList.remove('d-none');
	}

	async function sendTestOtp() {
		if (!testOtpBtn || !testOtpPhone) {
			return;
		}

		const phone = (testOtpPhone.value || '').trim();
		if (!phone) {
			showTestOtpAlert('warning', @json(__('admin.whatsapp-test-otp-phone-required')));
			testOtpPhone.focus();
			return;
		}

		testOtpBtn.disabled = true;
		if (testOtpLabel) {
			testOtpLabel.textContent = labels.testOtpSending;
		}

		try {
			const response = await fetch(testOtpUrl, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({
					phone: phone,
					country_code: testOtpCountry ? testOtpCountry.value : '966',
				}),
			});

			const payload = await response.json();

			if (payload && payload.ok) {
				showTestOtpAlert('success', payload.message || @json(__('admin.whatsapp-test-otp-sent-generic')));
				refreshActivityLogs();
			} else {
				showTestOtpAlert('danger', payload?.error || @json(__('admin.whatsapp-test-otp-failed-generic')));
			}
		} catch (e) {
			showTestOtpAlert('danger', @json(__('admin.whatsapp-gateway-unreachable')));
		} finally {
			testOtpBtn.disabled = false;
			if (testOtpLabel) {
				testOtpLabel.textContent = labels.testOtpSend;
			}
		}
	}

	if (testOtpBtn) {
		testOtpBtn.addEventListener('click', sendTestOtp);
	}
	if (testOtpPhone) {
		testOtpPhone.addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault();
				sendTestOtp();
			}
		});
	}
})();
</script>
@endif
@endsection
