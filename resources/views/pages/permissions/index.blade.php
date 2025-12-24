@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />

@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.permissions')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.permissions')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0">{{__('admin.permissions-list')}}</h4>
					<a href="{{route('permissions.create')}}" class="btn btn-primary waves-effect waves-light">
						<i class="bx bx-plus"></i> {{__('admin.add-permission')}}
					</a>
				</div>

				@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
				@endif

				@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ session('error') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
				@endif

				<div class="table-responsive mt-2">
					<table id="permissionsTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.name')}}</th>
								<th scope="col">{{__('admin.roles-count')}}</th>
								<th scope="col">{{__('admin.created_at')}}</th>
								<th scope="col">{{__('admin.actions')}}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($permissions as $permission)
							<tr>
								<td><a href="{{route('permissions.show',$permission->id)}}"
										class="text-body fw-bold">{{$permission->id}}</a>
								</td>
								<td>{{$permission->name}}</td>
								<td>
									<span class="badge bg-info">{{$permission->roles->count()}} {{__('admin.roles')}}</span>
								</td>
								<td>
									{{Carbon\Carbon::parse($permission->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										<a href="{{route('permissions.show',$permission->id)}}"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye-check font-size-22"></i></a>
										<a href="{{route('permissions.edit',$permission->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>

										<a onclick="openModalDelete({{$permission->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection

@include('pages.permissions.scripts.index-scripts')

@component('layouts.includes.modal')
@slot('modalID')
deleteModal
@endslot
@slot('modalTitle')
{{__('admin.delete-data')}}
@endslot
@slot('modalMethodPutOrDelete')
@method('delete')
@endslot
@slot('modalContent')
<div class="text-center">
	<span class="text-danger font-16">
		{{__('admin.delete-message-confirm')}}
	</span>
</div>
@endslot
@endcomponent
































