<div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-themes-title') }}</h5>
	<!-- <p class="text-muted mb-2">{{ __('admin.ib-tab-themes-subtitle') }}</p> -->
	<!-- <p class="small text-muted mb-0">{{ __('admin.ib-tab-themes-desc') }}</p> -->
</div>

<!-- <ul class="list-unstyled small text-muted mb-3">
	<li>✓ {{ __('admin.ib-tab-themes-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-themes-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-themes-bullet-3') }}</li>
	<li>✓ {{ __('admin.ib-tab-themes-bullet-4') }}</li>
</ul> -->

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
	<select name="event_category" class="form-select ib-preview-field">
		@foreach($catalog['event_types'] as $key => $type)
		<option value="{{ $key }}" @selected($config['event_category']===$key)>
			{{ $type['icon'] ?? '' }} {{ $type['label_ar'] ?? $key }}
		</option>
		@endforeach
	</select>
</div>

<p class="small text-muted mb-2">{{ __('admin.ib-opening-themes-hint') }}</p>

<div class="row g-2" id="ibThemeGrid">
	@foreach($catalog['animated_themes'] as $slug => $theme)
	@php
		$videoUrl = $theme['opening_video_url'] ?? '';
		$activeSlug = app(\App\Services\Invitation\InvitationBuilderService::class)
			->normalizeThemeSlug($config['theme_slug'] ?? null);
		$isActive = $activeSlug === $slug;
	@endphp
	<div class="col-6 ib-theme-card-wrap" data-category="{{ $theme['category'] ?? '' }}">
		<button type="button"
			class="ib-theme-card w-100 border-0 p-0 text-start @if($isActive) is-active @endif"
			data-slug="{{ $slug }}" data-primary="{{ $theme['primary_color'] }}"
			data-secondary="{{ $theme['secondary_color'] }}"
			data-bg="{{ $theme['background_color'] }}" data-text="{{ $theme['text_color'] }}"
			@if($videoUrl) data-video="{{ $videoUrl }}" @endif
			>
			@if($videoUrl)
			<div class="ib-theme-preview ib-theme-preview-video">
				<video class="ib-theme-preview-vid" muted loop playsinline preload="metadata"
					src="{{ $videoUrl }}"></video>
			</div>
			@else
			<div class="ib-theme-preview" style="background: {{ $theme['preview'] }};"></div>
			@endif
			<!-- <div class="ib-theme-meta p-2">
				<strong class="d-block small">{{ $theme['name_ar'] }}</strong>
			</div> -->
		</button>
	</div>
	@endforeach
</div>

<input type="hidden" name="background_media_url" id="background_media_url"
	value="{{ old('background_media_url', $config['background_media_url']) }}" class="ib-preview-field">
<input type="hidden" name="video_background" id="video_background_hidden"
	value="{{ old('video_background', $config['video_background']) ? '1' : '0' }}" class="ib-preview-field">

<hr class="my-4">

<!-- <div class="row g-3">
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-custom-background') }}</label>
		<input type="url" id="background_media_url_custom" class="form-control ib-preview-field"
			value="{{ old('background_media_url', $config['background_media_url']) }}"
			placeholder="https://">
		<div class="form-check mt-2">
			<input class="form-check-input ib-preview-field" type="checkbox"
				value="1" id="video_background" @checked($config['video_background'])>
			<label class="form-check-label"
				for="video_background">{{ __('admin.invitation-builder-video-bg') }}</label>
		</div>
		<small class="text-muted d-block mt-1">{{ __('admin.ib-custom-background-hint') }}</small>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.invitation-builder-theme-mode') }}</label>
		<select name="theme_mode" class="form-select ib-preview-field">
			@foreach($catalog['theme_modes'] as $key => $mode)
			<option value="{{ $key }}" @selected($config['theme_mode']===$key)>{{ $mode['label_ar'] }}
			</option>
			@endforeach
		</select>
		<div class="form-check mt-2">
			<input class="form-check-input ib-preview-field" type="checkbox" name="music_enabled"
				value="1" id="music_enabled" @checked($config['music_enabled'])>
			<label class="form-check-label"
				for="music_enabled">{{ __('admin.invitation-builder-music') }}</label>
		</div>
	</div>
</div> -->
