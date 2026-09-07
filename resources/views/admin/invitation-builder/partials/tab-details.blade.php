<!-- <div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-details-title') }}</h5>
	<p class="text-muted mb-2">{{ __('admin.ib-tab-details-subtitle') }}</p>
	<p class="small text-muted mb-0">{{ __('admin.ib-tab-details-desc') }}</p>
</div>

<ul class="list-unstyled small text-muted mb-4">
	<li>✓ {{ __('admin.ib-tab-details-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-details-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-details-bullet-3') }}</li>
</ul> -->

@php
	// Display guidance only — these lengths are shown as hints and are not enforced anywhere.
	$ibLimits = [
		'bride' => ['min' => 2, 'max' => 50],
		'groom' => ['min' => 2, 'max' => 50],
		'bride_father' => ['min' => 2, 'max' => 50],
		'groom_father' => ['min' => 2, 'max' => 50],
		'details_section_label' => ['min' => 2, 'max' => 30],
		'details_section_title' => ['min' => 2, 'max' => 60],
		'venue_name' => ['min' => 2, 'max' => 60],
		'venue_location' => ['min' => 2, 'max' => 120],
		'ceremony_note' => ['min' => 2, 'max' => 120],
		'reception_note' => ['min' => 2, 'max' => 60],
	];

	$ibLimitHint = function (string $field) use ($ibLimits): string {
		$min = $ibLimits[$field]['min'] ?? null;
		$max = $ibLimits[$field]['max'] ?? null;

		if ($min !== null && $max !== null) {
			$text = __('admin.ib-input-range-hint', ['min' => $min, 'max' => $max]);
		} elseif ($max !== null) {
			$text = __('admin.ib-input-max-hint', ['max' => $max]);
		} elseif ($min !== null) {
			$text = __('admin.ib-input-min-hint', ['min' => $min]);
		} else {
			return '';
		}

		return '<small class="text-muted d-block mt-1">'.e($text).'</small>';
	};
@endphp

