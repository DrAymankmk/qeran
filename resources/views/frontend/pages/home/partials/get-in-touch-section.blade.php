<div class="section-default">
	<style>
	.get-in-touch-section {
		background-size: cover !important;
		background-position: center !important;
		background-repeat: no-repeat !important;
		position: relative;
		min-height: 300px;
	}

	.get-in-touch-section::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #c5b076;
		opacity: 0.65;
		border-radius: inherit;
		z-index: 0;
	}

	.get-in-touch-section>* {
		position: relative;
		z-index: 1;
	}
	</style>
	<div class="row">
		<div class="col-md-12">
			<section data-stellar-background-ratio="0.4"
				class="b-info section-texture section-radius stellar section-texture_green section-radius get-in-touch-section">
				<h2 class="b-info__title">{{ __('frontend.get_in_touch') }}</h2>
				<div class="b-info__text">{{ __('frontend.get_in_touch_text') }}</div><a
					href="{{ route('contact') }}"
					class="b-info__btn btn btn-default btn-sm btn-effect">{{ __('frontend.contact_us') }}</a>
			</section>
			<!-- end b-info-->
		</div>

	</div>
</div>