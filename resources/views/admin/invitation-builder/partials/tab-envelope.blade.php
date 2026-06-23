@php
$selectedEnvelopeRef = old('envelope_image_ref', $envelopeImageRef ?? ($config['envelope_image_ref'] ?? ''));
$stockChoices = collect($envelopeImageChoices ?? [])->where('group', 'stock');
$invitationChoices = collect($envelopeImageChoices ?? [])->where('group', 'invitation');
@endphp

<!-- <div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-envelope-title') }}</h5>
	<p class="text-muted mb-2">{{ __('admin.ib-tab-envelope-subtitle') }}</p>
	<p class="small text-muted mb-0">{{ __('admin.ib-tab-envelope-desc') }}</p>
</div>

<ul class="list-unstyled small text-muted mb-4">
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-envelope-bullet-3') }}</li>
</ul> -->

@php
$activeEnvelopeShape = app(\App\Services\Invitation\InvitationBuilderService::class)
->normalizeEnvelopeShape($config['envelope_shape'] ?? null);
@endphp

<label class="form-label fw-semibold">{{ __('admin.ib-envelope-shape') }}</label>
<p class="small text-muted mb-2">{{ __('admin.ib-envelope-shape-hint') }}</p>
@error('envelope_shape')<div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>@enderror
<div class="row g-2 mb-4" id="ibEnvelopeShapeGrid">
	@foreach($catalog['envelope_shapes'] as $shapeKey => $shape)
	<div class="col-4 col-md-4">
		<label class="ib-envelope-shape-card @if($activeEnvelopeShape === $shapeKey) is-active @endif">
			<input type="radio" name="envelope_shape" value="{{ $shapeKey }}"
				class="d-none ib-preview-field" @checked($activeEnvelopeShape===$shapeKey)>
			<div class="ib-env-shape-mini ib-env-shape-mini-{{ $shapeKey }}" aria-hidden="true">
				<span class="ib-mini-flap"></span>
				<span class="ib-mini-body"></span>
				<span class="ib-mini-liner"></span>
			</div>
			<!-- <small class="d-block mt-1 text-center">{{ $shape['label_ar'] }}</small> -->
		</label>
	</div>
	@endforeach
</div>

<label class="form-label fw-semibold">{{ __('admin.ib-envelope-image') }}</label>
<p class="small text-muted mb-2">{{ __('admin.ib-envelope-image-hint') }}</p>
<input type="hidden" name="envelope_image_ref" id="envelope_image_ref" value="{{ $selectedEnvelopeRef }}"
	class="ib-preview-field">

<div class="d-flex flex-wrap gap-2 mb-3" id="ibEnvelopeImagePicker">
	<button type="button" class="ib-env-image-card @if($selectedEnvelopeRef === '') is-active @endif"
		data-envelope-ref="" title="{{ __('admin.ib-envelope-image-none') }}">
		<span class="ib-env-image-none">✕</span>
		<small>{{ __('admin.ib-envelope-image-none') }}</small>
	</button>

	@if($stockChoices->isNotEmpty())
	<div class="w-100 small text-muted fw-semibold mt-1">{{ __('admin.ib-envelope-images-stock') }}</div>
	@foreach($stockChoices as $choice)
	<button type="button" class="ib-env-image-card @if($selectedEnvelopeRef === $choice['id']) is-active @endif"
		data-envelope-ref="{{ $choice['id'] }}" data-envelope-url="{{ $choice['url'] }}"
		title="{{ $choice['label'] }}">
		<img src="{{ $choice['url'] }}" alt="{{ $choice['label'] }}" loading="lazy">
		<!-- <small>{{ $choice['label'] }}</small> -->
	</button>
	@endforeach
	@endif

	@if($invitationChoices->isNotEmpty())
	<div class="w-100 small text-muted fw-semibold mt-2">{{ __('admin.ib-envelope-images-invitation') }}</div>
	@foreach($invitationChoices as $choice)
	<button type="button" class="ib-env-image-card @if($selectedEnvelopeRef === $choice['id']) is-active @endif"
		data-envelope-ref="{{ $choice['id'] }}" data-envelope-url="{{ $choice['url'] }}"
		title="{{ $choice['label'] }}">
		<img src="{{ $choice['url'] }}" alt="{{ $choice['label'] }}" loading="lazy">
		<small>{{ Str::limit($choice['label'], 22) }}</small>
	</button>
	@endforeach
	@elseif($stockChoices->isEmpty())
	<p class="small text-warning mb-0">{{ __('admin.ib-envelope-images-empty') }}</p>
	@endif
</div>

