@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.add-permission')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('permissions.index')}}">{{__('admin.permissions')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.add-permission')}}</li>
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

				<form action="{{route('permissions.store')}}" method="post">
					@csrf

					<div class="row">
						<div class="col-sm-12">
							<div class="mb-3">
								<label for="name" class="form-label">{{__('admin.name')}} <span class="text-danger">*</span></label>
								<input type="text" required name="name" value="{{old('name')}}" class="form-control" id="name" placeholder="{{__('admin.name')}}">
								<small class="form-text text-muted">{{__('admin.permission-name-format')}}</small>
							</div>
						</div>
					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit" class="btn btn-primary waves-effect waves-light">{{__('admin.create')}}</button>
						<a href="{{route('permissions.index')}}" class="btn btn-secondary waves-effect waves-light">{{__('admin.cancel')}}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection















































