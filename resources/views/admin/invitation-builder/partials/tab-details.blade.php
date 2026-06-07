<div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-details-title') }}</h5>
	<p class="text-muted mb-2">{{ __('admin.ib-tab-details-subtitle') }}</p>
	<p class="small text-muted mb-0">{{ __('admin.ib-tab-details-desc') }}</p>
</div>

<ul class="list-unstyled small text-muted mb-4">
	<li>✓ {{ __('admin.ib-tab-details-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-details-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-details-bullet-3') }}</li>
</ul>

<div class="row g-3">
	<div class="col-12">
		<h6 class="text-muted mb-0">{{ __('admin.ib-couple-section') }}</h6>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.bride') }}</label>
		<input type="text" name="bride" class="form-control ib-preview-field" maxlength="50"
			value="{{ old('bride', $invitation->bride) }}" placeholder="{{ __('admin.bride') }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.groom') }}</label>
		<input type="text" name="groom" class="form-control ib-preview-field" maxlength="50"
			value="{{ old('groom', $invitation->groom) }}" placeholder="{{ __('admin.groom') }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.bride_father') }}</label>
		<input type="text" name="bride_father" class="form-control ib-preview-field" maxlength="50"
			value="{{ old('bride_father', $invitation->bride_father) }}" placeholder="{{ __('admin.bride_father') }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.groom_father') }}</label>
		<input type="text" name="groom_father" class="form-control ib-preview-field" maxlength="50"
			value="{{ old('groom_father', $invitation->groom_father) }}" placeholder="{{ __('admin.groom_father') }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-event-date') }}</label>
		<input type="date" name="event_date" class="form-control ib-preview-field mb-2" value="{{ old('event_date', $config['event_date'] ? \Illuminate\Support\Carbon::parse($config['event_date'])->format('Y-m-d') : '') }}">
		<label class="form-label">{{ __('admin.ib-event-time') }}</label>
		<input type="time" name="event_time" class="form-control ib-preview-field" value="{{ old('event_time', $config['event_time'] ? \Illuminate\Support\Carbon::parse($config['event_time'])->format('H:i') : '') }}">
	</div>

	<div class="col-12"><hr class="my-1"><h6 class="text-muted">{{ __('admin.ib-details-cards-section') }}</h6></div>

	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-details-section-label') }}</label>
		<input type="text" name="details_section_label" class="form-control ib-preview-field" value="{{ old('details_section_label', $config['details_section_label']) }}" placeholder="جميع التفاصيل">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-details-section-title') }}</label>
		<input type="text" name="details_section_title" class="form-control ib-preview-field" value="{{ old('details_section_title', $config['details_section_title']) }}" placeholder="{{ $invitation->event_name }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-venue-name') }}</label>
		<input type="text" name="venue_name" class="form-control ib-preview-field" value="{{ old('venue_name', $config['venue_name']) }}" placeholder="{{ $invitation->event_name }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-venue-location') }}</label>
		<input type="text" name="venue_location" class="form-control ib-preview-field" value="{{ old('venue_location', $config['venue_location']) }}" placeholder="{{ $invitation->address }}">
		@if($invitation->address || ($invitation->latitude && $invitation->longitude))
		<small class="text-muted d-block mt-1">
			{{ __('admin.ib-venue-location-hint') }}
			@if($invitation->address)<br>{{ __('admin.ib-invitation-address') }}: {{ $invitation->address }}@endif
			@if($invitation->latitude && $invitation->longitude)<br>{{ __('admin.ib-invitation-coords') }}: {{ $invitation->latitude }}, {{ $invitation->longitude }}@endif
		</small>
		@endif
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-ceremony-note') }}</label>
		<input type="text" name="ceremony_note" class="form-control ib-preview-field" value="{{ old('ceremony_note', $config['ceremony_note']) }}" placeholder="{{ __('admin.ib-ceremony-note-placeholder') }}">
	</div>
	<div class="col-md-3">
		<label class="form-label">{{ __('admin.ib-reception-time') }}</label>
		<input type="time" name="reception_time" class="form-control ib-preview-field" value="{{ old('reception_time', $config['reception_time'] ? \Illuminate\Support\Carbon::parse($config['reception_time'])->format('H:i') : '') }}">
	</div>
	<div class="col-md-3">
		<label class="form-label">{{ __('admin.ib-reception-note') }}</label>
		<input type="text" name="reception_note" class="form-control ib-preview-field" value="{{ old('reception_note', $config['reception_note']) }}">
	</div>

	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-headline-font') }}</label>
		<select name="headline_font" class="form-select ib-preview-field">
			@foreach($catalog['fonts'] as $key => $label)
			<option value="{{ $key }}" @selected(($config['headline_font'] ?? '') === $key)>{{ $label }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-body-font') }}</label>
		<select name="font_family" class="form-select ib-preview-field">
			@foreach($catalog['fonts'] as $key => $label)
			<option value="{{ $key }}" @selected($config['font_family'] === $key)>{{ $label }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.invitation-builder-text-color') }}</label>
		<input type="color" name="text_color_visible" class="form-control form-control-color w-100 ib-color-sync @error('text_color') is-invalid @enderror" data-target="text_color" value="{{ old('text_color', $config['text_color']) }}">
		@error('text_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-date-position') }}</label>
		<select name="date_position" class="form-select ib-preview-field @error('date_position') is-invalid @enderror">
			@foreach($catalog['date_positions'] as $key => $pos)
			<option value="{{ $key }}" @selected($config['date_position'] === $key)>{{ $pos['label_ar'] }}</option>
			@endforeach
		</select>
		@error('date_position')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
	</div>
</div>