<label class="form-label fw-semibold">{{ __('admin.ib-envelope-color') }}</label>
<small class="text-muted d-block mb-2">{{ __('admin.ib-envelope-color-hint') }}</small>
@error('envelope_color')<div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>@enderror
<div class="d-flex flex-wrap gap-2 mb-4">
	@foreach($catalog['envelope_colors'] as $key => $color)
	<label class="ib-envelope-swatch @if($config['envelope_color'] === $key) is-active @endif"
		title="{{ $color['label_ar'] }}">
		<input type="radio" name="envelope_color" value="{{ $key }}" class="d-none ib-preview-field"
			@checked($config['envelope_color']===$key)>
		<span style="background: {{ $color['swatch'] }};"></span>
		<!-- <small>{{ $color['label_ar'] }}</small> -->
	</label>
	@endforeach
</div>

@php
$resolvedSealColor = \App\Services\Invitation\WeddingInvitationPresenter::resolveSealColor(
$config['seal_style'] ?? 'wax_classic',
$config['seal_color'] ?? null
);
@endphp

<label class="form-label fw-semibold">{{ __('admin.ib-seal-style') }}</label>
<p class="small text-muted mb-2">{{ __('admin.ib-seal-style-hint') }}</p>

<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
	<label class="small text-muted mb-0" for="seal_color_picker">{{ __('admin.ib-seal-color') }}</label>
	<input type="color" id="seal_color_picker" class="form-control form-control-color ib-seal-color-picker"
		value="{{ $resolvedSealColor }}" title="{{ __('admin.ib-seal-color') }}">
	<input type="hidden" name="seal_color" id="seal_color" value="{{ $resolvedSealColor }}"
		class="ib-preview-field">
	@foreach(config('invitation_builder.seal_palette_colors', []) as $palKey => $palHex)
	<button type="button"
		class="ib-seal-color-swatch @if(strtolower($resolvedSealColor) === strtolower($palHex)) is-active @endif"
		data-seal-color="{{ $palHex }}" title="{{ $palKey }}" style="background: {{ $palHex }};"></button>
	@endforeach
</div>

@error('seal_style')<div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>@enderror
@error('seal_color')<div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>@enderror
<div class="row g-2 mb-4" id="ibSealStyleGrid">
	@foreach($catalog['seal_styles'] as $key => $seal)
	@php
	$sealDefaultColor = \App\Services\Invitation\WeddingInvitationPresenter::defaultSealColorForStyle($key);
	$sealVars = \App\Services\Invitation\WeddingInvitationPresenter::sealViewVars($key, $sealDefaultColor);
	@endphp
	<div class="col-6 col-md-4">
		<label class="ib-seal-option card h-100 mb-0 @if($config['seal_style'] === $key) border-primary @endif"
			data-seal-color="{{ $sealDefaultColor }}">
			<div class="card-body text-center py-2 px-2">
				<input type="radio" name="seal_style" value="{{ $key }}"
					class="d-none ib-preview-field"
					@checked($config['seal_style']===$key)>
				<div class="ib-seal-preview-wrap">
					<div class="wi-env-seal ib-seal-mini has-seal-custom-color wi-seal-shape-{{ $sealVars['wiSealShape'] }} wi-seal-pal-{{ $sealVars['wiSealPalette'] }} @if($sealVars['wiSealRing']) has-seal-ring @endif @if($sealVars['wiSealDrip']) has-seal-drip @endif"
						@if(!empty($sealVars['wiSealInlineStyle']))
						style="{{ $sealVars['wiSealInlineStyle'] }}" @endif>
						<span class="wi-seal-ring" aria-hidden="true"></span>
						<span class="wi-seal-initials">AB</span>
					</div>
				</div>
				<span class="ib-seal-color-dot"
					style="background: {{ $sealDefaultColor }};"></span>
				<strong class="small d-block mt-1">{{ $seal['label_ar'] }}</strong>
			</div>
		</label>
	</div>
	@endforeach
</div>

<label class="form-label fw-semibold" for="envelope_initials">{{ __('admin.ib-envelope-initials') }}</label>
<input type="text" name="envelope_initials" id="envelope_initials" class="form-control ib-preview-field mb-2"
	maxlength="8" placeholder="A & B" value="{{ old('envelope_initials', $config['envelope_initials']) }}">
<small class="text-muted">{{ __('admin.ib-envelope-initials-hint') }}</small>

<div class="form-check mt-3">
	<input class="form-check-input ib-preview-field" type="checkbox" name="welcome_enabled" value="1"
		id="welcome_enabled" @checked($config['welcome_enabled'])>
	<!-- <label class="form-check-label" for="welcome_enabled">{{ __('admin.invitation-builder-welcome-screen') }}</label> -->
</div>
