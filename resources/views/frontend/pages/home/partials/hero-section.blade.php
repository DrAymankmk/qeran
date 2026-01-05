@php
$heroSection = $homePage->activeSections->where('name', 'hero')->first();
@endphp
@push('styles')
<style>
/* Hero Section Responsive */
@media (max-width: 991px) {
	#main-slider {
		height: 600px !important;
	}
	
	.main-slider__title {
		font-size: 2rem !important;
	}
	
	.main-slider__info {
		font-size: 1.2rem !important;
	}
	
	.main-slider .col-md-8 {
		padding: 0 20px;
	}
}

@media (max-width: 767px) {
	#main-slider {
		height: 400px !important;
	}
	
	.main-slider__title {
		font-size: 1.5rem !important;
		line-height: 1.3;
		padding: 0 15px;
	}
	
	.main-slider__info {
		font-size: 1rem !important;
		padding: 0 15px;
		line-height: 1.4;
	}
	
	.main-slider .col-md-8 {
		width: 100%;
		padding: 0 15px;
	}
	
	.hero-slider-image {
		object-fit: cover;
		object-position: center;
	}
	
	.main-slider__decor {
		margin: 15px 0;
	}
}

@media (max-width: 480px) {
	#main-slider {
		height: 300px !important;
	}
	
	.main-slider__title {
		font-size: 1.25rem !important;
	}
	
	.main-slider__info {
		font-size: 0.9rem !important;
	}
	
	.main-slider .sp-layer {
		width: 100% !important;
	}
}
</style>
@endpush
@if($heroSection->items->count() > 0)
<div id="main-slider" data-slider-width="100%" data-slider-height="950px" data-slider-arrows="true"
	data-slider-buttons="false" class="main-slider main-slider_mod-a slider-pro">
	<div class="sp-slides">
		<!-- Slide 1-->
		@foreach($heroSection->items as $item)
		<div class="sp-slide"><img src="{{ $item->images->first()->getUrl() }}" alt="slider"
				class="sp-image hero-slider-image" />
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						<div data-width="100%" data-show-transition="left"
							data-hide-transition="left"
							data-show-duration="2000" data-show-delay="1200"
							data-hide-delay="400"
							class="main-slider__info sp-layer">
							{{ $item->title }}
						</div>
						<h2 data-width="100%" data-show-transition="left"
							data-hide-transition="left"
							data-show-duration="800" data-show-delay="400"
							data-hide-delay="400"
							class="main-slider__title sp-layer">
							{{ $item->sub_title }}
						</h2>
						<div data-width="100%" data-show-transition="left"
							data-hide-transition="left"
							data-show-duration="1200" data-show-delay="600"
							data-hide-delay="400" class="sp-layer">
							<div class="main-slider__decor bg-primary">
							</div>
						</div>
						<!-- <div data-width="100%" data-show-transition="left"
							data-hide-transition="left"
							data-show-duration="1200" data-show-delay="2000"
							data-hide-delay="400" class="sp-layer"><a
								href="services.html"
								class="main-slider__btn btn btn-default">our
								features</a></div> -->
					</div>
				</div>
			</div>
		</div>
		@endforeach

	</div>
</div>
<!-- end .main-slider-->
<!-- <div class="container">
	<div class="row">
		<div class="col-xs-12">
			<div class="section-area">
				<div class="b-request-estimate">
					<div class="b-request-estimate__info">Wedding
						Functions to Birthday Parties and
						Corporate Events to Musical Functions,
						We offer full Events Management
						Services!</div>
					<div class="b-request-estimate__title bg-primary">
						<span class="ui-decor-2"></span>request
						your event estimate
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->

@endif