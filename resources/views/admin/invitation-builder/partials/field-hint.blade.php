@php
	$hintDef = config('invitation_builder.field_hints.'.$field, []);
	$hintKey = $hintDef['hint'] ?? null;
	$hintFormat = $hintDef['format'] ?? null;
	$hintMin = $hintDef['min'] ?? null;
	$hintMax = $hintDef['max'] ?? null;

	$hintValues = '';
	if (! empty($hintDef['options'])) {
		$optionKeys = array_keys(config('invitation_builder.'.$hintDef['options'], []));
		$shown = array_slice($optionKeys, 0, 8);
		$hintValues = implode(' · ', $shown);
		if (count($optionKeys) > count($shown)) {
			$hintValues .= ' … (+'.(count($optionKeys) - count($shown)).')';
		}
	}

	$hintLength = '';
	if ($hintMin !== null && $hintMax !== null) {
		$hintLength = __('admin.ib-input-range-hint', ['min' => $hintMin, 'max' => $hintMax]);
	} elseif ($hintMax !== null) {
		$hintLength = __('admin.ib-input-max-hint', ['max' => $hintMax]);
	} elseif ($hintMin !== null) {
		$hintLength = __('admin.ib-input-min-hint', ['min' => $hintMin]);
	}
@endphp

@if($hintKey || $hintFormat || $hintValues !== '' || $hintLength !== '')
<small class="text-muted d-block mt-1 mb-2">
	@if($hintKey)
	<span class="d-block">{{ __($hintKey) }}</span>
	@endif
	@if($hintFormat)
	<span class="d-block">{{ __('admin.ib-hint-format', ['format' => $hintFormat]) }}</span>
	@endif
	@if($hintValues !== '')
	<span class="d-block">{{ __('admin.ib-hint-allowed-values', ['values' => $hintValues]) }}</span>
	@endif
	@if($hintLength !== '')
	<span class="d-block">{{ $hintLength }}</span>
	@endif
</small>
@endif

@error($field)
<div class="invalid-feedback d-block mb-2">{{ $message }}</div>
@enderror
