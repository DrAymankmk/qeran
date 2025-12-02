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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.admins')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.admins')}}</li>
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
					<!-- <h4 class="card-title mb-0">{{__('admin.admins-list')}}</h4> -->
					@can('create-admins')
					<a href="{{route('admins.create')}}" class="btn btn-primary waves-effect waves-light">
						<i class="bx bx-plus"></i> {{__('admin.add-admin')}}
					</a>
					@endcan
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
					<table id="adminsTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.img')}}</th>
								<th scope="col">{{__('admin.name')}}</th>
								<th scope="col">{{__('admin.email')}}</th>
								<th scope="col">{{__('admin.roles')}}</th>
								<th scope="col">{{__('admin.created_at')}}</th>
								<th scope="col">{{__('admin.actions')}}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($admins as $admin)
							<tr>
								<td><a href="{{route('admins.show',$admin->id)}}"
										class="text-body fw-bold">{{$admin->id}}</a>
								</td>
								<td>
									<a href="{{$admin->image()}}"
										target="_blank"
										class="text-body fw-bold">
										<img class="rounded-circle header-profile-user"
											src="{{$admin->image()}}"
											alt="Admin Avatar"
											style="width: 40px; height: 40px; object-fit: cover;">
									</a>
								</td>
								<td>{{$admin->name}}</td>
								<td>{{$admin->email}}</td>
								<td>
									@if($admin->roles->count() > 0)
										@foreach($admin->roles as $role)
											<span class="badge bg-info me-1 mb-1 fw-bold font-size-18">{{$role->name}}</span>
										@endforeach
									@else
										<span class="badge bg-danger">{{__('admin.no-roles')}}</span>
									@endif
								</td>
								<td>
									{{Carbon\Carbon::parse($admin->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
									@can('view-admins')
										<a href="{{route('admins.show',$admin->id)}}"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye-check font-size-22"></i></a>
									@endcan
									@can('edit-admins')			
										<a href="{{route('admins.edit',$admin->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>
									@endcan
									@can('delete-admins')
										@if($admin->id !== auth()->guard('admin')->id())
										
										<a onclick="openModalDelete({{$admin->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>
										@else
										<span class="text-muted" title="{{__('admin.cannot-delete-yourself')}}">
											<i class="mdi mdi-trash-can-outline font-size-22"></i>
										</span>
										@endif
									@endcan	
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

@include('pages.admins.scripts.index-scripts')

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





