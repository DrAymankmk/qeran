@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.admin-details')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('admins.index')}}">{{__('admin.admins')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.admin-details')}}</li>
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
				<div class="row">
					<div class="col-md-4 text-center mb-3">
						<img src="{{$admin->image()}}" alt="Admin Image"
							style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 3px solid #e9ecef;">
					</div>
					<div class="col-md-8">
						<h4 class="mb-3">{{__('admin.admin-information')}}</h4>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.id')}}:</label>
							<p class="mb-0">{{$admin->id}}</p>
						</div>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.name')}}:</label>
							<p class="mb-0">{{$admin->name}}</p>
						</div>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.email')}}:</label>
							<p class="mb-0">{{$admin->email}}</p>
						</div>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.created_at')}}:</label>
							<p class="mb-0">
								{{Carbon\Carbon::parse($admin->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
							</p>
						</div>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.updated_at')}}:</label>
							<p class="mb-0">
								{{Carbon\Carbon::parse($admin->updated_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
							</p>
						</div>

						<div class="mb-3">
							<label class="form-label fw-bold">{{__('admin.roles')}}:</label>
							<div>
								@if($admin->roles->count() > 0)
									@foreach($admin->roles as $role)
										<span class="badge bg-primary me-1 mb-1">{{$role->name}}</span>
									@endforeach
								@else
									<span class="badge bg-danger">{{__('admin.no-roles')}}</span>
								@endif
							</div>
						</div>

						<div class="d-flex gap-2 mt-4">
							<a href="{{route('admins.edit', $admin->id)}}" class="btn btn-warning waves-effect waves-light">
								<i class="mdi mdi-file-edit-outline"></i> {{__('admin.edit')}}
							</a>
							<a href="{{route('admins.index')}}" class="btn btn-secondary waves-effect waves-light">
								<i class="mdi mdi-arrow-left"></i> {{__('admin.back')}}
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection





