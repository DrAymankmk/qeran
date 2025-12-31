@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('translations.edit-translation')}}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('admin.translations.index')}}">{{__('translations.manage-translations')}}</a></li>
					<li class="breadcrumb-item active">{{__('translations.edit-translation')}}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="{{route('admin.translations.update', ['locale' => $locale, 'file' => $file, 'key' => $key])}}" method="POST">
					@csrf
					@method('PUT')

					<div class="mb-3">
						<label class="form-label">{{__('translations.locale')}}</label>
						<input type="text" class="form-control" value="{{strtoupper($locale)}}" readonly>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.file')}}</label>
						<input type="text" class="form-control" value="{{$file}}.php" readonly>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.key')}}</label>
						<input type="text" class="form-control" value="{{$key}}" readonly>
						<small class="form-text text-muted">{{__('translations.key-path')}}: <code>{{$keyPath}}</code></small>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.value')}} <span class="text-danger">*</span></label>
						<textarea name="value" class="form-control @error('value') is-invalid @enderror" rows="5" required>{{old('value', $value)}}</textarea>
						@error('value')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<button type="submit" class="btn btn-primary waves-effect waves-light">
							<i class="mdi mdi-content-save me-1"></i>
							{{__('translations.update')}}
						</button>
						<a href="{{route('admin.translations.index', ['locale' => $locale, 'file' => $file])}}" class="btn btn-secondary waves-effect waves-light">
							<i class="mdi mdi-close me-1"></i>
							{{__('translations.cancel')}}
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection







