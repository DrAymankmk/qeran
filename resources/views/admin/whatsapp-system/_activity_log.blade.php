<div class="card">
	<div class="card-body">
		<div class="d-flex align-items-center justify-content-between mb-3">
			<h5 class="card-title mb-0">{{ __('admin.whatsapp-activity-log-title') }}</h5>
			<button type="button" id="wa-logs-refresh" class="btn btn-sm btn-outline-secondary">
				<i class="mdi mdi-refresh"></i>
				{{ __('admin.whatsapp-activity-log-refresh') }}
			</button>
		</div>
		<p class="text-muted small mb-3">{{ __('admin.whatsapp-activity-log-description') }}</p>

		<div id="wa-activity-log-wrap">
			@include('admin.whatsapp-system._activity_log_rows', ['activityLogs' => $activityLogs ?? collect()])
		</div>
	</div>
</div>
