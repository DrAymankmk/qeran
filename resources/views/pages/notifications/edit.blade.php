@php use App\Helpers\Constant; @endphp
@extends('layouts.app')

@section('title', __('admin.add-new'))
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />

@endsection

@section('breadcrumb')
<li class="breadcrumb-item">
	<a href="{{ route('admin.dashboard') }}" class="text-muted">{{__('admin.home')}}</a>
</li>
<li class="breadcrumb-item">
	<a href="{{ route('notifications.index') }}" class="text-muted">{{__('admin.notifications')}}</a>
</li>
<li class="breadcrumb-item">
	<a href="" class="text-muted">{{__('admin.edit')}}</a>
</li>
@endsection


@section('content')

<!-- start page title -->
<div class="row">

	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.notifications')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item active">{{__('admin.notifications')}}</li>
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
				@php
				$hasArabicErrors = $errors->has('title.ar') || $errors->has('description.ar');
				$hasEnglishErrors = $errors->has('title.en') || $errors->has('description.en');
				$activeTabLocale = $hasEnglishErrors ? 'en' : 'ar';
				@endphp
				<ul class="nav nav-tabs nav-tabs-custom" role="tablist">
					@foreach(Constant::LANGUAGES as $key=>$value)
					<li class="nav-item">
						<a class="nav-link @if($activeTabLocale == $value) active show @endif"
							data-bs-toggle="tab" href="#{{$value}}"
							role="tab">
							{{__('admin.'.$key)}}
							@if(($value == 'ar' && $hasArabicErrors) ||
							($value == 'en' && $hasEnglishErrors))
							<span class="badge bg-danger ms-1">!</span>
							@endif
						</a>
					</li>
					@endforeach
				</ul>

				<form action="{{route('notifications.update',$notification->id)}}" method="post"
					enctype="multipart/form-data">

					<div class="tab-content crypto-buy-sell-nav-content p-4">
						@csrf
						@method('PATCH')
						@foreach(Constant::LANGUAGES as $key=>$value)
						<div class="tab-pane fade @if($activeTabLocale == $value) active show @endif"
							id="{{$value}}" role="tabpanel">

							@if(($value == 'ar' && $hasArabicErrors) ||
							($value == 'en' && $hasEnglishErrors))
							<div class="alert alert-danger alert-dismissible fade show mb-3"
								role="alert">
								<i class="icon-thumb-down"></i>
								<strong>{{__('admin.'.$key.'-validation-errors')}}</strong>
								<ul class="mb-0 mt-2">
									@if($errors->has('title.'.$value))
									<li>{{ $errors->first('title.'.$value) }}
									</li>
									@endif
									@if($errors->has('description.'.$value))
									<li>{{ $errors->first('description.'.$value) }}
									</li>
									@endif
								</ul>
								<button class="close" type="button"
									data-dismiss="alert"
									aria-label="Close"
									data-original-title=""
									title=""><span
										aria-hidden="true">×</span></button>
							</div>
							@endif

							<div class="row">
								<div class="col-sm-12">
									<div class="mb-3">
										<label for="formrow-firstname-input-{{$value}}"
											class="form-label">
											{{__('admin.title-'.$value)}}
										</label>
										<input type="text"
											name="title[{{$value}}]"
											value="{{old('title.'.$value, $notification->getTranslation('title',$value,'ar'))}}"
											required
											class="form-control @error('title.'.$value) is-invalid @enderror"
											id="formrow-firstname-input-{{$value}}"
											placeholder="{{__('admin.title-text')}}">
										@error('title.'.$value)
										<div
											class="invalid-feedback d-block">
											{{ $message }}
										</div>
										@enderror
									</div>
									<div class="mb-3">
										<label for="description-{{$value}}"
											class="form-label">
											{{__('admin.description')}}
										</label>
										<textarea class="form-control @error('description.'.$value) is-invalid @enderror"
											id="description-{{$value}}"
											name="description[{{$value}}]"
											rows="10"
											placeholder=" {{__('admin.description')}}">{{old('description.'.$value, $notification->getTranslation('description',$value,'ar'))}}</textarea>
										@error('description.'.$value)
										<div
											class="invalid-feedback d-block">
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>
							</div>
						</div>
						@endforeach
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
$(document).ready(function() {
	// Ensure only one tab is active at a time
	$('.nav-tabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
		// Remove active class from all tab panes
		$('.tab-pane').removeClass('active show');
		// Add active class to the target tab pane
		var target = $(e.target).attr('href');
		$(target).addClass('active show');
	});

	// On page load, ensure only the active tab pane is shown
	var activeTab = $('.nav-tabs .nav-link.active');
	if (activeTab.length > 0) {
		var targetPane = activeTab.attr('href');
		$('.tab-pane').removeClass('active show');
		$(targetPane).addClass('active show');
	}
});
</script>

@endsection
