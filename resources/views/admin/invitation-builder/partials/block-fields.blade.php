@php
	$blockValues = $blockData[$blockKey] ?? [];
	$schema = $schemas[$blockKey] ?? null;
@endphp
@if($schema)
<div class="ib-block-fields border-top mt-2 pt-3">
	@php $styleFields = $catalog['block_style_fields'] ?? config('invitation_builder.block_style_fields', []); @endphp
	@if(count($styleFields) > 0 && $blockKey !== 'background_music')
	<div class="ib-block-style-fields mb-3 pb-3 border-bottom">
		<p class="small fw-semibold text-muted mb-2">{{ __('admin.ib-block-style-section') }}</p>
		@php
			$styleGroups = [];
			foreach ($styleFields as $styleKey => $styleDef) {
				$groupKey = $styleDef['group'] ?? 'general';
				if (! isset($styleGroups[$groupKey])) {
					$styleGroups[$groupKey] = [
						'label' => $styleDef['group_label_ar'] ?? null,
						'fields' => [],
					];
				}
				$styleGroups[$groupKey]['fields'][$styleKey] = $styleDef;
			}
		@endphp
		@foreach($styleGroups as $group)
			@if(!empty($group['label']))
			<p class="small fw-semibold text-secondary mb-2 mt-1">{{ $group['label'] }}</p>
			@endif
			<div class="row g-2 mb-2">
				@foreach($group['fields'] as $styleKey => $styleDef)
				@php
					$styleName = 'block_data['.$blockKey.']['.$styleKey.']';
					$styleValue = old('block_data.'.$blockKey.'.'.$styleKey, $blockValues[$styleKey] ?? ($styleDef['default'] ?? ''));
					$styleCol = app(\App\Services\Invitation\InvitationBuilderService::class)->blockFieldColumnClass($styleDef['type'] ?? 'text');
				@endphp
				<div class="{{ $styleCol }} ib-block-field ib-block-field-{{ $styleDef['type'] ?? 'text' }}">
					@include('admin.invitation-builder.partials.block-field-input', [
						'name' => $styleName,
						'value' => $styleValue,
						'fieldDef' => array_merge($styleDef, [
							'fonts' => $catalog['fonts'] ?? config('invitation_builder.fonts', []),
							'weights' => $catalog['font_weights'] ?? config('invitation_builder.font_weights', []),
						]),
						'fieldKey' => $styleKey,
						'inputId' => 'ib_bs_'.$blockKey.'_'.$styleKey,
					])
				</div>
				@endforeach
			</div>
		@endforeach
	</div>
	@endif

	@foreach($schema['fields'] ?? [] as $fieldKey => $fieldDef)
		@php
			$fieldName = 'block_data['.$blockKey.']['.$fieldKey.']';
			$fieldValue = old('block_data.'.$blockKey.'.'.$fieldKey, $blockValues[$fieldKey] ?? ($fieldDef['default'] ?? ''));
		@endphp
		<div class="mb-2 ib-block-field ib-block-field-{{ $fieldDef['type'] ?? 'text' }}">
			@include('admin.invitation-builder.partials.block-field-input', [
				'name' => $fieldName,
				'value' => $fieldValue,
				'fieldDef' => $fieldDef,
				'fieldKey' => $fieldKey,
				'inputId' => 'ib_bf_'.$blockKey.'_'.$fieldKey,
			])
		</div>
	@endforeach

	@foreach($schema['groups'] ?? [] as $groupDef)
		@if(!empty($groupDef['label_ar']))
		<p class="small fw-semibold text-muted mb-2 mt-1">{{ $groupDef['label_ar'] }}</p>
		@endif
		<div class="row g-2 mb-2">
			@foreach($groupDef['fields'] ?? [] as $fieldKey => $fieldDef)
			@php
				$fieldName = 'block_data['.$blockKey.']['.$fieldKey.']';
				$fieldValue = old('block_data.'.$blockKey.'.'.$fieldKey, $blockValues[$fieldKey] ?? ($fieldDef['default'] ?? ''));
				$fieldCol = app(\App\Services\Invitation\InvitationBuilderService::class)->blockFieldColumnClass($fieldDef['type'] ?? 'text');
			@endphp
			<div class="{{ $fieldCol }} ib-block-field ib-block-field-{{ $fieldDef['type'] ?? 'text' }}">
				@include('admin.invitation-builder.partials.block-field-input', [
					'name' => $fieldName,
					'value' => $fieldValue,
					'fieldDef' => $fieldDef,
					'fieldKey' => $fieldKey,
					'inputId' => 'ib_bf_'.$blockKey.'_'.$fieldKey,
				])
			</div>
			@endforeach
		</div>
	@endforeach

	@foreach($schema['repeaters'] ?? [] as $repeaterKey => $repeaterDef)
		@php
			$rows = old('block_data.'.$blockKey.'.'.$repeaterKey, $blockValues[$repeaterKey] ?? []);
			if (! is_array($rows)) {
				$rows = [];
			}
			$maxRows = (int) ($repeaterDef['max'] ?? 8);
		@endphp
		<div class="ib-block-repeater mb-2" data-block="{{ $blockKey }}" data-repeater="{{ $repeaterKey }}" data-max="{{ $maxRows }}">
			<div class="d-flex justify-content-between align-items-center mb-1">
				<label class="form-label small mb-0">{{ $repeaterDef['label_ar'] ?? $repeaterKey }}</label>
				<button type="button" class="btn btn-sm btn-outline-primary ib-repeater-add">{{ __('admin.ib-block-add-row') }}</button>
			</div>
			<div class="ib-repeater-rows">
				@foreach($rows as $rowIndex => $row)
				@include('admin.invitation-builder.partials.block-repeater-row', [
					'blockKey' => $blockKey,
					'repeaterKey' => $repeaterKey,
					'rowIndex' => $rowIndex,
					'row' => is_array($row) ? $row : [],
					'fields' => $repeaterDef['fields'] ?? [],
				])
				@endforeach
			</div>
		</div>
	@endforeach
</div>
@endif
