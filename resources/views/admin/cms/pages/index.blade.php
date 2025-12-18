@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
	type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">CMS Pages</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">Dashboard</a>
					</li>
					<li class="breadcrumb-item active">CMS Pages</li>
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
						<a href="{{route('cms.pages.create')}}"
							class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
							<i class="mdi mdi-plus me-1"></i> Add New Page
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
								<th>ID</th>
								<th>Name</th>
								<th>Slug</th>
								<th>Title (EN)</th>
								<th>Title (AR)</th>
								<th>Order</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($pages as $page)
							<tr>
								<td>{{ $page->id }}</td>
								<td>{{ $page->name }}</td>
								<td>{{ $page->slug }}</td>
								<td>{{ $page->translate('en')->title ?? '-' }}
								</td>
								<td>{{ $page->translate('ar')->title ?? '-' }}
								</td>
								<td>{{ $page->order }}</td>
								<td>
									<span
										class="badge bg-{{ $page->is_active ? 'success' : 'danger' }}">
										{{ $page->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
								<td>
									<a href="{{route('cms.sections.index', $page)}}"
										class="btn btn-sm btn-info"
										title="Sections">
										<i
											class="mdi mdi-folder"></i>
									</a>
									<a href="{{route('cms.pages.edit', $page)}}"
										class="btn btn-sm btn-primary"
										title="Edit">
										<i
											class="mdi mdi-pencil"></i>
									</a>
									<form action="{{route('cms.pages.destroy', $page)}}"
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
								<td colspan="8" class="text-center">No
									pages found</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="d-flex justify-content-center">
					{{ $pages->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection