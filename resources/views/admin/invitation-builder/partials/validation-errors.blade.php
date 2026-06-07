@php
	$ibErrorTabMap = [
		'envelope_color' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'envelope_shape' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'envelope_image_ref' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'envelope_initials' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'seal_style' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'seal_color' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'welcome_enabled' => ['tab' => 'ibTabEnvelope', 'label' => __('admin.ib-tab-envelope')],
		'event_category' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'theme_slug' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'theme_mode' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'background_media_url' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'video_background' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'primary_color' => ['tab' => 'ibTabThemes', 'label' => __('admin.ib-tab-themes')],
		'text_color' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'groom' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'bride' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'groom_father' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'bride_father' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'event_date' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'event_time' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'date_position' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'venue_name' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'venue_location' => ['tab' => 'ibTabDetails', 'label' => __('admin.ib-tab-details')],
		'blocks' => ['tab' => 'ibTabBlocks', 'label' => __('admin.ib-tab-blocks')],
		'blocks.*' => ['tab' => 'ibTabBlocks', 'label' => __('admin.ib-tab-blocks')],
	];
@endphp

<div class="alert alert-danger mb-3" id="ibValidationErrors" role="alert">
	<strong class="d-block mb-2">{{ __('admin.ib-validation-errors-title') }}</strong>
	<ul class="mb-0 ps-3">
		@foreach ($errors->getMessages() as $field => $messages)
			@foreach ($messages as $message)
				@php
					$mapKey = $field;
					if (! isset($ibErrorTabMap[$mapKey]) && str_contains($field, '.')) {
						$mapKey = explode('.', $field, 2)[0].'.*';
					}
					$tabInfo = $ibErrorTabMap[$mapKey] ?? $ibErrorTabMap[$field] ?? null;
				@endphp
				<li class="mb-1" @if($tabInfo) data-ib-error-tab="{{ $tabInfo['tab'] }}" @endif>
					@if($tabInfo)
						<span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1">{{ $tabInfo['label'] }}</span>
					@endif
					{{ $message }}
				</li>
			@endforeach
		@endforeach
	</ul>
</div>

<script>
(function () {
	var firstTab = document.querySelector('#ibValidationErrors [data-ib-error-tab]');
	if (!firstTab || !window.bootstrap || !bootstrap.Tab) {
		return;
	}
	var tabId = firstTab.getAttribute('data-ib-error-tab');
	var tabBtn = document.querySelector('[data-bs-target="#' + tabId + '"]');
	if (tabBtn) {
		bootstrap.Tab.getOrCreateInstance(tabBtn).show();
	}
	document.querySelectorAll('.is-invalid').forEach(function (el) {
		el.classList.remove('is-invalid');
	});
	{!! json_encode($errors->keys()) !!}
		.filter(function (key) { return key.indexOf('.') === -1; })
		.forEach(function (field) {
			var input = document.querySelector('[name="' + field + '"]');
			if (input) {
				input.classList.add('is-invalid');
			}
		});
})();
</script>
