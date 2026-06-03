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
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-opening-headline') }}</label>
		<textarea name="opening_headline" class="form-control ib-preview-field" rows="3" placeholder="{{ $invitation->event_name }}">{{ old('opening_headline', $config['opening_headline']) }}</textarea>
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-event-date') }}</label>
		<input type="date" name="event_date" class="form-control ib-preview-field mb-2" value="{{ old('event_date', $config['event_date'] ? \Illuminate\Support\Carbon::parse($config['event_date'])->format('Y-m-d') : '') }}">
		<label class="form-label">{{ __('admin.ib-event-time') }}</label>
		<input type="time" name="event_time" class="form-control ib-preview-field" value="{{ old('event_time', $config['event_time'] ? \Illuminate\Support\Carbon::parse($config['event_time'])->format('H:i') : '') }}">
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
		<input type="color" name="text_color_visible" class="form-control form-control-color w-100 ib-color-sync" data-target="text_color" value="{{ old('text_color', $config['text_color']) }}">
	</div>
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-date-position') }}</label>
		<select name="date_position" class="form-select ib-preview-field">
			@foreach($catalog['date_positions'] as $key => $pos)
			<option value="{{ $key }}" @selected($config['date_position'] === $key)>{{ $pos['label_ar'] }}</option>
			@endforeach
		</select>
	</div>
</div>
