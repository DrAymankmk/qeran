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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.categories')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.categories')}}</li>
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
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<i class="mdi mdi-alert-circle me-2"></i>
					<strong>{{__('admin.error')}}</strong>
					<ul class="mb-0 mt-2">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close" data-bs-dismiss="alert"
						aria-label="Close"></button>
				</div>
				@endif
				<div class="crypto-buy-sell-nav">

					<ul class="nav nav-tabs nav-tabs-custom" role="tablist">
						<li class="nav-item">
							<a class="nav-link active show"
								data-bs-toggle="tab" href="#buy"
								role="tab">
								عربي
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab"
								href="#sell" role="tab">
								English
							</a>
						</li>
					</ul>
					<form action="{{route('category.store')}}" method="post"
						enctype="multipart/form-data" id="categoryCreateForm"
						novalidate>

						<div class="tab-content crypto-buy-sell-nav-content p-4">
							@csrf
							<div class="tab-pane active" id="buy"
								role="tabpanel">

								<div class="row">


									<div class="col-sm-12">

										<div class="mb-3">
											<label for="formrow-title-input"
												class="form-label">
												{{__('admin.title')}}
											</label>
											<input type="text"
												name="ar[title]"
												value="{{old('ar.title')}}"
												required
												class="form-control @error('ar.title') is-invalid @enderror"
												id="formrow-title-input"
												placeholder="{{__('admin.title')}}">
											@error('ar.title')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

										<div class="mb-3">
											<label for="formrow-firstname-input"
												class="form-label">
												{{__('admin.name')}}
											</label>
											<input type="text"
												name="ar[name]"
												value="{{old('ar.name')}}"
												required
												class="form-control @error('ar.name') is-invalid @enderror"
												id="formrow-firstname-input"
												placeholder="{{__('admin.name')}}">
											@error('ar.name')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

										<div class="mb-3">
											<label for="formrow-description-input"
												class="form-label">
												{{__('admin.description')}}
											</label>
											<textarea name="ar[description]"
												class="form-control @error('ar.description') is-invalid @enderror"
												id="formrow-description-input"
												rows="4"
												placeholder="{{__('admin.description')}}">{{old('ar.description')}}</textarea>
											@error('ar.description')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

									</div>
								</div>
							</div>
							<div class="tab-pane" id="sell" role="tabpanel">
								<div class="row">


									<div class="col-sm-12">

										<div class="mb-3">
											<label for="formrow-english-title-input"
												class="form-label">
												{{__('admin.english-title')}}
											</label>
											<input type="text"
												name="en[title]"
												value="{{old('en.title')}}"
												required
												class="form-control @error('en.title') is-invalid @enderror"
												id="formrow-english-title-input"
												placeholder="{{__('admin.title')}}">
											@error('en.title')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

										<div class="mb-3">
											<label for="formrow-english-name-input"
												class="form-label">
												{{__('admin.english-name')}}
											</label>
											<input type="text"
												name="en[name]"
												value="{{old('en.name')}}"
												required
												class="form-control @error('en.name') is-invalid @enderror"
												id="formrow-english-name-input"
												placeholder="{{__('admin.name')}}">
											@error('en.name')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

										<div class="mb-3">
											<label for="formrow-description-input-en"
												class="form-label">
												{{__('admin.english-description')}}
											</label>
											<textarea name="en[description]"
												class="form-control @error('en.description') is-invalid @enderror"
												id="formrow-description-input-en"
												rows="4"
												placeholder="{{__('admin.description')}}">{{old('en.description')}}</textarea>
											@error('en.description')
											<div
												class="invalid-feedback">
												{{ $message }}
											</div>
											@enderror
										</div>

									</div>
								</div>

							</div>

						</div>
						<div class="mb-3">
							<label for="formFile" class="form-label">
								{{__('admin.img')}} </label>
							<input class="form-control @error('image') is-invalid @enderror"
								type="file" name="image" id="formFile">
							@error('image')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="d-flex flex-wrap gap-2">
							<button type="submit"
								class="btn btn-primary waves-effect waves-light">
								{{__('admin.add')}}</button>

						</div>

					</form>

				</div>
			</div>
		</div>


	</div>
</div>

<!-- end row -->
@endsection

@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

<!-- Required datatable js -->
<script src="{{asset('admin_assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<!-- Responsive examples -->
<script src="{{asset('admin_assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<!-- init js -->
<script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

<script>
// Auto-switch to tab with errors on page load
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
	@if($errors->has('ar.title') || $errors->has('ar.name') || $errors->has('ar.description'))
	// Switch to Arabic tab if it has errors
	const arabicTab = document.querySelector('a[href="#buy"]');
	if (arabicTab) {
		const tab = new bootstrap.Tab(arabicTab);
		tab.show();
	}
	@elseif($errors->has('en.title') || $errors->has('en.name') || $errors->has('en.description'))
	// Switch to English tab if it has errors
	const englishTab = document.querySelector('a[href="#sell"]');
	if (englishTab) {
		const tab = new bootstrap.Tab(englishTab);
		tab.show();
	}
	@endif
});
@endif

// Validate both Arabic and English data before form submission
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('categoryCreateForm');

	// Define error messages based on locale
	@if(app()->getLocale() == 'ar')
	const errorBothMissing = 'يرجى إدخال البيانات العربية والإنجليزية';
	const errorArabicMissing = 'يرجى إدخال البيانات العربية';
	const errorEnglishMissing = 'يرجى إدخال البيانات الإنجليزية';
	@else
	const errorBothMissing = 'Please enter both Arabic and English data';
	const errorArabicMissing = 'Please enter Arabic data';
	const errorEnglishMissing = 'Please enter English data';
	@endif

	if (form) {
		const submitHandler = function(e) {
			e
		.preventDefault(); // Always prevent default first, then validate

			// Get Arabic fields
			const arTitleInput = document.querySelector(
				'input[name="ar[title]"]');
			const arNameInput = document.querySelector(
				'input[name="ar[name]"]');
			const arDescriptionInput = document.querySelector(
				'textarea[name="ar[description]"]');

			// Get English fields
			const enTitleInput = document.querySelector(
				'input[name="en[title]"]');
			const enNameInput = document.querySelector(
				'input[name="en[name]"]');
			const enDescriptionInput = document.querySelector(
				'textarea[name="en[description]"]');

			// Get values and trim
			const arTitle = arTitleInput ? arTitleInput.value.trim() :
				'';
			const arName = arNameInput ? arNameInput.value.trim() : '';
			const arDescription = arDescriptionInput ?
				arDescriptionInput.value.trim() : '';

			const enTitle = enTitleInput ? enTitleInput.value.trim() :
				'';
			const enName = enNameInput ? enNameInput.value.trim() : '';
			const enDescription = enDescriptionInput ?
				enDescriptionInput.value.trim() : '';

			// Check if Arabic data is missing
			const arabicMissing = !arTitle || !arName || !arDescription;

			// Check if English data is missing
			const englishMissing = !enTitle || !enName || !
			enDescription;

			// If both are missing or incomplete, show error
			if (arabicMissing && englishMissing) {
				showValidationError(errorBothMissing, 'buy');
				return false;
			}

			// If Arabic data is missing
			if (arabicMissing) {
				showValidationError(errorArabicMissing, 'buy');
				return false;
			}

			// If English data is missing
			if (englishMissing) {
				showValidationError(errorEnglishMissing, 'sell');
				return false;
			}

			// If validation passes, remove the event listener and submit
			form.removeEventListener('submit', submitHandler);
			// Use requestSubmit if available (triggers validation), otherwise use submit
			if (typeof form.requestSubmit === 'function') {
				form.requestSubmit();
			} else {
				form.submit();
			}
		};

		form.addEventListener('submit', submitHandler);
	} else {
		console.error('Category form not found!');
	}

	function showValidationError(message, tabId) {
		// Remove existing custom error alert if any
		const existingAlert = document.querySelector('.custom-validation-alert');
		if (existingAlert) {
			existingAlert.remove();
		}

		// Create error alert
		const alertDiv = document.createElement('div');
		alertDiv.className =
			'alert alert-danger alert-dismissible fade show custom-validation-alert';
		alertDiv.setAttribute('role', 'alert');
		alertDiv.innerHTML = `
			<i class="mdi mdi-alert-circle me-2"></i>
			<strong>{{__('admin.error')}}</strong>
			<p class="mb-0 mt-2">${message}</p>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		`;

		// Insert alert at the top of card-body
		const cardBody = document.querySelector('.card-body');
		if (cardBody) {
			cardBody.insertBefore(alertDiv, cardBody.firstChild);
		}

		// Switch to the appropriate tab
		const targetTab = document.querySelector(`a[href="#${tabId}"]`);
		if (targetTab) {
			const tab = new bootstrap.Tab(targetTab);
			tab.show();
		}

		// Scroll to top to show the error
		window.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	}
});
</script>

@endsection
