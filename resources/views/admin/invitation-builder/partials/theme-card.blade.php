@php
	$mediaUrl = $theme['opening_media_url'] ?? ($theme['opening_video_url'] ?? '');
	$mediaType = $theme['media_type'] ?? (!empty($theme['opening_video_url']) ? 'video' : 'image');
	$isCustom = !empty($theme['is_custom']);
	$mediaTypeLabel = match ($mediaType) {
		'video' => __('admin.ib-theme-upload-type-video'),
		'gif' => __('admin.ib-theme-upload-type-gif'),
		default => __('admin.ib-theme-upload-type-image'),
	};
@endphp
<div class="col-6 ib-theme-card-wrap" data-category="{{ $theme['category'] ?? '' }}">
	<div class="position-relative">
		@if($isCustom)
		<button type="button"
			class="btn btn-sm btn-danger ib-theme-delete-btn"
			data-theme-slug="{{ $slug }}"
			title="{{ __('admin.ib-theme-delete') }}"
			aria-label="{{ __('admin.ib-theme-delete') }}">
			<i class="mdi mdi-close"></i>
		</button>
		@endif
		<button type="button"
			class="ib-theme-card w-100 border-0 p-0 text-start @if($isActive) is-active @endif"
			data-slug="{{ $slug }}"
			data-primary="{{ $theme['primary_color'] }}"
			data-secondary="{{ $theme['secondary_color'] }}"
			data-bg="{{ $theme['background_color'] }}"
			data-text="{{ $theme['text_color'] }}"
			@if($mediaUrl) data-media-url="{{ $mediaUrl }}" data-media-type="{{ $mediaType }}" @endif
			>
			@if($mediaType === 'video' && $mediaUrl)
			<div class="ib-theme-preview ib-theme-preview-video">
				<span class="ib-theme-type-badge ib-theme-type-{{ $mediaType }}">{{ $mediaTypeLabel }}</span>
				<video class="ib-theme-preview-vid" muted loop playsinline preload="metadata"
					src="{{ $mediaUrl }}"></video>
			</div>
			@elseif($mediaUrl)
			<div class="ib-theme-preview ib-theme-preview-media">
				<span class="ib-theme-type-badge ib-theme-type-{{ $mediaType }}">{{ $mediaTypeLabel }}</span>
				<img class="ib-theme-preview-img" src="{{ $mediaUrl }}" alt="{{ $theme['name_ar'] ?? $slug }}">
			</div>
			@else
			<div class="ib-theme-preview" style="background: {{ $theme['preview'] ?? '#1a1520' }};">
				<span class="ib-theme-type-badge ib-theme-type-{{ $mediaType }}">{{ $mediaTypeLabel }}</span>
			</div>
			@endif
			@if(!empty($theme['name_ar']))
			<div class="ib-theme-meta px-2 py-1">
				<strong class="d-block small text-truncate">{{ $theme['name_ar'] }}</strong>
			</div>
			@endif
		</button>
	</div>
</div>
