@php
	use App\Services\Invitation\InvitationBuilderService;

	$builder = app(InvitationBuilderService::class);
	$type = $fieldDef['type'] ?? 'text';
	$label = $fieldDef['label_ar'] ?? ($fieldKey ?? '');
	$maxlength = (int) ($fieldDef['max'] ?? 500);
	$placeholder = $fieldDef['placeholder'] ?? '';
	$inputId = $inputId ?? ('ib_bf_'.substr(md5($name.($rowIndex ?? '0')), 0, 10));
	$displayValue = $builder->formatBlockFieldForInput($type, $value ?? '');
	$inputClass = 'ib-preview-field';
	$step = $fieldDef['step'] ?? null;
@endphp

@if($type === 'checkbox')
<div class="form-check mt-1">
	<input type="hidden" name="{{ $name }}" value="0">
	<input type="checkbox" class="form-check-input {{ $inputClass }}" name="{{ $name }}" value="1" id="{{ $inputId }}"
		@checked(filter_var($value ?? false, FILTER_VALIDATE_BOOLEAN))>
	<label class="form-check-label small" for="{{ $inputId }}">{{ $label }}</label>
</div>
@else
@if($showLabel ?? true)
<label class="form-label small mb-1" for="{{ $inputId }}">{{ $label }}</label>
@endif
@switch($type)
	@case('textarea')
	<textarea name="{{ $name }}" id="{{ $inputId }}" rows="{{ $fieldDef['rows'] ?? 2 }}"
		class="form-control form-control-sm {{ $inputClass }}" maxlength="{{ $maxlength }}"
		placeholder="{{ $placeholder }}">{{ $displayValue }}</textarea>
	@break
	@case('color')
	<input type="color" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-color w-100 {{ $inputClass }}" value="{{ $displayValue ?: '#c9a962' }}">
	@break
	@case('optional_color')
	<div class="d-flex gap-2 align-items-center">
		<input type="text" name="{{ $name }}" id="{{ $inputId }}"
			class="form-control form-control-sm {{ $inputClass }} ib-optional-color-text"
			value="{{ $displayValue }}" maxlength="7"
			placeholder="{{ __('admin.ib-block-style-inherit') }}">
		<input type="color" class="form-control form-control-color ib-optional-color-picker {{ $inputClass }}"
			data-target="{{ $inputId }}" value="{{ $displayValue ?: '#faf7f2' }}"
			style="width:48px;min-width:48px;flex-shrink:0;">
		<button type="button" class="btn btn-sm btn-outline-secondary py-0 ib-optional-color-clear"
			data-target="{{ $inputId }}" title="{{ __('admin.ib-block-style-inherit') }}">×</button>
	</div>
	@break
	@case('font')
	<select name="{{ $name }}" id="{{ $inputId }}" class="form-select form-select-sm {{ $inputClass }}">
		<option value="">{{ __('admin.ib-block-style-inherit') }}</option>
		@foreach($fieldDef['fonts'] ?? config('invitation_builder.fonts', []) as $fontKey => $fontLabel)
		<option value="{{ $fontKey }}" @selected((string) ($value ?? '') === (string) $fontKey)>{{ $fontLabel }}</option>
		@endforeach
	</select>
	@break
	@case('font_size')
	<div class="input-group input-group-sm">
		<input type="number" name="{{ $name }}" id="{{ $inputId }}"
			class="form-control {{ $inputClass }}" value="{{ $displayValue }}"
			min="{{ $fieldDef['min'] ?? 8 }}" max="{{ $fieldDef['max'] ?? 96 }}" step="1"
			placeholder="{{ __('admin.ib-block-style-inherit') }}">
		<span class="input-group-text">px</span>
	</div>
	@break
	@case('font_weight')
	<select name="{{ $name }}" id="{{ $inputId }}" class="form-select form-select-sm {{ $inputClass }}">
		<option value="">{{ __('admin.ib-block-style-inherit') }}</option>
		@foreach($fieldDef['weights'] ?? config('invitation_builder.font_weights', []) as $weightKey => $weightLabel)
		<option value="{{ $weightKey }}" @selected((string) ($value ?? '') === (string) $weightKey)>{{ $weightLabel }}</option>
		@endforeach
	</select>
	@break
	@case('select')
	@php
		$selectOptions = $builder->resolveSelectOptions($fieldDef);
		$selectLocale = app()->getLocale() === 'en' ? 'label_en' : 'label_ar';
	@endphp
	<select name="{{ $name }}" id="{{ $inputId }}" class="form-select form-select-sm {{ $inputClass }}">
		@foreach($selectOptions as $optionKey => $optionDef)
		@php
			$optionLabel = is_array($optionDef)
				? trim(($optionDef['glyph'] ?? '').' '.($optionDef[$selectLocale] ?? $optionDef['label_ar'] ?? $optionKey))
				: (string) $optionDef;
		@endphp
		<option value="{{ $optionKey }}" @selected((string) ($value ?? ($fieldDef['default'] ?? '')) === (string) $optionKey)>{{ $optionLabel }}</option>
		@endforeach
	</select>
	@break
	@case('number')
	<input type="number" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-sm {{ $inputClass }}" value="{{ $displayValue }}"
		@if($step !== null) step="{{ $step }}" @endif
		@if(isset($fieldDef['min'])) min="{{ $fieldDef['min'] }}" @endif
		@if(isset($fieldDef['max'])) max="{{ $fieldDef['max'] }}" @endif
		placeholder="{{ $placeholder }}">
	@break
	@case('date')
	<input type="date" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-sm {{ $inputClass }}" value="{{ $displayValue }}"
		placeholder="{{ $placeholder }}">
	@break
	@case('time')
	<input type="time" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-sm {{ $inputClass }}" value="{{ $displayValue }}"
		placeholder="{{ $placeholder }}">
	@break
	@case('datetime-local')
	<input type="datetime-local" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-sm {{ $inputClass }}" value="{{ $displayValue }}"
		placeholder="{{ $placeholder }}">
	@break
	@case('audio_upload')
	<div class="ib-audio-upload" data-ib-audio-upload>
		<input type="hidden" name="{{ $name }}" id="{{ $inputId }}"
			class="{{ $inputClass }} ib-audio-upload-url" value="{{ $displayValue }}">
		@if($displayValue !== '')
		<audio class="ib-audio-upload-player w-100 mb-2" controls preload="metadata" src="{{ $displayValue }}"></audio>
		@else
		<div class="ib-audio-upload-empty text-muted small mb-2">{{ __('admin.ib-block-audio-empty') }}</div>
		@endif
		<input type="file" class="form-control form-control-sm ib-audio-upload-file"
			accept="audio/mpeg,audio/mp3,audio/ogg,audio/wav,audio/webm,audio/mp4,.mp3,.ogg,.wav,.m4a,.webm">
		<div class="d-flex gap-1 mt-1 flex-wrap">
			<button type="button" class="btn btn-sm btn-outline-primary ib-audio-upload-btn">
				<i class="mdi mdi-upload"></i> {{ __('admin.ib-block-audio-upload') }}
			</button>
			<button type="button" class="btn btn-sm btn-outline-secondary ib-audio-upload-clear"
				@if($displayValue === '') disabled @endif title="{{ __('admin.ib-block-audio-clear') }}">×</button>
		</div>
		<small class="text-muted d-block ib-audio-upload-status"></small>
	</div>
	@break
	@case('icon_upload')
	<div class="ib-icon-upload" data-ib-icon-upload>
		<input type="hidden" name="{{ $name }}" id="{{ $inputId }}"
			class="{{ $inputClass }} ib-icon-upload-url" value="{{ $displayValue }}">
		<div class="d-flex gap-2 align-items-start">
			<div class="ib-icon-upload-preview border rounded bg-white d-flex align-items-center justify-content-center flex-shrink-0">
				@if($displayValue !== '')
				<img src="{{ $displayValue }}" alt="" class="ib-icon-upload-img">
				@else
				<span class="ib-icon-upload-placeholder text-muted small">—</span>
				@endif
			</div>
			<div class="flex-grow-1 min-w-0">
				<input type="file" class="form-control form-control-sm ib-icon-upload-file"
					accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml,.svg">
				<div class="d-flex gap-1 mt-1 flex-wrap">
					<button type="button" class="btn btn-sm btn-outline-primary ib-icon-upload-btn">
						<i class="mdi mdi-upload"></i> {{ __('admin.ib-block-icon-upload') }}
					</button>
					<button type="button" class="btn btn-sm btn-outline-secondary ib-icon-upload-clear"
						@if($displayValue === '') disabled @endif title="{{ __('admin.ib-block-icon-clear') }}">×</button>
				</div>
				<small class="text-muted d-block ib-icon-upload-status"></small>
			</div>
		</div>
	</div>
	@break
	@default
	@php $htmlType = match ($type) {
		'url' => 'url',
		'email' => 'email',
		'tel' => 'tel',
		default => 'text',
	}; @endphp
	<input type="{{ $htmlType }}" name="{{ $name }}" id="{{ $inputId }}"
		class="form-control form-control-sm {{ $inputClass }}" value="{{ $displayValue }}"
		maxlength="{{ $maxlength }}" placeholder="{{ $placeholder }}">
@endswitch
@endif
