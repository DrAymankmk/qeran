@php
$heroSection = $homePage->activeSections->where('name', 'hero')->first();
@endphp
@if($heroSection->items->count() > 0)
<div id="main-slider" data-slider-width="100%" data-slider-height="950px" data-slider-arrows="true"
	data-slider-buttons="false" class="main-slider main-slider_mod-a slider-pro">
	<div class="sp-slides">

		@foreach($heroSection->items as $item)
		<div class="sp-slide her-slider"><img src="{{ $item->images->first()->getUrl() }}" alt="slider"
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