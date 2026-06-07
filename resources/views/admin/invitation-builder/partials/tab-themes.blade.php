<div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-themes-title') }}</h5>
</div>

<input type="hidden" name="theme_mode" value="{{ old('theme_mode', $config['theme_mode'] ?? 'dark') }}" class="ib-preview-field">
<input type="hidden" name="theme_slug" id="theme_slug" value="{{ old('theme_slug', $config['theme_slug']) }}"
	class="ib-preview-field">
<input type="hidden" name="primary_color" id="primary_color"
	value="{{ old('primary_color', $config['primary_color']) }}" class="ib-preview-field">
<input type="hidden" name="secondary_color" id="secondary_color"
	value="{{ old('secondary_color', $config['secondary_color']) }}" class="ib-preview-field">
<input type="hidden" name="background_color" id="background_color"
	value="{{ old('background_color', $config['background_color']) }}" class="ib-preview-field">
<input type="hidden" name="text_color" id="text_color" value="{{ old('text_color', $config['text_color']) }}"
	class="ib-preview-field">

<div class="mb-3">
	<label class="form-label">{{ __('admin.invitation-builder-event-type') }}</label>
	<select name="event_category" class="form-select ib-preview-field @error('event_category') is-invalid @enderror">
		@foreach($catalog['event_types'] as $key => $type)
		<option value="{{ $key }}" @selected($config['event_category']===$key)>
			{{ $type['icon'] ?? '' }} {{ $type['label_ar'] ?? $key }}
		</option>
		@endforeach
	</select>
	@error('event_category')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	@error('theme_slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	@error('theme_mode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<div class="card border mb-4" id="ibThemeUploadPanel">
	<div class="card-body">
		<h6 class="fw-bold mb-2">{{ __('admin.ib-theme-upload-title') }}</h6>
		<p class="small text-muted mb-3">{{ __('admin.ib-theme-upload-hint') }}</p>
		<div class="row g-3 align-items-end">
			<div class="col-md-4">
				<label class="form-label" for="ibThemeNameAr">{{ __('admin.ib-theme-upload-name') }}</label>
				<input type="text" id="ibThemeNameAr" class="form-control" maxlength="120"
					placeholder="{{ __('admin.ib-theme-upload-name-placeholder') }}">
			</div>
			<div class="col-md-3">
				<label class="form-label" for="ibThemeMediaType">{{ __('admin.ib-theme-upload-type') }}</label>
				<select id="ibThemeMediaType" class="form-select">
					<option value="video">{{ __('admin.ib-theme-upload-type-video') }}</option>
					<option value="gif">{{ __('admin.ib-theme-upload-type-gif') }}</option>
					<option value="image">{{ __('admin.ib-theme-upload-type-image') }}</option>
				</select>
			</div>
			<div class="col-md-5">
				<label class="form-label" for="ibThemeMediaInput">{{ __('admin.ib-theme-upload-file') }}</label>
				<input type="file" id="ibThemeMediaInput" class="form-control"
					accept="video/mp4,video/webm,image/gif,image/jpeg,image/png,image/webp">
			</div>
			<div class="col-12">
				<button type="button" class="btn btn-primary btn-sm" id="ibThemeUploadBtn">
					<i class="mdi mdi-upload me-1"></i>{{ __('admin.ib-theme-upload-submit') }}
				</button>
				<span class="small text-muted ms-2" id="ibThemeUploadStatus"></span>
			</div>
		</div>
	</div>
</div>

<p class="small text-muted mb-2">{{ __('admin.ib-opening-themes-hint') }}</p>

@php
	$activeSlug = app(\App\Services\Invitation\InvitationBuilderService::class)
		->normalizeThemeSlug($config['theme_slug'] ?? null);
@endphp

<div class="row g-2" id="ibThemeGrid">
	@foreach($catalog['animated_themes'] as $slug => $theme)
		@include('admin.invitation-builder.partials.theme-card', [
			'slug' => $slug,
			'theme' => $theme,
			'isActive' => $activeSlug === $slug,
		])
	@endforeach
</div>

<input type="hidden" name="background_media_url" id="background_media_url"
	value="{{ old('background_media_url', $config['background_media_url']) }}" class="ib-preview-field">
<input type="hidden" name="video_background" id="video_background_hidden"
	value="{{ old('video_background', $config['video_background']) ? '1' : '0' }}" class="ib-preview-field">

<hr class="my-4">
