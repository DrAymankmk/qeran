@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.edit-admin')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('admins.index')}}">{{__('admin.admins')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.edit-admin')}}</li>
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

				<form action="{{route('admins.update',$admin->id)}}" method="post" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-sm-12">
							<div class="mb-3">
								<label for="name" class="form-label">{{__('admin.name')}} <span class="text-danger">*</span></label>
								<input type="text" required name="name" value="{{old('name', $admin->name)}}" class="form-control" id="name" placeholder="{{__('admin.name')}}">
							</div>

							<div class="mb-3">
								<label for="email" class="form-label">{{__('admin.email')}} <span class="text-danger">*</span></label>
								<input type="email" required name="email" value="{{old('email', $admin->email)}}" class="form-control" id="email" placeholder="{{__('admin.email')}}">
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">{{__('admin.password')}}</label>
								<input type="password" name="password" class="form-control" id="password" placeholder="{{__('admin.password')}}" minlength="6">
								<small class="form-text text-muted">{{__('admin.password-optional')}}</small>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label for="image" class="form-label">{{__('admin.img')}}</label>
						<input class="form-control" type="file" name="image" id="image" accept="image/*">
						<small class="form-text text-muted">{{__('admin.image-help')}}</small>
					</div>

					@if($admin->image())
					<div class="mb-3">
						<label class="form-label">{{__('admin.current-image')}}</label>
						<div>
							<img style="width: 150px;height: 150px;padding-bottom: 15px; object-fit: cover; border-radius: 8px;" src="{{$admin->image()}}" alt="Current Image">
						</div>
					</div>
					@endif

					<div class="mb-3">
						<label for="roles" class="form-label">{{__('admin.roles')}} <span class="text-danger">*</span></label>
						<select class="form-select" name="roles[]" id="roles" multiple required>
							@foreach($roles as $role)
								<option value="{{$role->id}}" {{in_array($role->id, $adminRoles) ? 'selected' : ''}}>{{$role->name}}</option>
							@endforeach
						</select>
						<small class="form-text text-muted">{{__('admin.select-at-least-one-role')}}</small>
					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit" class="btn btn-primary waves-effect waves-light">{{__('admin.update')}}</button>
						<a href="{{route('admins.index')}}" class="btn btn-secondary waves-effect waves-light">{{__('admin.cancel')}}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection





