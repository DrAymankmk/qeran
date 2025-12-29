@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />

@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.edit-testimonial')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('testimonials.index')}}">{{__('admin.testimonials')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.edit')}}</li>
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
				<form action="{{route('testimonials.update',$testimonial->id)}}" method="post"
					enctype="multipart/form-data">
					@method('PATCH')
					<div class="tab-content crypto-buy-sell-nav-content p-4">
						@csrf
						<div class="tab-pane active" id="buy" role="tabpanel">
							<div class="row">
								<div class="col-sm-6">
									<div class="mb-3">
										<label for="rating"
											class="form-label">{{__('admin.rating')}}
										</label>
										<select id="rating"
											class="form-select"
											name="rating">
											<option
												value="">

												{{__('admin.no-rating')}}
											</option>
											<option value="1"
												{{old('rating', $testimonial->rating) == '1' ? 'selected' : ''}}>
												1
												{{__('admin.star')}}
											</option>
											<option value="2"
												{{old('rating', $testimonial->rating) == '2' ? 'selected' : ''}}>
												2
												{{__('admin.stars')}}
											</option>
											<option value="3"
												{{old('rating', $testimonial->rating) == '3' ? 'selected' : ''}}>
												3
												{{__('admin.stars')}}
											</option>
											<option value="4"
												{{old('rating', $testimonial->rating) == '4' ? 'selected' : ''}}>
												4
												{{__('admin.stars')}}
											</option>
											<option value="5"
												{{old('rating', $testimonial->rating) == '5' ? 'selected' : ''}}>
												5
												{{__('admin.stars')}}
											</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="mb-3">
										<label for="order"
											class="form-label">{{__('admin.order')}}</label>
										<input type="number"
											name="order"
											value="{{old('order', $testimonial->order)}}"
											class="form-control"
											id="order"
											placeholder="{{__('admin.order')}}">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="mb-3">
										<label for="is_active"
											class="form-label">{{__('admin.status')}}</label>
										<select id="is_active"
											class="form-select"
											name="is_active">
											<option value="1"
												{{old('is_active', $testimonial->is_active) == '1' ? 'selected' : ''}}>
												{{ __('admin.active') }}
											</option>
											<option value="0"
												{{old('is_active', $testimonial->is_active) == '0' ? 'selected' : ''}}>
												{{ __('admin.inactive') }}
											</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="mb-3">
										<label for="image"
											class="form-label">{{__('admin.image')}}
											({{__('admin.optional')}})</label>
										@if($testimonial->image())
										<div class="mb-2">
											<img src="{{$testimonial->image()}}"
												alt="Current Image"
												style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
										</div>
										@endif
										<input type="file"
											name="image"
											accept="image/*"
											class="form-control"
											id="image">
										<small
											class="form-text text-muted">JPEG,
											PNG, GIF,
											WebP, max
											5MB. Leave
											empty to
											keep
											current
											image.</small>
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
										{{ __('admin.arabic') }}
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
													<span
														class="text-danger">*</span></label>
												<input type="text"
													name="en[name]"
													value="{{old('en.name', $testimonial->translate('en')->name ?? '')}}"
													class="form-control"
													id="en-name"
													placeholder="{{__('admin.name-en')}}"
													required>
											</div>
										</div>
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="en-job"
													class="form-label">{{__('admin.job-title-en')}}
												</label>
												<input type="text"
													name="en[job]"
													value="{{old('en.job', $testimonial->translate('en')->job ?? '')}}"
													class="form-control"
													id="en-job"
													placeholder="{{__('admin.job-title-en')}}">
											</div>
										</div>
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="en-message"
													class="form-label">
													{{__('admin.testimonial-message-en')}}
													<span
														class="text-danger">*</span></label>
												<textarea name="en[message]"
													class="form-control"
													id="en-message"
													rows="5"
													required
													placeholder="{{__('admin.testimonial-message-en')}}">{{old('en.message', $testimonial->translate('en')->message ?? '')}}</textarea>
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
													<span
														class="text-danger">*</span></label>
												<input type="text"
													name="ar[name]"
													value="{{old('ar.name', $testimonial->translate('ar')->name ?? '')}}"
													class="form-control"
													id="ar-name"
													placeholder="{{__('admin.name-ar')}}"
													required>
											</div>
										</div>
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="ar-job"
													class="form-label">
													{{ __('admin.job-title-ar') }}
												</label>
												<input type="text"
													name="ar[job]"
													value="{{old('ar.job', $testimonial->translate('ar')->job ?? '')}}"
													class="form-control"
													id="ar-job"
													placeholder="{{__('admin.job-title-ar')}}">
											</div>
										</div>
										<div
											class="col-sm-12">
											<div
												class="mb-3">
												<label for="ar-message"
													class="form-label">
													{{ __('admin.testimonial-message-ar') }}
													<span
														class="text-danger">*</span></label>
												<textarea name="ar[message]"
													class="form-control"
													id="ar-message"
													rows="5"
													required
													placeholder="{{ __('admin.testimonial-message-ar') }}"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class=" d-flex flex-wrap gap-2">
						<button type="submit"
							class="btn btn-primary waves-effect waves-light">
							{{__('admin.update')}}</button>
						<a href="{{route('testimonials.index')}}"
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
@endsection