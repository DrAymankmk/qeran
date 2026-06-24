@php
	$isPaginator = ($activityLogs ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator;
	$logTotal = $isPaginator ? $activityLogs->total() : ($activityLogs ?? collect())->count();
@endphp

@if($logTotal === 0)
<p class="text-muted mb-0" id="wa-activity-log-empty">{{ __('admin.whatsapp-activity-log-empty') }}</p>
@else
<div class="table-responsive">
	<table class="table table-sm table-hover align-middle mb-0" id="wa-activity-log-table">
		<thead class="table-light">
			<tr>
				<th style="width: 140px;">{{ __('admin.whatsapp-activity-log-col-time') }}</th>
				<th style="width: 120px;">{{ __('admin.whatsapp-activity-log-col-event') }}</th>
				<th>{{ __('admin.whatsapp-activity-log-col-message') }}</th>
				<th style="width: 120px;">{{ __('admin.whatsapp-activity-log-col-actor') }}</th>
				<th style="width: 90px;">{{ __('admin.whatsapp-activity-log-col-details') }}</th>
				<th style="width: 70px;">{{ __('admin.whatsapp-activity-log-col-actions') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($activityLogs as $log)
			@include('admin.whatsapp-system._activity_log_row', ['log' => $log])
			@endforeach
		</tbody>
	</table>
</div>

@if($isPaginator)
@include('admin.whatsapp-system._activity_log_pagination', ['activityLogs' => $activityLogs])
@endif
@endif
