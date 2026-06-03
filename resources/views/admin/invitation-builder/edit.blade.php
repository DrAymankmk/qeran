@extends('layouts.app')
@section('extra-css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Playfair+Display:wght@400;700&family=Amiri&family=Tajawal&family=Great+Vibes&display=swap" rel="stylesheet">
<style>
.ib-builder-layout { align-items: flex-start; }
.ib-preview-sticky { position: sticky; top: 88px; z-index: 10; }
.ib-preview-device {
	background: linear-gradient(145deg, #1e1e2e, #2a2a40);
	border-radius: 16px; padding: 14px; margin: 0 auto; transition: max-width 0.25s ease;
}
.ib-preview-device.is-mobile { max-width: 390px; box-shadow: 0 0 0 3px #333, 0 0 0 6px #1a1a1a; border-radius: 28px; }
.ib-preview-device.is-desktop { max-width: 100%; }
.ib-preview-device iframe { width: 100%; border: 0; display: block; background: #0f0f18; border-radius: 8px; }
.ib-preview-device.is-desktop iframe { height: min(72vh, 680px); }
.ib-preview-device.is-mobile iframe { height: min(68vh, 640px); border-radius: 12px; }
.ib-preview-loading {
	position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
	background: rgba(255,255,255,0.85); border-radius: 12px; z-index: 2;
}
.ib-preview-loading.d-none { display: none !important; }
.ib-builder-tabs .nav-link { font-weight: 600; border-radius: 8px 8px 0 0; }
.ib-builder-tabs .nav-link.active { background: #fff; border-bottom-color: #fff; }
.ib-tab-pane { min-height: 320px; }
.ib-theme-card {
	border-radius: 12px; overflow: hidden; background: #fff;
	box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.15s, box-shadow 0.15s;
}
.ib-theme-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.12); }
.ib-theme-card.is-active { outline: 3px solid var(--bs-primary); outline-offset: 2px; }
.ib-theme-preview { height: 72px; }
.ib-envelope-swatch {
	display: flex; flex-direction: column; align-items: center; gap: 4px;
	cursor: pointer; padding: 6px; border-radius: 8px; border: 2px solid transparent;
}
.ib-envelope-swatch span:first-of-type {
	width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.15);
}
.ib-envelope-swatch.is-active { border-color: var(--bs-primary); background: rgba(13,110,253,0.08); }
.ib-envelope-swatch small { font-size: 10px; max-width: 64px; text-align: center; }
.ib-seal-option { cursor: pointer; }
.ib-block-item { cursor: grab; }
.ib-block-item.dragging { opacity: 0.5; }
.ib-drag-handle { user-select: none; }
.cursor-grab { cursor: grab; }
#ibThemeGrid { max-height: 420px; overflow-y: auto; }
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
				<a href="{{ route('invitation.edit', $invitation) }}" class="btn btn-secondary btn-sm">{{ __('admin.back') }}</a>
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
	<input type="hidden" name="opening_type" value="envelope">

	<div class="row ib-builder-layout">
		<div class="col-xl-7 col-lg-12">
			<div class="card mb-3">
				<div class="card-body p-0">
					<ul class="nav nav-tabs nav-justified ib-builder-tabs px-2 pt-2" role="tablist">
						<li class="nav-item">
							<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ibTabThemes" type="button">
								<i class="mdi mdi-palette-outline me-1"></i>{{ __('admin.ib-tab-themes') }}
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#ibTabEnvelope" type="button">
								<i class="mdi mdi-email-outline me-1"></i>{{ __('admin.ib-tab-envelope') }}
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#ibTabDetails" type="button">
								<i class="mdi mdi-text-box-outline me-1"></i>{{ __('admin.ib-tab-details') }}
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#ibTabBlocks" type="button">
								<i class="mdi mdi-view-grid-plus-outline me-1"></i>{{ __('admin.ib-tab-blocks') }}
							</button>
						</li>
					</ul>
					<div class="tab-content p-4 border-top">
						<div class="tab-pane fade show active ib-tab-pane" id="ibTabThemes">
							@include('admin.invitation-builder.partials.tab-themes')
						</div>
						<div class="tab-pane fade ib-tab-pane" id="ibTabEnvelope">
							@include('admin.invitation-builder.partials.tab-envelope')
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
				<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
					<div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="publish" value="1" id="publish" @checked($config['published'])>
							<label class="form-check-label" for="publish">{{ __('admin.invitation-builder-publish') }}</label>
						</div>
						<small class="text-muted">{{ __('admin.invitation-builder-publish-hint') }}</small>
					</div>
					<button type="submit" class="btn btn-success btn-lg">{{ __('admin.save') }}</button>
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
					<div class="btn-group btn-group-sm">
						<button type="button" class="btn btn-outline-secondary active" data-ib-device="desktop"><i class="mdi mdi-monitor"></i></button>
						<button type="button" class="btn btn-outline-secondary" data-ib-device="mobile"><i class="mdi mdi-cellphone"></i></button>
						<button type="button" class="btn btn-outline-primary" id="ibPreviewRefresh"><i class="mdi mdi-refresh"></i></button>
					</div>
				</div>
				<div class="card-body position-relative p-3">
					<div id="ibPreviewLoading" class="ib-preview-loading">
						<div class="spinner-border text-primary"></div>
					</div>
					<div id="ibPreviewDevice" class="ib-preview-device is-desktop">
						<iframe id="ibPreviewFrame" title="{{ __('admin.invitation-builder-live-preview') }}"></iframe>
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
