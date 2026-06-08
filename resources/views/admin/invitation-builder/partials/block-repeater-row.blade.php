@php
	use App\Services\Invitation\InvitationBuilderService;
	$builder = app(InvitationBuilderService::class);
@endphp
<div class="ib-repeater-row card card-body p-2 mb-2 bg-light">
	<div class="d-flex justify-content-between align-items-center mb-2">
		<span class="small text-muted">#{{ (int) $rowIndex + 1 }}</span>
		<button type="button" class="btn btn-sm btn-outline-danger py-0 ib-repeater-remove" title="{{ __('admin.ib-block-remove-row') }}">×</button>
	</div>
	<div class="row g-2">
		@foreach($fields as $rfKey => $rfDef)
		@php
			$rfType = $rfDef['type'] ?? 'text';
			$rfName = 'block_data['.$blockKey.']['.$repeaterKey.']['.$rowIndex.']['.$rfKey.']';
			$rfValue = $row[$rfKey] ?? '';
			$colClass = $builder->blockFieldColumnClass($rfType);
		@endphp
		<div class="{{ $colClass }} ib-block-field ib-block-field-{{ $rfType }}">
			@include('admin.invitation-builder.partials.block-field-input', [
				'name' => $rfName,
				'value' => $rfValue,
				'fieldDef' => $rfDef,
				'fieldKey' => $rfKey,
				'rowIndex' => $rowIndex,
				'inputId' => $blockKey.'_'.$repeaterKey.'_'.$rowIndex.'_'.$rfKey,
				'showLabel' => $rfType !== 'checkbox',
			])
		</div>
		@endforeach
	</div>
</div>
