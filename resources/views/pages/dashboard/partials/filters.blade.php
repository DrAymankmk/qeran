<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="GET" action="{{route('admin.dashboard')}}" id="filterForm">
					<div class="row g-3 align-items-end">
						<div class="col-md-3">
							<label
								class="form-label mb-0 fw-medium d-block">{{__('admin.quick-filter')}}:</label>
							<div class="btn-group w-100" role="group">
								<button type="button"
									class="btn btn-sm btn-outline-primary quick-filter-btn {{$filter == 'today' ? 'active' : ''}}"
									data-filter="today">{{__('admin.today')}}</button>
								<button type="button"
									class="btn btn-sm btn-outline-primary quick-filter-btn {{$filter == 'week' ? 'active' : ''}}"
									data-filter="week">{{__('admin.this-week')}}</button>
								<button type="button"
									class="btn btn-sm btn-outline-primary quick-filter-btn {{$filter == 'month' ? 'active' : ''}}"
									data-filter="month">{{__('admin.this-month')}}</button>
								<button type="button"
									class="btn btn-sm btn-outline-primary quick-filter-btn {{$filter == 'year' ? 'active' : ''}}"
									data-filter="year">{{__('admin.this-year')}}</button>
								<button type="button"
									class="btn btn-sm btn-outline-primary quick-filter-btn {{$filter == 'all' ? 'active' : ''}}"
									data-filter="all">{{__('admin.all-time')}}</button>
							</div>
							<input type="hidden" name="filter"
								id="filter_input"
								value="{{$filter ?? 'all'}}">
						</div>
						<div class="col-md-3">
							<label for="from_date"
								class="form-label mb-0 fw-medium">{{__('admin.from-date')}}:</label>
							<input type="date" name="from_date" id="from_date"
								class="form-control"
								value="{{$fromDate ?? ''}}">
						</div>
						<div class="col-md-3">
							<label for="to_date"
								class="form-label mb-0 fw-medium">{{__('admin.to-date')}}:</label>
							<input type="date" name="to_date" id="to_date"
								class="form-control"
								value="{{$toDate ?? ''}}">
						</div>
						<div class="col-md-3">
							<button type="submit"
								class="btn btn-primary w-100 mt-4">{{__('admin.apply-filter')}}</button>
						</div>


					</div>
				</form>
			</div>
		</div>
	</div>
</div>
