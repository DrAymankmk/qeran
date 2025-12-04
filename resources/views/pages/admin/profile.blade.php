@extends('layouts.app')
@section('extra-css')
@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.profile')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
					<li class="breadcrumb-item active">{{__('admin.profile')}}</li>
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
				@if (session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<i class="mdi mdi-check-circle me-2"></i>
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif

				@if ($errors->any())
					@foreach ($errors->all() as $error)
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="mdi mdi-alert-circle me-2"></i>
							{{ $error }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					@endforeach
				@endif

				<form action="{{route('admin.profile.update')}}" method="post" enctype="multipart/form-data">
					@csrf
					@method('POST')

					<div class="row">
						<div class="col-md-8">
							<div class="mb-3">
								<label for="name" class="form-label">{{__('admin.name')}} <span class="text-danger">*</span></label>
								<input type="text" class="form-control @error('name') is-invalid @enderror"
									id="name" name="name" value="{{old('name', $admin->name)}}" required>
								@error('name')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="email" class="form-label">{{__('admin.email')}} <span class="text-danger">*</span></label>
								<input type="email" class="form-control @error('email') is-invalid @enderror"
									id="email" name="email" value="{{old('email', $admin->email)}}" required>
								@error('email')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">{{__('admin.password')}}</label>
								<input type="password" class="form-control @error('password') is-invalid @enderror"
									id="password" name="password" placeholder="{{__('admin.leave-blank-to-keep-current')}}">
								@error('password')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
								<small class="form-text text-muted">{{__('admin.password-min-hint')}}</small>
							</div>

							<div class="mb-3">
								<label for="password_confirmation" class="form-label">{{__('admin.confirm-password')}}</label>
								<input type="password" class="form-control"
									id="password_confirmation" name="password_confirmation" placeholder="{{__('admin.confirm-password')}}">
							</div>

							<div class="mb-3">
								<label for="image" class="form-label">{{__('admin.profile-image')}}</label>
								<input type="file" class="form-control @error('image') is-invalid @enderror"
									id="image" name="image" accept="image/*">
								@error('image')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
								<small class="form-text text-muted">{{__('admin.image-upload-hint')}}</small>
							</div>
						</div>

						<div class="col-md-4">
							<div class="text-center mb-3">
								<label class="form-label d-block">{{__('admin.current-image')}}</label>
								<img id="imagePreview" src="{{$admin->image()}}"
									alt="{{__('admin.profile-image')}}"
									class="img-thumbnail rounded-circle"
									style="width: 200px; height: 200px; object-fit: cover;">
								<p class="text-muted mt-2 mb-0">{{__('admin.current-profile-image')}}</p>
							</div>
						</div>
					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit" class="btn btn-primary waves-effect waves-light">
							<i class="mdi mdi-content-save me-1"></i> {{__('admin.update')}}
						</button>
						<a href="{{route('admin.dashboard')}}" class="btn btn-secondary waves-effect waves-light">
							<i class="mdi mdi-close me-1"></i> {{__('admin.cancel')}}
						</a>
					</div>

				</form>

			</div>
		</div>
	</div>
</div>

<!-- end row -->
@endsection

@section('extra-js')
<script>
	// Preview image before upload
	document.getElementById('image').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('imagePreview').src = e.target.result;
			}
			reader.readAsDataURL(file);
		}
	});
</script>
@endsection














