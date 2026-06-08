@extends('layouts.app')
@section('extra-css')
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|cairo:400,600,700|playfair-display:400,700|amiri:400|tajawal:400|great-vibes:400"
	rel="stylesheet">
<style>
body {
	font-family: Poppins, 'Segoe UI', Tahoma, sans-serif;
}
</style>
@include('admin.invitation-builder.partials.preview-panel-styles')
<style>
.ib-builder-layout {
	align-items: flex-start;
}

.ib-preview-sticky {
	position: sticky;
	top: 88px;
	z-index: 10;
}

.ib-preview-device.is-live-updating iframe {
	opacity: 0.94;
	transition: opacity 0.15s ease;
}

.ib-builder-tabs .nav-link {
	font-weight: 600;
	border-radius: 8px 8px 0 0;
}

.ib-builder-tabs .nav-link.active {
	background: #fff;
	border-bottom-color: #fff;
}

.ib-tab-pane {
	min-height: 320px;
}

.ib-theme-card {
	border-radius: 12px;
	overflow: hidden;
	background: #fff;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
	transition: transform 0.15s, box-shadow 0.15s;
}

.ib-theme-card:hover {
	transform: translateY(-2px);
	box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.ib-theme-card.is-active {
	outline: 3px solid var(--bs-primary);
	outline-offset: 2px;
}

.ib-theme-preview {
	height: 72px;
}

.ib-theme-preview-video {
	height: 120px;
	overflow: hidden;
	background: #1a1520;
	position: relative;
}

.ib-theme-preview-vid,
.ib-theme-preview-img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	display: block;
	pointer-events: none;
}

.ib-theme-preview-media {
	height: 120px;
	overflow: hidden;
	background: #1a1520;
}

.ib-theme-preview {
	position: relative;
}

.ib-theme-preview-video,
.ib-theme-preview-media {
	position: relative;
}

.ib-theme-type-badge {
	position: absolute;
	top: 6px;
	right: 6px;
	z-index: 2;
	padding: 2px 8px;
	border-radius: 999px;
	font-size: 0.65rem;
	font-weight: 700;
	line-height: 1.4;
	color: #fff;
	background: rgba(18, 18, 35, 0.72);
	backdrop-filter: blur(4px);
	pointer-events: none;
}

.ib-theme-type-video {
	background: rgba(37, 99, 235, 0.88);
}

.ib-theme-type-gif {
	background: rgba(124, 58, 237, 0.88);
}

.ib-theme-type-image {
	background: rgba(5, 150, 105, 0.88);
}

.ib-theme-meta {
	background: #f8f9fa;
}

.ib-theme-delete-btn {
	position: absolute;
	top: 6px;
	left: 6px;
	z-index: 3;
	width: 28px;
	height: 28px;
	padding: 0;
	line-height: 1;
	border-radius: 50%;
}

.ib-envelope-swatch {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 4px;
	cursor: pointer;
	padding: 6px;
	border-radius: 8px;
	border: 2px solid transparent;
}

.ib-envelope-swatch span:first-of-type {
	width: 40px;
	height: 40px;
	border-radius: 50%;
	border: 1px solid rgba(0, 0, 0, 0.15);
}

.ib-envelope-swatch.is-active {
	border-color: var(--bs-primary);
	background: rgba(13, 110, 253, 0.08);
}

.ib-envelope-swatch small {
	font-size: 10px;
	max-width: 64px;
	text-align: center;
}

.ib-env-image-card {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 6px;
	width: 88px;
	padding: 8px 6px;
	border: 2px solid #e9ecef;
	border-radius: 10px;
	background: #fff;
	cursor: pointer;
	transition: border-color 0.15s, box-shadow 0.15s;
}

.ib-env-image-card img {
	width: 72px;
	height: 88px;
	object-fit: cover;
	border-radius: 4px;
	background: #f8f9fa;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.ib-env-image-card small {
	font-size: 10px;
	text-align: center;
	line-height: 1.2;
	max-width: 80px;
	color: #6c757d;
}

.ib-env-image-card.is-active {
	border-color: var(--bs-primary);
	box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

.ib-env-image-none {
	width: 72px;
	height: 88px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.5rem;
	color: #adb5bd;
	background: #f8f9fa;
	border-radius: 4px;
}

#ibEnvelopeImagePicker {
	max-height: 320px;
	overflow-y: auto;
}

.ib-envelope-shape-card {
	display: block;
	cursor: pointer;
	padding: 8px 6px;
	border: 2px solid #e9ecef;
	border-radius: 10px;
	background: #fff;
	transition: border-color 0.15s, box-shadow 0.15s;
}

