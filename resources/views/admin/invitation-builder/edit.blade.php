@extends('layouts.app')
@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0">{{ __('admin.invitation-builder') }} — {{ $invitation->event_name }}</h4>
			<div class="page-title-right">
				<a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
					{{ __('admin.invitation-builder-preview') }}
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

<form method="post" action="{{ route('admin.invitation-builder.update', $invitation) }}">
	@csrf
	@method('PUT')

	<div class="row">
		<div class="col-lg-4">
			<div class="card mb-3">
				<div class="card-header bg-primary text-white">{{ __('admin.invitation-builder-types') }}</div>
				<div class="card-body">
					<label class="form-label">{{ __('admin.invitation-builder-event-type') }}</label>
					<select name="event_category" class="form-select">
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
					<select name="theme_template" class="form-select mb-2">
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
					<select name="theme_mode" class="form-select">
						@foreach($catalog['theme_modes'] as $key => $mode)
						<option value="{{ $key }}" @selected($config['theme_mode'] === $key)>{{ $mode['label_ar'] }}</option>
						@endforeach
					</select>
					<div class="form-check mt-2">
						<input class="form-check-input" type="checkbox" name="animated_theme" value="1" id="animated_theme" @checked($config['animated_theme'])>
						<label class="form-check-label" for="animated_theme">{{ __('admin.invitation-builder-animated') }}</label>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card mb-3">
				<div class="card-header">{{ __('admin.invitation-builder-opening') }}</div>
				<div class="card-body">
					<label class="form-label">{{ __('admin.invitation-builder-opening-type') }}</label>
					<select name="opening_type" class="form-select mb-2">
						@foreach($catalog['opening_types'] as $key => $op)
						<option value="{{ $key }}" @selected($config['opening_type'] === $key)>{{ $op['label_ar'] }}</option>
						@endforeach
					</select>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="welcome_enabled" value="1" id="welcome_enabled" @checked($config['welcome_enabled'])>
						<label class="form-check-label" for="welcome_enabled">{{ __('admin.invitation-builder-welcome-screen') }}</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="music_enabled" value="1" id="music_enabled" @checked($config['music_enabled'])>
						<label class="form-check-label" for="music_enabled">{{ __('admin.invitation-builder-music') }}</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="intro_video_enabled" value="1" id="intro_video_enabled" @checked($config['intro_video_enabled'])>
						<label class="form-check-label" for="intro_video_enabled">{{ __('admin.invitation-builder-intro-video') }}</label>
					</div>
					<hr>
					<label class="form-label">{{ __('admin.invitation-builder-welcome-title') }}</label>
					<input type="text" name="welcome_title" class="form-control mb-2" value="{{ old('welcome_title', $config['welcome_title']) }}">
					<label class="form-label">{{ __('admin.invitation-builder-welcome-subtitle') }}</label>
					<input type="text" name="welcome_subtitle" class="form-control" value="{{ old('welcome_subtitle', $config['welcome_subtitle']) }}">
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card mb-3">
				<div class="card-header">{{ __('admin.invitation-builder-visual') }}</div>
				<div class="card-body">
					<div class="row g-2">
						<div class="col-6">
							<label class="form-label">{{ __('admin.invitation-builder-primary-color') }}</label>
							<input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $config['primary_color']) }}">
						</div>
						<div class="col-6">
							<label class="form-label">{{ __('admin.invitation-builder-secondary-color') }}</label>
							<input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', $config['secondary_color']) }}">
						</div>
						<div class="col-6">
							<label class="form-label">{{ __('admin.invitation-builder-bg-color') }}</label>
							<input type="color" name="background_color" class="form-control form-control-color w-100" value="{{ old('background_color', $config['background_color']) }}">
						</div>
						<div class="col-6">
							<label class="form-label">{{ __('admin.invitation-builder-text-color') }}</label>
							<input type="color" name="text_color" class="form-control form-control-color w-100" value="{{ old('text_color', $config['text_color']) }}">
						</div>
					</div>
					<label class="form-label mt-2">{{ __('admin.invitation-builder-font') }}</label>
					<input type="text" name="font_family" class="form-control mb-2" value="{{ old('font_family', $config['font_family']) }}" placeholder="Cairo">
					<label class="form-label">{{ __('admin.invitation-builder-logo-url') }}</label>
					<input type="url" name="logo_url" class="form-control mb-2" value="{{ old('logo_url', $config['logo_url']) }}" placeholder="https://">
					<label class="form-label">{{ __('admin.invitation-builder-bg-media-url') }}</label>
					<input type="url" name="background_media_url" class="form-control mb-2" value="{{ old('background_media_url', $config['background_media_url']) }}">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="video_background" value="1" id="video_background" @checked($config['video_background'])>
						<label class="form-check-label" for="video_background">{{ __('admin.invitation-builder-video-bg') }}</label>
					</div>
					<label class="form-label mt-2">{{ __('admin.invitation-builder-custom-css') }}</label>
					<textarea name="custom_css" class="form-control" rows="4" placeholder=".front { }">{{ old('custom_css', $config['custom_css']) }}</textarea>
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
			<button type="submit" class="btn btn-success">{{ __('admin.save') }}</button>
		</div>
	</div>
</form>

<div class="card mt-3">
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
