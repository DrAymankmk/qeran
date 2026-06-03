<div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-envelope-title') }}</h5>
	<p class="text-muted mb-2">{{ __('admin.ib-tab-envelope-subtitle') }}</p>
	<p class="small text-muted mb-0">{{ __('admin.ib-tab-envelope-desc') }}</p>
</div>

<ul class="list-unstyled small text-muted mb-4">
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-3') }}</li>
</ul>

<label class="form-label fw-semibold">{{ __('admin.ib-envelope-color') }}</label>
<div class="d-flex flex-wrap gap-2 mb-4">
	@foreach($catalog['envelope_colors'] as $key => $color)
	<label class="ib-envelope-swatch @if($config['envelope_color'] === $key) is-active @endif" title="{{ $color['label_ar'] }}">
		<input type="radio" name="envelope_color" value="{{ $key }}" class="d-none ib-preview-field" @checked($config['envelope_color'] === $key)>
		<span style="background: {{ $color['swatch'] }};"></span>
		<small>{{ $color['label_ar'] }}</small>
	</label>
	@endforeach
</div>

<label class="form-label fw-semibold">{{ __('admin.ib-seal-style') }}</label>
<div class="row g-2 mb-4">
	@foreach($catalog['seal_styles'] as $key => $seal)
	<div class="col-6">
		<label class="ib-seal-option card h-100 mb-0 @if($config['seal_style'] === $key) border-primary @endif">
			<div class="card-body text-center py-3">
				<input type="radio" name="seal_style" value="{{ $key }}" class="d-none ib-preview-field" @checked($config['seal_style'] === $key)>
				<div style="font-size: 2rem;">{{ $seal['icon'] }}</div>
				<strong class="small d-block mt-1">{{ $seal['label_ar'] }}</strong>
			</div>
		</label>
	</div>
	@endforeach
</div>

<label class="form-label fw-semibold" for="envelope_initials">{{ __('admin.ib-envelope-initials') }}</label>
<input type="text" name="envelope_initials" id="envelope_initials" class="form-control ib-preview-field mb-2"
	maxlength="8" placeholder="A & B"
	value="{{ old('envelope_initials', $config['envelope_initials']) }}">
<small class="text-muted">{{ __('admin.ib-envelope-initials-hint') }}</small>

<div class="form-check mt-3">
	<input class="form-check-input ib-preview-field" type="checkbox" name="welcome_enabled" value="1" id="welcome_enabled" @checked($config['welcome_enabled'])>
	<label class="form-check-label" for="welcome_enabled">{{ __('admin.invitation-builder-welcome-screen') }}</label>
</div>
