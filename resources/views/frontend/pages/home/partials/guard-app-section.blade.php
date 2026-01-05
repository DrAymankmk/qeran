@php
$guardAppSection = $homePage->activeSections->where('name', 'guard-application')->first();
$settings = $guardAppSection->settings ?? [];
$images = $settings['images'] ?? [];
$hasImages = !empty($images) && is_array($images) && count($images) > 0;
@endphp
@push('styles')
<style>
/* Guard App Section Responsive */
@media (max-width: 767px) {
	.section-events {
		padding: 30px 0;
	}
	
	.section-events .ui-title-block {
		font-size: 1.75rem !important;
	}
	
	.b-events-2 {
		margin-bottom: 20px;
	}
	
	.b-events-2__media img {
		width: 100%;
		height: auto;
	}
	
	.enable-owl-carousel {
		padding: 0 15px;
	}
}

@media (max-width: 480px) {
	.section-events {
		padding: 25px 0;
	}
	
	.section-events .ui-title-block {
		font-size: 1.5rem !important;
	}
	
	.enable-owl-carousel {
		padding: 0 10px;
	}
}
</style>
@endpush
@if($hasImages && $guardAppSection)
<div class="section-events">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">{{ $guardAppSection->title ?? '' }}</h2>
					<div class="ui-subtitle-block">{{ $guardAppSection->subtitle ?? '' }}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div data-min480="1" data-min768="3" data-min992="4" data-min1200="4"
					data-pagination="false" data-navigation="false" data-auto-play="4000"
					data-stop-on-hover="true"
					class="owl-carousel owl-theme enable-owl-carousel">
					@foreach($images as $image)
					<section class="b-events-2 text-center">
						<div class="b-events-2__media"><img src="{{ $image }}"
								alt="{{ $guardAppSection->title ?? 'Guard App' }}"
								class="img-responsive" />

						</div>
						<div class="ui-decor-2 ui-decor-2_vert bg-primary">
						</div>

					</section>
					@endforeach

				</div>
				<!-- end b-events-->
			</div>
		</div>
	</div>
</div>
@endif
