@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{ __('cms.links-management') }} - {{ ucfirst($type) }}
			</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{ __('admin.dashboard') }}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('cms.pages.index')}}">{{ __('cms.cms-pages') }}</a>
					</li>
					<li class="breadcrumb-item active">{{ __('cms.links-management') }}
					</li>
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
						@if($type == 'page')
						<a href="{{route('cms.pages.index')}}"
							class="btn btn-secondary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-arrow-left me-1"></i>
							{{ __('cms.back-to') }}
							{{ __('cms.cms-pages') }}
						</a>
						@elseif($type == 'section')
						<a href="{{route('cms.sections.index', $model->page)}}"
							class="btn btn-secondary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-arrow-left me-1"></i>
							{{ __('cms.back-to') }}
							{{ __('cms.sections') }}
						</a>
						@elseif($type == 'item')
						<a href="{{route('cms.items.index', [$model->section->page, $model->section])}}"
							class="btn btn-secondary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-arrow-left me-1"></i>
							{{ __('cms.back-to') }}
							{{ __('cms.items') }}
						</a>
						@endif
						<a href="{{route('cms.links.create', [$type, $id])}}"
							class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-plus me-1"></i>
							{{ __('cms.add-new-link') }}
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
								<th>{{ __('cms.name') }}</th>
								<th>{{ __('cms.link-route') }}</th>
								<th>{{ __('cms.icon') }}</th>
								<th>{{ __('cms.type') }}</th>
								<th>{{ __('cms.target') }}</th>
								<th>{{ __('cms.order') }}</th>
								<th>{{ __('cms.status') }}</th>
								<th>{{ __('cms.actions') }}</th>
							</tr>
						</thead>
						<tbody>
							@forelse($links as $link)
							<tr>
								<td>{{ $link->id }}</td>
								<td>{{ $link->name }}</td>
								<td>
									<a href="{{ $link->url }}"
										target="_blank">
										@if($link->route_name)
										<span
											class="badge bg-info">{{ $link->route_name }}</span>
										@else
										{{ Str::limit($link->link, 30) }}
										@endif
									</a>
								</td>
								<td>
									@if($link->icon)
									{!! $link->icon_html !!}
									@else
									-
									@endif
								</td>
								<td>{{ $link->type ?? '-' }}</td>
								<td>{{ $link->target }}</td>
								<td>{{ $link->order }}</td>
								<td>
									<span
										class="badge bg-{{ $link->is_active ? 'success' : 'danger' }}">
										{{ $link->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
								<td>
									<a href="{{route('cms.links.edit', [$type, $id, $link])}}"
										class="btn btn-sm btn-primary"
										title="Edit">
										<i
											class="mdi mdi-pencil"></i>
									</a>
									<form action="{{route('cms.links.destroy', [$type, $id, $link])}}"
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
								<td colspan="9" class="text-center">
									{{ __('cms.no-links-found') }}
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