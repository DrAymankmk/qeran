@php
	/** @var \Illuminate\Pagination\LengthAwarePaginator $activityLogs */
@endphp
<nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3" id="wa-activity-log-pagination" aria-label="{{ __('admin.whatsapp-activity-log-title') }}">
	<p class="small text-muted mb-0" id="wa-activity-log-summary">
		{{ __('admin.whatsapp-activity-log-showing', [
			'from' => $activityLogs->firstItem() ?? 0,
			'to' => $activityLogs->lastItem() ?? 0,
			'total' => $activityLogs->total(),
		]) }}
	</p>

	@if($activityLogs->lastPage() > 1)
	<ul class="pagination pagination-sm mb-0">
		<li class="page-item {{ $activityLogs->onFirstPage() ? 'disabled' : '' }}">
			<button type="button" class="page-link wa-log-page-btn" data-page="{{ $activityLogs->currentPage() - 1 }}" @disabled($activityLogs->onFirstPage()) aria-label="Previous">&laquo;</button>
		</li>
		@for($page = 1; $page <= $activityLogs->lastPage(); $page++)
		<li class="page-item {{ $page === $activityLogs->currentPage() ? 'active' : '' }}">
			<button type="button" class="page-link wa-log-page-btn" data-page="{{ $page }}">{{ $page }}</button>
		</li>
		@endfor
		<li class="page-item {{ $activityLogs->currentPage() >= $activityLogs->lastPage() ? 'disabled' : '' }}">
			<button type="button" class="page-link wa-log-page-btn" data-page="{{ $activityLogs->currentPage() + 1 }}" @disabled($activityLogs->currentPage() >= $activityLogs->lastPage()) aria-label="Next">&raquo;</button>
		</li>
	</ul>
	@endif
</nav>
