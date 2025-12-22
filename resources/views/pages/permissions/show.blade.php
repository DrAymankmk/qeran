@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.permission-details')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('permissions.index')}}">{{__('admin.permissions')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.permission-details')}}</li>
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
				<h4 class="mb-3">{{__('admin.permission-information')}}</h4>

				<div class="mb-3">
					<label class="form-label fw-bold">{{__('admin.id')}}:</label>
					<p class="mb-0">{{$permission->id}}</p>
				</div>

				<div class="mb-3">
					<label class="form-label fw-bold">{{__('admin.name')}}:</label>
					<p class="mb-0">{{$permission->name}}</p>
				</div>

				<div class="mb-3">
					<label class="form-label fw-bold">{{__('admin.roles')}}:</label>
					<div>
						@if($permission->roles->count() > 0)
							@foreach($permission->roles as $role)
								<span class="badge bg-primary me-1 mb-1">{{$role->name}}</span>
							@endforeach
						@else
							<span class="badge bg-danger">{{__('admin.no-roles')}}</span>
						@endif
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label fw-bold">{{__('admin.created_at')}}:</label>
					<p class="mb-0">
						{{Carbon\Carbon::parse($permission->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
					</p>
				</div>

				<div class="d-flex gap-2 mt-4">
					<a href="{{route('permissions.edit', $permission->id)}}" class="btn btn-warning waves-effect waves-light">
						<i class="mdi mdi-file-edit-outline"></i> {{__('admin.edit')}}
					</a>
					<a href="{{route('permissions.index')}}" class="btn btn-secondary waves-effect waves-light">
						<i class="mdi mdi-arrow-left"></i> {{__('admin.back')}}
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection





