.ib-envelope-shape-card.is-active {
	border-color: var(--bs-primary);
	box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
}

.ib-envelope-shape-card small {
	font-size: 10px;
	color: #6c757d;
	line-height: 1.2;
}

.ib-env-shape-mini {
	position: relative;
	width: 100%;
	height: 72px;
	margin: 0 auto;
	border-radius: 6px;
	background: linear-gradient(165deg, #faf7f2, #e8dcc8);
	box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.06);
	overflow: hidden;
}

.ib-mini-body {
	position: absolute;
	inset: 8% 10% 10%;
	background: linear-gradient(180deg, #f5f0e6, #ddd0b8);
	border-radius: 2px;
	box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.06);
}

.ib-mini-liner {
	position: absolute;
	left: 18%;
	right: 18%;
	top: 28%;
	height: 32%;
	background: linear-gradient(180deg, rgba(200, 169, 122, 0.35), transparent);
	clip-path: polygon(0 0, 50% 100%, 100% 0);
	z-index: 1;
}

.ib-mini-flap {
	position: absolute;
	left: 8%;
	right: 8%;
	top: 6%;
	height: 48%;
	background: linear-gradient(180deg, #fffef8, #e0d4bc);
	z-index: 2;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.ib-env-shape-mini-european .ib-mini-flap {
	height: 54%;
	clip-path: polygon(0 0, 50% 100%, 100% 0);
}

.ib-env-shape-mini-classic .ib-mini-flap,
.ib-env-shape-mini-luxe .ib-mini-flap,
.ib-env-shape-mini-vintage .ib-mini-flap {
	clip-path: polygon(0 0, 50% 100%, 100% 0);
}

.ib-env-shape-mini-square .ib-mini-flap {
	height: 42%;
	clip-path: polygon(0 0, 10% 75%, 50% 100%, 90% 75%, 100% 0);
}

.ib-env-shape-mini-luxe .ib-mini-body {
	box-shadow: inset 0 0 0 1px rgba(200, 169, 122, 0.4);
}

.ib-env-shape-mini-vintage .ib-mini-body {
	filter: sepia(0.12);
	border-radius: 3px 5px 4px 6px;
}

.ib-env-shape-mini-pocket .ib-mini-flap {
	clip-path: polygon(0 0, 100% 0, 100% 100%, 0 50%);
	height: 44%;
}

.ib-env-shape-mini-pocket .ib-mini-body {
	top: 18%;
}

.ib-seal-option {
	cursor: pointer;
}

#ibSealStyleGrid {
	max-height: 360px;
	overflow-y: auto;
}

.ib-seal-preview-wrap {
	width: 56px;
	height: 56px;
	margin: 0 auto;
	position: relative;
	display: flex;
	align-items: center;
	justify-content: center;
}

.ib-seal-color-picker {
	width: 42px;
	height: 32px;
	padding: 2px;
	cursor: pointer;
}

.ib-seal-color-swatch {
	width: 26px;
	height: 26px;
	border-radius: 50%;
	border: 2px solid #dee2e6;
	padding: 0;
	cursor: pointer;
	transition: transform 0.12s, box-shadow 0.12s;
}

.ib-seal-color-swatch.is-active,
.ib-seal-color-swatch:hover {
	border-color: var(--bs-primary);
	transform: scale(1.08);
	box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2);
}

.ib-seal-color-dot {
	display: block;
	width: 14px;
	height: 14px;
	border-radius: 50%;
	margin: 6px auto 0;
	border: 1px solid rgba(0, 0, 0, 0.12);
	box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.35);
}

.ib-seal-option .small {
	font-size: 10px;
	line-height: 1.2;
	color: #6c757d;
}

.ib-seal-preview-wrap .wi-env-seal.ib-seal-mini {
	width: 52px;
	height: 52px;
	font-size: 0.45rem;
	padding: 4px;
	pointer-events: none;
}

.ib-block-item {
	cursor: grab;
}

.ib-block-item.dragging {
	opacity: 0.5;
}

.ib-block-fields-wrap {
	margin-top: 0.5rem;
}

.ib-block-item .ib-block-fields {
	background: rgba(0, 0, 0, 0.02);
	border-radius: 8px;
	padding: 0.75rem 0.5rem 0.25rem;
}

.ib-block-field input[type="date"],
.ib-block-field input[type="time"],
.ib-block-field input[type="datetime-local"] {
	min-height: 31px;
}

.ib-block-field .form-control-color {
	height: 31px;
	padding: 2px 4px;
}

