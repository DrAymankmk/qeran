@php
$activeBlocks = $config['blocks'] ?? [];
$inactiveBlocks = array_diff(array_keys($catalog['information_blocks']), $activeBlocks);
@endphp

<div class="ib-tab-hero mb-4">
	<h5 class="fw-bold mb-1">{{ __('admin.ib-tab-blocks-title') }}</h5>
	<p class="text-muted mb-2">{{ __('admin.ib-tab-blocks-subtitle') }}</p>
	<p class="small text-muted mb-0">{{ __('admin.ib-tab-blocks-desc') }}</p>
</div>

<ul class="list-unstyled small text-muted mb-4">
	<li>✓ {{ __('admin.ib-tab-blocks-bullet-1') }}</li>
	<li>✓ {{ __('admin.ib-tab-blocks-bullet-2') }}</li>
	<li>✓ {{ __('admin.ib-tab-blocks-bullet-3') }}</li>
</ul>

<!-- <div class="row g-3 mb-4">
	<div class="col-md-6">
		<label class="form-label">{{ __('admin.ib-block-accent') }}</label>
		<input type="color" name="block_accent_color" class="form-control form-control-color w-100 ib-preview-field" value="{{ old('block_accent_color', $config['block_accent_color']) }}">
	</div>
	<div class="col-md-6 d-flex align-items-end">
		<div class="form-check">
			<input class="form-check-input ib-preview-field" type="checkbox" name="block_floral_border" value="1" id="block_floral_border" @checked($config['block_floral_border'])>
			<label class="form-check-label" for="block_floral_border">{{ __('admin.ib-block-floral') }}</label>
		</div>
	</div>
</div> -->

<label class="form-label fw-semibold">{{ __('admin.ib-blocks-active') }}</label>
<p class="small text-muted">{{ __('admin.ib-blocks-drag-hint') }}</p>

<ul class="list-group mb-3" id="ibBlocksSortable">
	@foreach($activeBlocks as $blockKey)
	@if(isset($catalog['information_blocks'][$blockKey]))
	@php $block = $catalog['information_blocks'][$blockKey]; @endphp
	<li class="list-group-item ib-block-item d-flex align-items-center gap-2" draggable="true"
		data-block="{{ $blockKey }}">
		<span class="ib-drag-handle text-muted cursor-grab">⋮⋮</span>
		<span class="fs-5">{{ $block['icon'] }}</span>
		<div class="flex-grow-1">
			<strong class="small d-block">{{ $block['label_ar'] }}</strong>
			<span class="text-muted" style="font-size: 11px;">{{ $block['description_ar'] }}</span>
		</div>
		<input type="hidden" name="blocks[]" value="{{ $blockKey }}" class="ib-preview-field">
		<button type="button" class="btn btn-sm btn-outline-danger ib-block-remove" title="إزالة">×</button>
	</li>
	@endif
	@endforeach
</ul>

<label class="form-label fw-semibold">{{ __('admin.ib-blocks-add') }}</label>
<div class="row g-2" id="ibBlocksAvailable">
	@foreach($inactiveBlocks as $blockKey)
	@php $block = $catalog['information_blocks'][$blockKey]; @endphp
	<div class="col-md-6">
		<button type="button" class="btn btn-outline-secondary w-100 text-start ib-block-add"
			data-block="{{ $blockKey }}" data-icon="{{ $block['icon'] }}"
			data-label="{{ $block['label_ar'] }}" data-desc="{{ $block['description_ar'] }}">
			{{ $block['icon'] }} {{ $block['label_ar'] }}
		</button>
	</div>
	@endforeach
</div>

@if(count($inactiveBlocks) === 0)
<p class="text-muted small mt-2 mb-0">{{ __('admin.ib-blocks-all-added') }}</p>
@endif