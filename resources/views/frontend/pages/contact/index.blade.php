@extends('frontend.layouts.app')
@section('content')
@php
$contactSection = $contactPage->activeSections->where('name', 'contact')->first();
@endphp
<div class="b-title-page area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="ui-decor-2 ui-decor-2_vert bg-primary"></div>
					<h1 class="b-title-page__title">{{ $contactPage->title }}</h1>
					<ol class="breadcrumb">
						<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
						</li>
						<li class="active">{{ __('frontend.contact') }}</li>
					</ol>
					<!-- end breadcrumb-->
				</div>
			</div>
		</div>
	</div>
</div>
<section class="section-contact">
	<div class="container">

		<div class="row" style="margin-bottom: 40px;">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">{{ $contactSection->title ?? '' }}</h2>
					<div class="ui-subtitle-block">{{ $contactSection->subtitle ?? '' }}
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			@php
			$phoneSetting = \App\Models\Setting::where('key', 'phone')->first();
			$emailSetting = \App\Models\Setting::where('key', 'email')->first();
			@endphp

			<div class="col-lg-6 col-lg-offset-0 col-md-6 col-md-offset-3">
				<div data-stellar-background-ratio="0.4"
					class="b-contact stellar section-texture section-texture_blue section-radius">
					<div class="b-contact__name">{{ __('frontend.phone') }}</div>
					<div class="b-contact__info">
						{{ $phoneSetting?->content ?? '0571868088' }}
					</div>
					<div class="b-contact__icon icon-call-in"></div>
				</div>
				<!-- end b-contact-->
			</div>
			<div class="col-lg-6 col-lg-offset-0 col-md-6 col-md-offset-3">
				<div data-stellar-background-ratio="0.4"
					class="b-contact stellar section-texture section-texture_grey section-radius">
					<div class="b-contact__name">{{ __('frontend.email') }}</div>
					<div class="b-contact__info">
						{{ $emailSetting?->content ?? 'qeraninvitation@gmail.com' }}
						<div class="b-contact__icon icon-envelope-open"></div>
					</div>
					<!-- end b-contact-->
				</div>
			</div>
		</div>
</section>
<section class="section-contact" style="padding-top:0px">
	<div class="container">

		<div class="row">
			@foreach($contactSection->items as $item)
			<div class="col-lg-4 col-lg-offset-0 col-md-6 col-md-offset-3">
				<div data-stellar-background-ratio="0.4"
					class="b-contact stellar section-texture section-texture_green section-radius">
					<div class="b-contact__name">{{ $item->title }}</div>
					<div class="b-contact__info">{!! formatCmsContent($item->content) !!}
					</div>
					@if($item->icon)
					<div class="b-contact__icon {{ $item->icon }}"></div>
					@endif
				</div>
				<!-- end b-contact-->
			</div>
			@endforeach

		</div>
	</div>
</section>
<div class="section-form-contact">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2 class="ui-title-block"><i class="ui-decor-2 bg-primary"></i>
					{{ __('frontend.message_form') }}
				</h2>
				<div id="success"></div>
				@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close"
						data-bs-dismiss="alert"></button>
				</div>
				@endif
				@if($errors->any())
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<ul class="mb-0">
						@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close"
						data-bs-dismiss="alert"></button>
				</div>
				@endif
				<form id="contactForm" action="{{ route('contact.store') }}" method="post"
					class="b-form-contacts ui-form">
					@csrf
					<div class="row">
						<div class="col-md-6">
							<input id="user-name" type="text" name="name"
								placeholder="{{ __('frontend.name') }}"
								required="required"
								class="form-control @error('name') is-invalid @enderror"
								value="{{ old('name') }}" />
							@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<input id="user-phone" type="tel" name="phone"
								placeholder="{{ __('frontend.phone') }}"
								required="required"
								class="form-control @error('phone') is-invalid @enderror"
								value="{{ old('phone') }}" />
							@error('phone')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<input type="hidden" name="country_code"
								value="966">
						</div>
						<div class="col-md-6">
							<input id="user-email" type="email" name="email"
								placeholder="{{ __('frontend.email') }}"
								class="form-control @error('email') is-invalid @enderror"
								value="{{ old('email') }}" />
							@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<input id="user-subject" type="text"
								name="subject"
								placeholder="{{ __('frontend.subject') }}"
								required="required"
								class="form-control last-block_mrg-btn_0 @error('subject') is-invalid @enderror"
								value="{{ old('subject') }}" />
							@error('subject')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<textarea id="user-message" name="message"
								rows="5"
								placeholder="{{ __('frontend.message') }}"
								required="required"
								class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
							@error('message')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<button type="submit"
								class="btn btn-primary btn-block">{{ __('frontend.send') }}
							</button>
						</div>
					</div>
				</form>
				<!-- end .b-form-contact-->
			</div>

		</div>
	</div>
</div>
@endsection