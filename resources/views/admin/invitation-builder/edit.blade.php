@extends('layouts.app')
@section('extra-css')
<style>
.ib-builder-layout { align-items: flex-start; }
.ib-preview-sticky {
	position: sticky;
	top: 88px;
	z-index: 10;
}
.ib-preview-device {
	background: linear-gradient(145deg, #1e1e2e, #2a2a40);
	border-radius: 16px;
	padding: 14px;
	margin: 0 auto;
	transition: max-width 0.25s ease;
}
.ib-preview-device.is-mobile {
	max-width: 390px;
	box-shadow: 0 0 0 3px #333, 0 0 0 6px #1a1a1a;
	border-radius: 28px;
}
.ib-preview-device.is-desktop {
	max-width: 100%;
}
.ib-preview-device iframe {
	width: 100%;
	border: 0;
	display: block;
	background: #0f0f18;
	border-radius: 8px;
}
.ib-preview-device.is-desktop iframe { height: min(72vh, 680px); }
.ib-preview-device.is-mobile iframe { height: min(68vh, 640px); border-radius: 12px; }
.ib-preview-loading {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(255,255,255,0.85);
	border-radius: 12px;
	z-index: 2;
}
.ib-preview-loading.d-none { display: none !important; }
</style>
@endsection
@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0">{{ __('admin.invitation-builder') }} — {{ $invitation->event_name }}</h4>
			<div class="page-title-right d-flex flex-wrap gap-2">
				<a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
					{{ __('admin.invitation-builder-preview-tab') }}
				</a>
				<a href="{{ route('invitation.edit', $invitation) }}" class="btn btn-secondary btn-sm">
					{{ __('admin.back') }}
				</a>
			</div>
		</div>
	</div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="post" action="{{ route('admin.invitation-builder.update', $invitation) }}" id="invitationBuilderForm">
	@csrf
	@method('PUT')

	<div class="row ib-builder-layout">
		<div class="col-xl-7 col-lg-12">
			<div class="row">
				<div class="col-lg-6">
					<div class="card mb-3">
						<div class="card-header bg-primary text-white">{{ __('admin.invitation-builder-types') }}</div>
						<div class="card-body">
							<label class="form-label">{{ __('admin.invitation-builder-event-type') }}</label>
							<select name="event_category" class="form-select ib-preview-field">
								@foreach($catalog['event_types'] as $key => $type)
								<option value="{{ $key }}" @selected($config['event_category'] === $key)>
									{{ $type['icon'] ?? '' }} {{ $type['label_ar'] ?? $key }}
								</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="card mb-3">
						<div class="card-header">{{ __('admin.invitation-builder-theme') }}</div>
						<div class="card-body">
							<label class="form-label">{{ __('admin.invitation-builder-template') }}</label>
							<select name="theme_template" class="form-select mb-2 ib-preview-field">
								@foreach($catalog['templates'] as $id => $tpl)
								<option value="{{ $id }}" @selected((int)$config['template'] === (int)$id)>
									#{{ $id }} — {{ $tpl['name'] }}
									@if(!empty($tpl['wooow_style'])) (Wooow) @endif
								</option>
								@endforeach
								@for($i = 1; $i <= 21; $i++)
									@if(!isset($catalog['templates'][$i]))
									<option value="{{ $i }}" @selected((int)$config['template'] === $i)>#{{ $i }}</option>
									@endif
								@endfor
							</select>
							<label class="form-label">{{ __('admin.invitation-builder-theme-mode') }}</label>
							<select name="theme_mode" class="form-select ib-preview-field">
								@foreach($catalog['theme_modes'] as $key => $mode)
								<option value="{{ $key }}" @selected($config['theme_mode'] === $key)>{{ $mode['label_ar'] }}</option>
								@endforeach
							</select>
							<div class="form-check mt-2">
								<input class="form-check-input ib-preview-field" type="checkbox" name="animated_theme" value="1" id="animated_theme" @checked($config['animated_theme'])>
								<label class="form-check-label" for="animated_theme">{{ __('admin.invitation-builder-animated') }}</label>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="card mb-3">
						<div class="card-header">{{ __('admin.invitation-builder-opening') }}</div>
						<div class="card-body">
							<label class="form-label">{{ __('admin.invitation-builder-opening-type') }}</label>
							<select name="opening_type" class="form-select mb-2 ib-preview-field">
								@foreach($catalog['opening_types'] as $key => $op)
								<option value="{{ $key }}" @selected($config['opening_type'] === $key)>{{ $op['label_ar'] }}</option>
								@endforeach
							</select>
							<div class="form-check">
								<input class="form-check-input ib-preview-field" type="checkbox" name="welcome_enabled" value="1" id="welcome_enabled" @checked($config['welcome_enabled'])>
								<label class="form-check-label" for="welcome_enabled">{{ __('admin.invitation-builder-welcome-screen') }}</label>
							</div>
							<div class="form-check">
								<input class="form-check-input ib-preview-field" type="checkbox" name="music_enabled" value="1" id="music_enabled" @checked($config['music_enabled'])>
								<label class="form-check-label" for="music_enabled">{{ __('admin.invitation-builder-music') }}</label>
							</div>
							<div class="form-check">
								<input class="form-check-input ib-preview-field" type="checkbox" name="intro_video_enabled" value="1" id="intro_video_enabled" @checked($config['intro_video_enabled'])>
								<label class="form-check-label" for="intro_video_enabled">{{ __('admin.invitation-builder-intro-video') }}</label>
							</div>
							<hr>
							<label class="form-label">{{ __('admin.invitation-builder-welcome-title') }}</label>
							<input type="text" name="welcome_title" class="form-control mb-2 ib-preview-field" value="{{ old('welcome_title', $config['welcome_title']) }}">
							<label class="form-label">{{ __('admin.invitation-builder-welcome-subtitle') }}</label>
							<input type="text" name="welcome_subtitle" class="form-control ib-preview-field" value="{{ old('welcome_subtitle', $config['welcome_subtitle']) }}">
						</div>
					</div>

					<div class="card mb-3">
						<div class="card-header">{{ __('admin.invitation-builder-visual') }}</div>
						<div class="card-body">
							<div class="row g-2">
								<div class="col-6">
									<label class="form-label">{{ __('admin.invitation-builder-primary-color') }}</label>
									<input type="color" name="primary_color" class="form-control form-control-color w-100 ib-preview-field" value="{{ old('primary_color', $config['primary_color']) }}">
								</div>
								<div class="col-6">
									<label class="form-label">{{ __('admin.invitation-builder-secondary-color') }}</label>
									<input type="color" name="secondary_color" class="form-control form-control-color w-100 ib-preview-field" value="{{ old('secondary_color', $config['secondary_color']) }}">
								</div>
								<div class="col-6">
									<label class="form-label">{{ __('admin.invitation-builder-bg-color') }}</label>
									<input type="color" name="background_color" class="form-control form-control-color w-100 ib-preview-field" value="{{ old('background_color', $config['background_color']) }}">
								</div>
								<div class="col-6">
									<label class="form-label">{{ __('admin.invitation-builder-text-color') }}</label>
									<input type="color" name="text_color" class="form-control form-control-color w-100 ib-preview-field" value="{{ old('text_color', $config['text_color']) }}">
								</div>
							</div>
							<label class="form-label mt-2">{{ __('admin.invitation-builder-font') }}</label>
							<input type="text" name="font_family" class="form-control mb-2 ib-preview-field" value="{{ old('font_family', $config['font_family']) }}" placeholder="Cairo">
							<label class="form-label">{{ __('admin.invitation-builder-logo-url') }}</label>
							<input type="url" name="logo_url" class="form-control mb-2 ib-preview-field" value="{{ old('logo_url', $config['logo_url']) }}" placeholder="https://">
							<label class="form-label">{{ __('admin.invitation-builder-bg-media-url') }}</label>
							<input type="url" name="background_media_url" class="form-control mb-2 ib-preview-field" value="{{ old('background_media_url', $config['background_media_url']) }}">
							<div class="form-check">
								<input class="form-check-input ib-preview-field" type="checkbox" name="video_background" value="1" id="video_background" @checked($config['video_background'])>
								<label class="form-check-label" for="video_background">{{ __('admin.invitation-builder-video-bg') }}</label>
							</div>
							<label class="form-label mt-2">{{ __('admin.invitation-builder-custom-css') }}</label>
							<textarea name="custom_css" class="form-control ib-preview-field" rows="3" placeholder=".front { }">{{ old('custom_css', $config['custom_css']) }}</textarea>
						</div>
					</div>
				</div>
			</div>

			<div class="card mb-3">
				<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
					<div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="publish" value="1" id="publish" @checked($config['published'])>
							<label class="form-check-label" for="publish">{{ __('admin.invitation-builder-publish') }}</label>
						</div>
						<small class="text-muted">{{ __('admin.invitation-builder-publish-hint') }}</small>
					</div>
					<button type="submit" class="btn btn-success">{{ __('admin.save') }}</button>
				</div>
			</div>
		</div>

		<div class="col-xl-5 col-lg-12">
			<div class="card ib-preview-sticky mb-3">
				<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
					<div>
						<strong>{{ __('admin.invitation-builder-live-preview') }}</strong>
						<small class="text-muted d-block">{{ __('admin.invitation-builder-live-preview-hint') }}</small>
					</div>
					<div class="btn-group btn-group-sm" role="group">
						<button type="button" class="btn btn-outline-secondary active" data-ib-device="desktop" title="{{ __('admin.invitation-builder-preview-desktop') }}">
							<i class="mdi mdi-monitor"></i>
						</button>
						<button type="button" class="btn btn-outline-secondary" data-ib-device="mobile" title="{{ __('admin.invitation-builder-preview-mobile') }}">
							<i class="mdi mdi-cellphone"></i>
						</button>
						<button type="button" class="btn btn-outline-primary" id="ibPreviewRefresh" title="{{ __('admin.invitation-builder-preview-refresh') }}">
							<i class="mdi mdi-refresh"></i>
						</button>
					</div>
				</div>
				<div class="card-body position-relative p-3">
					<div id="ibPreviewLoading" class="ib-preview-loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<div id="ibPreviewDevice" class="ib-preview-device is-desktop">
						<iframe id="ibPreviewFrame" title="{{ __('admin.invitation-builder-live-preview') }}"></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="card mt-1">
	<div class="card-header">{{ __('admin.invitation-builder-features-list') }}</div>
	<div class="card-body">
		<div class="row">
			@foreach($catalog['features'] as $key => $feature)
			<div class="col-md-4 col-6 mb-2">
				<span class="badge bg-light text-dark border">✓ {{ $feature['label_ar'] }}</span>
			</div>
			@endforeach
		</div>
		<p class="text-muted small mt-2 mb-0">{{ __('admin.invitation-builder-features-note') }}</p>
	</div>
</div>
@endsection

@section('extra-js')
<script>
(function () {
	const form = document.getElementById('invitationBuilderForm');
	const iframe = document.getElementById('ibPreviewFrame');
	const loading = document.getElementById('ibPreviewLoading');
	const deviceWrap = document.getElementById('ibPreviewDevice');
	const previewUrl = @json($previewPostUrl);
	const csrf = @json(csrf_token());
	let debounceTimer = null;
	let previewSeq = 0;

	function setLoading(show) {
		loading.classList.toggle('d-none', !show);
	}

	function refreshPreview() {
		const seq = ++previewSeq;
		setLoading(true);

		const body = new FormData(form);
		body.delete('_method');
		body.delete('publish');

		fetch(previewUrl, {
			method: 'POST',
			body: body,
			headers: {
				'X-CSRF-TOKEN': csrf,
				'Accept': 'text/html',
			},
			credentials: 'same-origin',
		})
			.then(function (res) {
				if (!res.ok) throw new Error('Preview failed');
				return res.text();
			})
			.then(function (html) {
				if (seq !== previewSeq) return;
				iframe.srcdoc = html;
			})
			.catch(function () {
				if (seq !== previewSeq) return;
				iframe.srcdoc = '<body style="font-family:sans-serif;padding:24px;color:#c00;text-align:center;">' + @json(__('admin.invitation-builder-preview-error')) + '</body>';
			})
			.finally(function () {
				if (seq === previewSeq) setLoading(false);
			});
	}

	function schedulePreview() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(refreshPreview, 450);
	}

	form.querySelectorAll('.ib-preview-field').forEach(function (el) {
		el.addEventListener('input', schedulePreview);
		el.addEventListener('change', schedulePreview);
	});

	document.getElementById('ibPreviewRefresh').addEventListener('click', function () {
		clearTimeout(debounceTimer);
		refreshPreview();
	});

	document.querySelectorAll('[data-ib-device]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			document.querySelectorAll('[data-ib-device]').forEach(function (b) {
				b.classList.remove('active');
			});
			btn.classList.add('active');
			const mode = btn.getAttribute('data-ib-device');
			deviceWrap.classList.toggle('is-mobile', mode === 'mobile');
			deviceWrap.classList.toggle('is-desktop', mode === 'desktop');
		});
	});

	refreshPreview();
})();
</script>
@endsection