<div class="row g-3">
	<div class="col-12">
		<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
			<h6 class="text-muted mb-0">{{ __('admin.ib-couple-section') }}</h6>
			<div class="form-check mb-0">
				<input class="form-check-input ib-preview-field" type="checkbox" name="hero_enabled" value="1"
					id="hero_enabled" @checked(filter_var(old('hero_enabled', $config['hero_enabled'] ?? true), FILTER_VALIDATE_BOOLEAN))>
				<label class="form-check-label" for="hero_enabled">{{ __('admin.ib-hero-section-toggle') }}</label>
			</div>
		</div>
		<p class="small text-muted mb-0 mt-1">{{ __('admin.ib-hero-section-toggle-hint') }}</p>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.bride') }}</label>
		<input type="text" name="bride" class="form-control ib-preview-field"
			value="{{ old('bride', $invitation->bride) }}" placeholder="{{ __('admin.bride') }}">
		{!! $ibLimitHint('bride') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.groom') }}</label>
		<input type="text" name="groom" class="form-control ib-preview-field"
			value="{{ old('groom', $invitation->groom) }}" placeholder="{{ __('admin.groom') }}">
		{!! $ibLimitHint('groom') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.bride_father') }}</label>
		<input type="text" name="bride_father" class="form-control ib-preview-field"
			value="{{ old('bride_father', $invitation->bride_father) }}"
			placeholder="{{ __('admin.bride_father') }}">
		{!! $ibLimitHint('bride_father') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.groom_father') }}</label>
		<input type="text" name="groom_father" class="form-control ib-preview-field"
			value="{{ old('groom_father', $invitation->groom_father) }}"
			placeholder="{{ __('admin.groom_father') }}">
		{!! $ibLimitHint('groom_father') !!}
	</div>

	<div class="col-12">
		<hr class="my-1">
		<h6 class="text-muted">{{ __('admin.ib-hero-accents-section') }}</h6>
		<p class="small text-muted mb-2">{{ __('admin.ib-hero-accents-section-hint') }}</p>
		@include('admin.invitation-builder.partials.block-fields', [
			'blockKey' => 'hero_accents',
			'blockData' => $config['block_data'] ?? [],
			'schemas' => $catalog['block_field_schemas'] ?? config('invitation_builder.block_field_schemas', []),
			'catalog' => $catalog,
		])
	</div>

	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-event-date') }}</label>
		<input type="date" name="event_date" class="form-control ib-preview-field mb-2"
			value="{{ old('event_date', $config['event_date'] ? \Illuminate\Support\Carbon::parse($config['event_date'])->format('Y-m-d') : '') }}">
		<label class="form-label">{{ __('admin.ib-event-time') }}</label>
		<input type="time" name="event_time" class="form-control ib-preview-field"
			value="{{ old('event_time', $config['event_time'] ? \Illuminate\Support\Carbon::parse($config['event_time'])->format('H:i') : '') }}">
	</div>

	<div class="col-12">
		<hr class="my-1">
		<h6 class="text-muted">{{ __('admin.ib-details-cards-section') }}</h6>
	</div>

	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-details-section-label') }}</label>
		<input type="text" name="details_section_label" class="form-control ib-preview-field"
			value="{{ old('details_section_label', $config['details_section_label']) }}"
			placeholder="جميع التفاصيل">
		{!! $ibLimitHint('details_section_label') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-details-section-title') }}</label>
		<input type="text" name="details_section_title" class="form-control ib-preview-field"
			value="{{ old('details_section_title', $config['details_section_title']) }}"
			placeholder="{{ $invitation->event_name }}">
		{!! $ibLimitHint('details_section_title') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-venue-name') }}</label>
		<input type="text" name="venue_name" class="form-control ib-preview-field"
			value="{{ old('venue_name', $config['venue_name']) }}"
			placeholder="{{ $invitation->event_name }}">
		{!! $ibLimitHint('venue_name') !!}
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-venue-location') }}</label>
		<input type="text" name="venue_location" class="form-control ib-preview-field"
			value="{{ old('venue_location', $config['venue_location']) }}"
			placeholder="{{ $invitation->address }}">
		{!! $ibLimitHint('venue_location') !!}
		@if($invitation->address || ($invitation->latitude && $invitation->longitude))
		<small class="text-muted d-block mt-1">
			{{ __('admin.ib-venue-location-hint') }}
			@if($invitation->address)<br>{{ __('admin.ib-invitation-address') }}:
			{{ $invitation->address }}@endif
			@if($invitation->latitude &&
			$invitation->longitude)<br>{{ __('admin.ib-invitation-coords') }}:
			{{ $invitation->latitude }}, {{ $invitation->longitude }}@endif
		</small>
		@endif
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-ceremony-note') }}</label>
		<input type="text" name="ceremony_note" class="form-control ib-preview-field"
			value="{{ old('ceremony_note', $config['ceremony_note']) }}"
			placeholder="{{ __('admin.ib-ceremony-note-placeholder') }}">
		{!! $ibLimitHint('ceremony_note') !!}
	</div>
	<div class="col-md-3">
		<label class="form-label">{{ __('admin.ib-reception-time') }}</label>
		<input type="time" name="reception_time" class="form-control ib-preview-field"
			value="{{ old('reception_time', $config['reception_time'] ? \Illuminate\Support\Carbon::parse($config['reception_time'])->format('H:i') : '') }}">
	</div>
	<div class="col-md-3">
		<label class="form-label">{{ __('admin.ib-reception-note') }}</label>
		<input type="text" name="reception_note" class="form-control ib-preview-field"
			value="{{ old('reception_note', $config['reception_note']) }}">
		{!! $ibLimitHint('reception_note') !!}
	</div>

	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-headline-font') }}</label>
		<select name="headline_font" class="form-select ib-preview-field">
			@foreach($catalog['fonts'] as $key => $label)
			<option value="{{ $key }}" @selected(($config['headline_font'] ?? '' )===$key)>
				{{ $label }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-body-font') }}</label>
		<select name="font_family" class="form-select ib-preview-field">
			@foreach($catalog['fonts'] as $key => $label)
			<option value="{{ $key }}" @selected($config['font_family']===$key)>{{ $label }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.invitation-builder-text-color') }}</label>
		<input type="color" name="text_color_visible"
			class="form-control form-control-color w-100 ib-color-sync @error('text_color') is-invalid @enderror"
			data-target="text_color" value="{{ old('text_color', $config['text_color']) }}">
		@error('text_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-date-position') }}</label>
		<select name="date_position"
			class="form-select ib-preview-field @error('date_position') is-invalid @enderror">
			@foreach($catalog['date_positions'] as $key => $pos)
			<option value="{{ $key }}" @selected($config['date_position']===$key)>
				{{ $pos['label_ar'] }}</option>
			@endforeach
		</select>
		@error('date_position')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	</div>

	<div class="col-12">
		<hr class="my-1">
		<h6 class="text-muted">{{ __('admin.ib-honorific-section') }}</h6>
		<p class="small text-muted mb-2">{{ __('admin.ib-honorific-section-hint') }}</p>
		@include('admin.invitation-builder.partials.block-fields', [
			'blockKey' => 'hero_honorific',
			'blockData' => $config['block_data'] ?? [],
			'schemas' => $catalog['block_field_schemas'] ?? config('invitation_builder.block_field_schemas', []),
			'catalog' => $catalog,
		])
	</div>
</div>
