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
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="b-contact__title ui-subtitle-block">
						{{ $contactSection->subtitle }}</h2>
				</div>
			</div>
		</div>
		<div class="row">
			@foreach($contactSection->items as $item)
			<div class="col-lg-4 col-lg-offset-0 col-md-6 col-md-offset-3">
				<div data-stellar-background-ratio="0.4"
					class="b-contact stellar section-texture section-texture_green section-radius">
					<div class="b-contact__name">{{ $item->title }}</div>
					<div class="b-contact__info">{{ $item->description }}</div>
					<div class="b-contact__icon {{ $item->icon }}"></div>
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
				<form id="contactForm" action="#" method="post" class="b-form-contacts ui-form">
					<div class="row">
						<div class="col-md-6">
							<input id="user-name" type="text" name="user-name"
								placeholder="{{ __('frontend.name') }}"
								required="required"
								class="form-control" />
							<input id="user-phone" type="tel"
								name="user-phone"
								placeholder="{{ __('frontend.phone') }}"
								class="form-control" />
						</div>
						<div class="col-md-6">
							<input id="user-email" type="email"
								name="user-email"
								placeholder="{{ __('frontend.email') }}"
								class="form-control" />
							<input id="user-subject" type="text"
								name="user-subject"
								placeholder="{{ __('frontend.subject') }}"
								class="form-control last-block_mrg-btn_0" />
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<textarea id="user-message" rows="5"
								placeholder="{{ __('frontend.message') }}"
								required="required"
								class="form-control"></textarea>
							<button class="btn btn-primary btn-block">{{ __('frontend.send') }}
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