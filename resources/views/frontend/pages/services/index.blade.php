@extends('frontend.layouts.app')

@section('content')

@include('frontend.pages.services.partials.features-section')

@include('frontend.pages.home.partials.info-section')

@include('frontend.pages.home.partials.services-section')

@include('frontend.pages.home.partials.why-choose-us-section')

@include('frontend.pages.home.partials.guard-app-section')

@include('frontend.pages.services.partials.packages-section')


<div class="block-table block-table-md">
	<div class="block-table__cell col-md-6"><img src="{{ asset('frontend/assets/media/content/960x750/1.jpg') }}"
			alt="foto"></div>
	<div class="block-table__cell col-md-6">
		<section data-stellar-background-ratio="0.4"
			class="section-form-contact section-form-contact_color_white section-texture bg-primary stellar">
			<div class="ui-decor-1"><img
					src="{{ asset('frontend/assets/media/general/ui-decor-1_wh.png') }}"
					alt="decor">
			</div>
			<h2 class="ui-title-block"><span>Dvents</span> Contact Form</h2>
			<div class="ui-subtitle-block">Send us a message for your personalized event booking.
			</div>
			<div id="success"></div>
			<form id="contactForm" action="#" method="post" class="b-form-contacts ui-form">
				<div class="row">
					<div class="col-md-6">
						<input id="user-name" type="text" name="user-name"
							placeholder="Full Name" required="required"
							class="form-control" />
						<input id="user-phone" type="tel" name="user-phone"
							placeholder="Phone" class="form-control" />
					</div>
					<div class="col-md-6">
						<input id="user-email" type="email" name="user-email"
							placeholder="Email" class="form-control" />
						<input id="user-subject" type="text" name="user-subject"
							placeholder="Event type"
							class="form-control last-block_mrg-btn_0" />
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<textarea id="user-message" rows="3"
							placeholder="Message ..." required="required"
							class="form-control"></textarea>
						<button class="btn btn-default">Send Message</button>
					</div>
				</div>
			</form>
			<!-- end .b-form-contact-->

		</section>
	</div>
</div>
@endsection