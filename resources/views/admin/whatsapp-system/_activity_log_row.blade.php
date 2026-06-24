@php
	$logTime = $log->created_at?->timezone(config('app.display_timezone', 'Asia/Riyadh'));
@endphp
<tr>
	<td class="small text-nowrap">
		<div>{{ $logTime?->format('Y-m-d H:i:s') }}</div>
		<div class="text-muted">{{ $logTime?->diffForHumans() }}</div>
	</td>
	<td>
		<span class="badge bg-{{ $log->levelBadgeClass() }}">
			{{ __('admin.whatsapp-log-event-'.$log->event) }}
		</span>
	</td>
	<td class="small">{{ $log->message }}</td>
	<td class="small text-muted">
		@if($log->admin)
			{{ $log->admin->name }}
		@else
			{{ __('admin.whatsapp-activity-log-actor-system') }}
		@endif
	</td>
	<td>
		@if(!empty($log->context))
		<button type="button" class="btn btn-link btn-sm p-0 wa-log-details-btn"
			data-context="{{ rawurlencode(json_encode($log->context, JSON_UNESCAPED_UNICODE)) }}">
			{{ __('admin.whatsapp-activity-log-view') }}
		</button>
		@else
		<span class="text-muted">—</span>
		@endif
	</td>
	<td>
		<button type="button" class="btn btn-link btn-sm p-0 text-danger wa-log-delete-btn" data-id="{{ $log->id }}"
			title="{{ __('admin.whatsapp-activity-log-delete') }}" aria-label="{{ __('admin.whatsapp-activity-log-delete') }}">
			<i class="mdi mdi-delete-outline"></i>
		</button>
	</td>
</tr>
