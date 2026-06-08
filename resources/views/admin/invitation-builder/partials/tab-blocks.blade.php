@php
$activeBlocks = $config['blocks'] ?? [];
$inactiveBlocks = array_diff(array_keys($catalog['information_blocks']), $activeBlocks);
$blockData = $config['block_data'] ?? [];
$schemas = $catalog['block_field_schemas'] ?? [];
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

<label class="form-label fw-semibold">{{ __('admin.ib-blocks-active') }}</label>
<p class="small text-muted">{{ __('admin.ib-blocks-drag-hint') }} · {{ __('admin.ib-blocks-edit-hint') }}</p>

<ul class="list-group mb-3" id="ibBlocksSortable">
	@foreach($activeBlocks as $blockKey)
	@if(isset($catalog['information_blocks'][$blockKey]))
	@php $block = $catalog['information_blocks'][$blockKey]; @endphp
	<li class="list-group-item ib-block-item" draggable="true" data-block="{{ $blockKey }}">
		<div class="d-flex align-items-center gap-2">
			<span class="ib-drag-handle text-muted cursor-grab">⋮⋮</span>
			<span class="fs-5">{{ $block['icon'] }}</span>
			<div class="flex-grow-1">
				<strong class="small d-block">{{ $block['label_ar'] }}</strong>
				<span class="text-muted" style="font-size: 11px;">{{ $block['description_ar'] }}</span>
			</div>
			<input type="hidden" name="blocks[]" value="{{ $blockKey }}" class="ib-preview-field">
			@if(isset($schemas[$blockKey]))
			<button type="button" class="btn btn-sm btn-outline-secondary ib-block-toggle" title="{{ __('admin.ib-block-edit') }}">
				<i class="mdi mdi-pencil-outline"></i>
			</button>
			@endif
			<button type="button" class="btn btn-sm btn-outline-danger ib-block-remove" title="{{ __('admin.ib-block-remove') }}">×</button>
		</div>
		@if(isset($schemas[$blockKey]))
		<div class="ib-block-fields-wrap collapse">
			@include('admin.invitation-builder.partials.block-fields', [
				'blockKey' => $blockKey,
				'blockData' => $blockData,
				'schemas' => $schemas,
			])
		</div>
		@endif
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

@foreach($schemas as $tplBlockKey => $tplSchema)
<template id="ibBlockFieldsTpl_{{ $tplBlockKey }}">
	<div class="ib-block-fields-wrap collapse show">
		@include('admin.invitation-builder.partials.block-fields', [
			'blockKey' => $tplBlockKey,
			'blockData' => $blockData,
			'schemas' => $schemas,
		])
	</div>
</template>
@endforeach

<script type="application/json" id="ibBlockSchemasJson">@json($schemas)</script>
