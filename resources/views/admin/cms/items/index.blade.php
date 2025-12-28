@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{ __('cms.items') }} -
				{{ $section->translate('en')->title ?? $section->type }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{ __('admin.dashboard') }}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('cms.pages.index')}}">{{ __('cms.cms-pages') }}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('cms.sections.index', $page)}}">{{ __('cms.sections') }}</a>
					</li>
					<li class="breadcrumb-item active">{{ __('cms.items') }}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-2">
					<div class="col-12">
						<a href="{{route('cms.sections.index', $page)}}"
							class="btn btn-secondary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-arrow-left me-1"></i>
							{{ __('cms.back-to') }}
							{{ __('cms.sections') }}
						</a>
						<a href="{{route('cms.items.create', [$page, $section])}}"
							class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-plus me-1"></i>
							{{ __('cms.add-new-item') }}
						</a>
						<a href="{{route('cms.links.index', ['section', $section->id])}}"
							class="btn btn-info btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-link me-1"></i>
							{{ __('cms.manage-links') }}
						</a>
					</div>
				</div>

				@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close"
						data-bs-dismiss="alert"></button>
				</div>
				@endif

				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>{{ __('admin.id') }}</th>
								<th>{{ __('cms.type') }}</th>
								<th>{{ __('cms.title-en') }}</th>
								<th>{{ __('cms.title-ar') }}</th>
								<th>{{ __('cms.images') }}</th>
								<th>{{ __('cms.order') }}</th>
								<th>{{ __('cms.status') }}</th>
								<th>{{ __('cms.actions') }}</th>
							</tr>
						</thead>
						<tbody>
							@forelse($items as $item)
							<tr>
								<td>{{ $item->id }}</td>
								<td>{{ $item->type }}</td>
								<td>{{ $item->translate('en')->title ?? '-' }}
								</td>
								<td>{{ $item->translate('ar')->title ?? '-' }}
								</td>
								<td>
									<span
										class="badge bg-info">{{ $item->getMedia('images')->count() }}</span>
								</td>
								<td>{{ $item->order }}</td>
								<td>
									<span
										class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
										{{ $item->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
								<td>
									<a href="{{route('cms.links.index', ['item', $item->id])}}"
										class="btn btn-sm btn-warning"
										title="Links">
										<i
											class="mdi mdi-link"></i>
									</a>
									<a href="{{route('cms.items.edit', [$page, $section, $item])}}"
										class="btn btn-sm btn-primary"
										title="Edit">
										<i
											class="mdi mdi-pencil"></i>
									</a>
									<form action="{{route('cms.items.destroy', [$page, $section, $item])}}"
										method="POST"
										class="d-inline"
										onsubmit="return confirm('Are you sure?')">
										@csrf
										@method('DELETE')
										<button type="submit"
											class="btn btn-sm btn-danger"
											title="Delete">
											<i
												class="mdi mdi-delete"></i>
										</button>
									</form>
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="8" class="text-center">
									{{ __('cms.no-items-found') }}
								</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection