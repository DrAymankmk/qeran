@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('translations.add-new-key')}}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{route('admin.translations.index')}}">{{__('translations.manage-translations')}}</a></li>
					<li class="breadcrumb-item active">{{__('translations.add-new-key')}}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="{{route('admin.translations.store')}}" method="POST">
					@csrf

					<div class="mb-3">
						<label class="form-label">{{__('translations.locale')}} <span class="text-danger">*</span></label>
						<select name="locale" class="form-select @error('locale') is-invalid @enderror" required>
							@foreach($availableLocales as $loc)
							<option value="{{$loc}}" {{old('locale', $locale) == $loc ? 'selected' : ''}}>{{strtoupper($loc)}}</option>
							@endforeach
						</select>
						@error('locale')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.file')}} <span class="text-danger">*</span></label>
						<select name="file" class="form-select @error('file') is-invalid @enderror" required>
							@foreach($availableFiles as $f)
							<option value="{{$f}}" {{old('file', $file) == $f ? 'selected' : ''}}>{{$f}}.php</option>
							@endforeach
						</select>
						@error('file')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.key')}} <span class="text-danger">*</span></label>
						<input type="text" name="key" class="form-control @error('key') is-invalid @enderror" 
							value="{{old('key')}}" 
							placeholder="e.g., new-key or nested.key.name" 
							required>
						<small class="form-text text-muted">{{__('translations.key-hint')}}</small>
						@error('key')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.value')}} <span class="text-danger">*</span></label>
						<textarea name="value" class="form-control @error('value') is-invalid @enderror" rows="5" required>{{old('value')}}</textarea>
						@error('value')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					@endif

					<div class="mb-3">
						<button type="submit" class="btn btn-primary waves-effect waves-light">
							<i class="mdi mdi-content-save me-1"></i>
							{{__('translations.save')}}
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

















