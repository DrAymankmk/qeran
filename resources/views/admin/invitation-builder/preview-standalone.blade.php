<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $invitation->event_name }} — {{ __('admin.invitation-builder-live-preview') }}</title>
	<link href="{{ asset('admin_assets/css/bootstrap-rtl.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('admin_assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
	@include('admin.invitation-builder.partials.preview-panel-styles')
	<style>
		body {
			background: #f3f4f7;
			font-family: 'Segoe UI', Tahoma, sans-serif;
			min-height: 100vh;
		}
		.ib-preview-page {
			max-width: 1100px;
			margin: 0 auto;
			padding: 24px 16px 40px;
		}
	</style>
</head>
<body>
	<div class="ib-preview-page">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
			<div>
				<h5 class="mb-0">{{ $invitation->event_name }}</h5>
				<small class="text-muted">{{ __('admin.invitation-builder-live-preview') }}</small>
			</div>
			<div class="d-flex flex-wrap gap-2">
				@if(!empty($directInvitationUrl))
				<a href="{{ $directInvitationUrl }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
					<i class="mdi mdi-open-in-new"></i> {{ __('admin.invitation-builder-open-direct') }}
				</a>
				@endif
				<a href="{{ $backUrl }}" class="btn btn-secondary btn-sm">
					<i class="mdi mdi-arrow-right"></i> {{ __('admin.back') }}
				</a>
			</div>
		</div>

		@if(!empty($directInvitationUrl))
		<div class="card mb-3">
			<div class="card-body py-3">
				<label class="form-label small fw-semibold mb-1" for="ibDirectInvitationUrl">
					{{ __('admin.invitation-builder-direct-link') }}
				</label>
				<p class="small text-muted mb-2">{{ __('admin.invitation-builder-direct-link-hint') }}</p>
				<div class="input-group input-group-sm">
					<input type="text" class="form-control" id="ibDirectInvitationUrl" readonly value="{{ $directInvitationUrl }}">
					<button type="button" class="btn btn-outline-secondary" id="ibCopyDirectInvitationUrl" title="{{ __('admin.invitation-builder-copy-link') }}">
						<i class="mdi mdi-content-copy"></i>
					</button>
					<a href="{{ $directInvitationUrl }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
						<i class="mdi mdi-open-in-new"></i>
					</a>
				</div>
				<small class="text-muted d-block mt-2" id="ibCopyDirectInvitationStatus"></small>
			</div>
		</div>
		@endif

		<div class="card mb-0">
			<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
				<div>
					<strong>{{ __('admin.invitation-builder-live-preview') }}</strong>
					<small class="text-muted d-block">{{ __('admin.invitation-builder-live-preview-hint') }}</small>
				</div>
				<div class="btn-group btn-group-sm">
					<button type="button" class="btn btn-outline-secondary active" data-ib-device="desktop">
						<i class="mdi mdi-monitor"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-ib-device="mobile">
						<i class="mdi mdi-cellphone"></i>
					</button>
					<button type="button" class="btn btn-outline-primary" id="ibPreviewRefresh">
						<i class="mdi mdi-refresh"></i>
					</button>
				</div>
			</div>
			<div class="card-body position-relative p-3">
				<div id="ibPreviewLoading" class="ib-preview-loading d-none">
					<div class="spinner-border text-primary"></div>
				</div>
				<div id="ibPreviewDevice" class="ib-preview-device is-desktop">
					<iframe id="ibPreviewFrame"
						allow="autoplay; fullscreen"
						title="{{ __('admin.invitation-builder-live-preview') }}"></iframe>
				</div>
			</div>
		</div>
	</div>

	<script>
	(function () {
		var iframe = document.getElementById('ibPreviewFrame');
		var deviceWrap = document.getElementById('ibPreviewDevice');
		var loading = document.getElementById('ibPreviewLoading');
		var embedHtml = @json($embedHtml);

		function setLoading(show) {
			loading.classList.toggle('d-none', !show);
		}

		function loadPreview() {
			setLoading(true);
			iframe.onload = function () {
				setLoading(false);
				try {
					var win = iframe.contentWindow;
					if (win && win.ibAfterPreviewPatch) {
						win.ibAfterPreviewPatch();
					}
				} catch (e) {}
			};
			iframe.srcdoc = embedHtml;
		}

		document.getElementById('ibPreviewRefresh').addEventListener('click', function () {
			loadPreview();
		});

		document.querySelectorAll('[data-ib-device]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				document.querySelectorAll('[data-ib-device]').forEach(function (b) {
					b.classList.remove('active');
				});
				btn.classList.add('active');
				var mode = btn.getAttribute('data-ib-device');
				deviceWrap.classList.toggle('is-mobile', mode === 'mobile');
				deviceWrap.classList.toggle('is-desktop', mode === 'desktop');
			});
		});

		loadPreview();

		var copyBtn = document.getElementById('ibCopyDirectInvitationUrl');
		var urlInput = document.getElementById('ibDirectInvitationUrl');
		var copyStatus = document.getElementById('ibCopyDirectInvitationStatus');
		if (copyBtn && urlInput) {
			copyBtn.addEventListener('click', function () {
				var url = urlInput.value || '';
				if (!url) return;
				function showCopied() {
					if (copyStatus) {
						copyStatus.textContent = @json(__('admin.invitation-builder-link-copied'));
						window.setTimeout(function () { copyStatus.textContent = ''; }, 2000);
					}
				}
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(url).then(showCopied).catch(function () {
						urlInput.select();
						document.execCommand('copy');
						showCopied();
					});
					return;
				}
				urlInput.select();
				document.execCommand('copy');
				showCopied();
			});
		}
	})();
	</script>
</body>
</html>
