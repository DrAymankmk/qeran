@php
$contactSection = $homePage->activeSections->where('name', 'contact')->first();
@endphp
@if($contactSection)
<div class="block-table block-table-md">
	<div class="block-table__cell col-md-6">
		<section class="section-form-contact section-form-contact_color_white bg-primary">
			<div class="ui-decor-1"><img
					src="{{ asset('frontend/assets/media/general/ui-decor-1_wh.png') }}"
					alt="decor">
			</div>
			<h2 class="ui-title-block"><span>{{ $contactSection->title }}</span></h2>
			<div class="ui-subtitle-block">{{ $contactSection->subtitle }}</div>
			<div id="success"></div>
			<form id="contactForm" action="#" method="post" class="b-form-contacts ui-form">
				<div class="row">
					<div class="col-md-6">
						<input id="user-name" type="text" name="user-name"
							placeholder="{{ __('frontend.name') }}"
							required="required" class="form-control" />
						<input id="user-phone" type="tel" name="user-phone"
							placeholder="{{ __('frontend.phone') }}"
							class="form-control" />
					</div>
					<div class="col-md-6">
						<input id="user-email" type="email" name="user-email"
							placeholder="{{ __('frontend.email') }}"
							class="form-control" />
						<input id="user-subject" type="text" name="user-subject"
							placeholder="{{ __('frontend.subject') }}"
							class="form-control last-block_mrg-btn_0" />
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<textarea id="user-message" rows="3"
							placeholder="{{ __('frontend.message') }}"
							required="required"
							class="form-control"></textarea>
						<button
							class="btn btn-default">{{ __('frontend.send') }}</button>
					</div>
				</div>
			</form>
			<!-- end .b-form-contact-->

		</section>
	</div>
	@if($contactSection->image)
	<div class="block-table__cell col-md-6"><img src="{{ $contactSection->image->getUrl() }}" alt="foto"></div>
	@else
	<div class="block-table__cell col-md-6"><img src="{{ asset('frontend/assets/media/content/960x750/2.jpg') }}"
			alt="foto"></div>
	@endif

</div>

@endif
