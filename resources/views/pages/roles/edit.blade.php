@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.edit-role')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('roles.index')}}">{{__('admin.roles')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.edit-role')}}</li>
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
				@if ($errors->any())
					@foreach ($errors->all() as $error)
						<div class="alert alert-danger inverse alert-dismissible fade show" role="alert">
							<i class="icon-thumb-down"></i>
							<p>{{ $error }}</p>
							<button class="close" type="button" data-dismiss="alert" aria-label="Close" data-original-title="" title=""><span aria-hidden="true">×</span></button>
						</div>
					@endforeach
				@endif

				<form action="{{route('roles.update',$role->id)}}" method="post">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-sm-12">
							<div class="mb-3">
								<label for="name" class="form-label">{{__('admin.name')}} <span class="text-danger">*</span></label>
								<input type="text" required name="name" value="{{old('name', $role->name)}}" class="form-control" id="name" placeholder="{{__('admin.name')}}">
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('admin.permissions')}}</label>
						<div class="row">
							@foreach($permissionGroups as $module => $modulePermissions)
							<div class="col-md-6 mb-3">
								<div class="card">
									<div class="card-header">
										<h5 class="mb-0">{{__('admin.module-' . $module, [], app()->getLocale()) ?: ucfirst(str_replace('-', ' ', $module))}}</h5>
									</div>
									<div class="card-body">
										@foreach($modulePermissions as $permission)
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="permissions[]" value="{{$permission->id}}" id="permission_{{$permission->id}}" {{in_array($permission->id, $rolePermissions) ? 'checked' : ''}}>
											<label class="form-check-label" for="permission_{{$permission->id}}">
												{{__('admin.permission-' . $permission->name, [], app()->getLocale()) ?: ucfirst(str_replace('-', ' ', $permission->name))}}
											</label>
										</div>
										@endforeach
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit" class="btn btn-primary waves-effect waves-light">{{__('admin.update')}}</button>
						<a href="{{route('roles.index')}}" class="btn btn-secondary waves-effect waves-light">{{__('admin.cancel')}}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection

