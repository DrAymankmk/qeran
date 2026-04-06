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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.edit-design')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('category.index')}}">{{__('admin.categories')}}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('designs.index', ['category_id' => $design->category_id])}}">{{__('admin.designs')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.edit-design')}}</li>
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
				<form action="{{route('designs.update',$design->id)}}" method="post"
					enctype="multipart/form-data">
					@method('PATCH')
					<div class="tab-content crypto-buy-sell-nav-content p-4">
						@csrf
						<div class="tab-pane active" id="buy" role="tabpanel">
							<div class="row">
								<div class="col-sm-12">
									<div class="mb-3">
										<label for="category_id"
											class="form-label">{{__('admin.category')}}
											<span
												class="text-danger">*</span></label>
										<select id="category_id"
											class="form-select"
											name="category_id"
											required>
											<option
												value="">
												{{__('admin.select-category')}}
											</option>
											@foreach($categories as $cat)
											<option value="{{$cat->id}}"
												{{old('category_id', $design->category_id) == $cat->id ? 'selected' : ''}}>
												{{$cat->name}}
											</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="mb-3">
										<label for="code"
											class="form-label">{{__('admin.code')}}</label>
										<div
											class="input-group">
											<input type="text"
												name="code"
												value="{{old('code', $design->code)}}"
												class="form-control"
												id="code"
												placeholder="{{__('admin.design-code')}}">
											<button type="button"
												class="btn btn-outline-secondary"
												id="generateCodeBtn">
												<i
													class="mdi mdi-refresh"></i>
												{{__('admin.generate')}}
											</button>
										</div>
										<div class="mt-2">
											<div
												class="row">
												<div
													class="col-md-6">
													<label for="codeLength"
														class="form-label small">{{__('admin.length')}}</label>
													<input type="number"
														id="codeLength"
														class="form-control form-control-sm"
														value="8"
														min="1"
														max="50">
												</div>
												<div
													class="col-md-6">
													<label for="codeType"
														class="form-label small">{{__('admin.type')}}</label>
													<select id="codeType"
														class="form-select form-select-sm">
														<option
															value="numbers">
															{{__('admin.numbers')}}
														</option>
														<option
															value="characters">
															{{__('admin.characters')}}
														</option>
														<option value="mixed"
															selected>
															{{__('admin.numbers-characters')}}
														</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="mb-3">
										<label for="image"
											class="form-label">{{ __('admin.image-or-video') }}</label>
										@if($design->image())
										<div class="mb-2">
											@php $hubMime = $design->hubFiles?->getMimeType ?? ''; @endphp
											@if($hubMime && str_starts_with($hubMime, 'video/'))
											<video src="{{ $design->image() }}" controls muted playsinline
												style="max-width: 200px; max-height: 200px;"
												class="img-thumbnail p-0"></video>
											@else
											<img src="{{$design->image()}}"
												alt="{{ __('admin.image-or-video') }}"
												style="max-width: 200px; max-height: 200px; object-fit: cover;"
												class="img-thumbnail">
											@endif
											<p class="text-muted small mt-1">{{ __('validation.design_current_media') }}</p>
										</div>
										@endif
										<input type="file"
											name="image"
											accept="image/*,video/*"
											class="form-control"
											id="image">
										<small class="form-text text-muted">{{ __('validation.design_media_replace_help', ['max' => (int) round(\App\Helpers\Constant::DESIGN_MEDIA_MAX_UPLOAD_KB / 1024)]) }}</small>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="mb-3">
										<label
											class="form-label">{{__('admin.show-on')}}</label>
										@php
										$showOn =
										old('show_on',
										$design->show_on ??
										[]);
										if
										(!is_array($showOn))
										{
										$showOn = [];
										}
										@endphp
										<div class="row">
											<div
												class="col-md-6">
												<div
													class="form-check">
													<input class="form-check-input"
														type="checkbox"
														name="show_on[]"
														value="home"
														id="show_on_home"
														{{ in_array('home', $showOn) ? 'checked' : '' }}>
													<label class="form-check-label"
														for="show_on_home">
														{{__('admin.home-page')}}
													</label>
												</div>
												<div
													class="form-check">
													<input class="form-check-input"
														type="checkbox"
														name="show_on[]"
														value="footer"
														id="show_on_footer"
														{{ in_array('footer', $showOn) ? 'checked' : '' }}>
													<label class="form-check-label"
														for="show_on_footer">
														{{__('admin.footer')}}
													</label>
												</div>
												<div
													class="form-check">
													<input class="form-check-input"
														type="checkbox"
														name="show_on[]"
														value="gallery"
														id="show_on_gallery"
														{{ in_array('gallery', $showOn) ? 'checked' : '' }}>
													<label class="form-check-label"
														for="show_on_gallery">
														{{__('admin.gallery-page')}}
													</label>
												</div>
											</div>
											<div
												class="col-md-6">
												<div
													class="form-check">
													<input class="form-check-input"
														type="checkbox"
														name="show_on[]"
														value="services"
														id="show_on_services"
														{{ in_array('services', $showOn) ? 'checked' : '' }}>
													<label class="form-check-label"
														for="show_on_services">
														{{__('admin.services-page')}}
													</label>
												</div>
												<div
													class="form-check">
													<input class="form-check-input"
														type="checkbox"
														name="show_on[]"
														value="about"
														id="show_on_about"
														{{ in_array('about', $showOn) ? 'checked' : '' }}>
													<label class="form-check-label"
														for="show_on_about">
														{{__('admin.about-page')}}
													</label>
												</div>
											</div>
										</div>
										<small
											class="form-text text-muted">{{__('admin.select-where-this-design-should-be-displayed')}}</small>
									</div>
								</div>
							</div>

							<!-- Language Tabs -->
							<ul class="nav nav-tabs mt-4" role="tablist">
								<li class="nav-item"
									role="presentation">
									<button class="nav-link active"
										id="en-tab"
										data-bs-toggle="tab"
										data-bs-target="#en"
										type="button"
										role="tab">
										{{__('admin.english')}}
									</button>
								</li>
								<li class="nav-item"
									role="presentation">
									<button class="nav-link"
										id="ar-tab"
										data-bs-toggle="tab"
										data-bs-target="#ar"
										type="button"
										role="tab">
										{{__('admin.arabic')}}
									</button>
								</li>
							</ul>

							<div class="tab-content mt-3"
								id="languageTabContent">
								<!-- English Tab -->
								<div class="tab-pane fade show active"
									id="en" role="tabpanel">
									<div class="row">
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="en-name"
													class="form-label">{{__('admin.name-en')}}
													</label>
												<input type="text"
													name="en[name]"
													value="{{old('en.name', $design->translate('en')->name ?? '')}}"
													class="form-control"
													id="en-name"
													placeholder="{{__('admin.design-name')}}">
											</div>
										</div>
									</div>
								</div>

								<!-- Arabic Tab -->
								<div class="tab-pane fade" id="ar"
									role="tabpanel">
									<div class="row">
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="ar-name"
													class="form-label">{{__('admin.name-ar')}}
													</label>
												<input type="text"
													name="ar[name]"
													value="{{old('ar.name', $design->translate('ar')->name ?? '')}}"
													class="form-control"
													id="ar-name"
													placeholder="{{__('admin.design-name')}}">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit"
							class="btn btn-primary waves-effect waves-light">
							{{__('admin.update')}}</button>
						<a href="{{route('designs.index', ['category_id' => $design->category_id])}}"
							class="btn btn-secondary waves-effect waves-light">{{__('admin.cancel')}}</a>

					</div>

				</form>

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
// Code Generator Function
function generateCode(length, type) {
	let characters = '';
	let result = '';

	if (type === 'numbers') {
		characters = '0123456789';
	} else if (type === 'characters') {
		characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	} else if (type === 'mixed') {
		characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}

	const charactersLength = characters.length;
	for (let i = 0; i < length; i++) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}

	return result;
}

// Generate Code Button Click Handler
$(document).ready(function() {
	$('#generateCodeBtn').on('click', function() {
		const length = parseInt($('#codeLength').val()) || 8;
		const type = $('#codeType').val() || 'mixed';

		if (length < 1 || length > 50) {
			alert('{{__('admin.length-must-be-between-1-and-50')}}');
			return;
		}

		const generatedCode = generateCode(length, type);
		$('#code').val(generatedCode);
	});
});
</script>

@endsection