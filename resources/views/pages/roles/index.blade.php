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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.roles')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.roles')}}</li>
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
					@can('create-roles')
					<a href="{{route('roles.create')}}"
						class="btn btn-primary waves-effect waves-light">
						<i class="bx bx-plus"></i> {{__('admin.add-role')}}
					</a>
					@endcan
				</div>

				@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"
						aria-label="Close"></button>
				</div>
				@endif

				@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ session('error') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"
						aria-label="Close"></button>
				</div>
				@endif

				<div class="table-responsive mt-2">
					<table id="rolesTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.role-name')}}
								</th>
								<th scope="col">
									{{__('admin.permissions')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($roles as $role)
							<tr>
								<td><a href="{{route('roles.show',$role->id)}}"
										class="text-body fw-bold">{{$role->id}}</a>
								</td>
								<td>{{$role->name}}</td>
								<td>
									@if($role->permissions->count()
									> 0)
									@foreach($role->permissions->take(3) as $permission)
									<span
										class="badge bg-info me-1 mb-1 fw-bold font-size-18">{{$permission->name}}</span>
									@endforeach
									@if($role->permissions->count()
									> 3)
									<span
										class="badge bg-secondary font-size-18">+{{$role->permissions->count() - 3}}
										{{__('admin.more')}}</span>
									@endif
									@else
									<span
										class="badge bg-danger font-size-18">{{__('admin.no-permissions')}}</span>
									@endif
								</td>
								<td>
									{{Carbon\Carbon::parse($role->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										@can('view-roles')
										<a href="{{route('roles.show',$role->id)}}"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye-check font-size-22"></i></a>
										@endcan
										@can('edit-roles')		
										<a href="{{route('roles.edit',$role->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>
										@endcan		
										@can('delete-roles')
										@if($role->name !== 'Super Admin')
										<a onclick="openModalDelete({{$role->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>
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

@include('pages.roles.scripts.index-scripts')

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