.ib-drag-handle {
	user-select: none;
}

.cursor-grab {
	cursor: grab;
}

#ibThemeGrid {
	max-height: 420px;
	overflow-y: auto;
}
</style>
<style>
@include('invitation.templates.partials.builder-wedding-seal-styles')
</style>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0">{{ __('admin.invitation-builder') }} — {{ $invitation->event_name }}
			</h4>
			<div class="page-title-right d-flex flex-wrap gap-2">
				<button type="button" id="ibOpenPreviewTab"
					class="btn btn-outline-primary btn-sm">
					{{ __('admin.invitation-builder-preview-tab') }}
				</button>
				<a href="{{ route('invitation.edit', $invitation) }}"
					class="btn btn-secondary btn-sm">{{ __('admin.back') }}</a>
			</div>
		</div>
	</div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
@include('admin.invitation-builder.partials.validation-errors')
@endif

<form method="post" action="{{ route('admin.invitation-builder.update', $invitation) }}" id="invitationBuilderForm">
	@csrf
	@method('PUT')
	<input type="hidden" name="opening_type" value="envelope">

	<div class="row ib-builder-layout">
		<div class="col-xl-7 col-lg-12">
			<div class="card mb-3">
				<div class="card-body p-0">

					<ul class="nav nav-tabs nav-justified ib-builder-tabs px-2 pt-2"
						role="tablist">
						<li class="nav-item">
							<button class="nav-link active" data-bs-toggle="tab"
								data-bs-target="#ibTabEnvelope"
								type="button">
								<i
									class="mdi mdi-email-outline me-1"></i>{{ __('admin.ib-tab-envelope') }}
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link"
								data-bs-toggle="tab"
								data-bs-target="#ibTabThemes"
								type="button">
								<i
									class="mdi mdi-palette-outline me-1"></i>{{ __('admin.ib-tab-themes') }}
							</button>
						</li>

						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab"
								data-bs-target="#ibTabDetails"
								type="button">
								<i
									class="mdi mdi-text-box-outline me-1"></i>{{ __('admin.ib-tab-details') }}
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab"
								data-bs-target="#ibTabBlocks"
								type="button">
								<i
									class="mdi mdi-view-grid-plus-outline me-1"></i>{{ __('admin.ib-tab-blocks') }}
							</button>
						</li>
					</ul>
					<div class="tab-content p-4 border-top">
						<div class="tab-pane fade show active ib-tab-pane" id="ibTabEnvelope">
							@include('admin.invitation-builder.partials.tab-envelope')
						</div>
						<div class="tab-pane fade ib-tab-pane"
							id="ibTabThemes">
							@include('admin.invitation-builder.partials.tab-themes')
						</div>
						
						<div class="tab-pane fade ib-tab-pane" id="ibTabDetails">
							@include('admin.invitation-builder.partials.tab-details')
						</div>
						<div class="tab-pane fade ib-tab-pane" id="ibTabBlocks">
							@include('admin.invitation-builder.partials.tab-blocks')
						</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div
					class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
					<div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox"
								name="publish" value="1" id="publish"
								@checked($config['published'])>
							<label class="form-check-label"
								for="publish">{{ __('admin.invitation-builder-publish') }}</label>
						</div>
						<small
							class="text-muted">{{ __('admin.invitation-builder-publish-hint') }}</small>
					</div>
					<button type="submit"
						class="btn btn-success btn-lg">{{ __('admin.save') }}</button>
				</div>
			</div>
		</div>

		<div class="col-xl-5 col-lg-12">
			<div class="card ib-preview-sticky mb-3">
				<div
					class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
					<div>
						<strong>{{ __('admin.invitation-builder-live-preview') }}</strong>
						<small
							class="text-muted d-block">{{ __('admin.invitation-builder-live-preview-hint') }}</small>
					</div>
					<div class="btn-group btn-group-sm">
						<button type="button"
							class="btn btn-outline-secondary active"
							data-ib-device="desktop"><i
								class="mdi mdi-monitor"></i></button>
						<button type="button" class="btn btn-outline-secondary"
							data-ib-device="mobile"><i
								class="mdi mdi-cellphone"></i></button>
						<button type="button" class="btn btn-outline-primary"
							id="ibPreviewRefresh"><i
								class="mdi mdi-refresh"></i></button>
					</div>
				</div>
				<div class="card-body position-relative p-3">
					<div id="ibPreviewLoading" class="ib-preview-loading">
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
	</div>
</form>
@endsection

@section('extra-js')
@include('admin.invitation-builder.partials.builder-scripts')
@endsection
