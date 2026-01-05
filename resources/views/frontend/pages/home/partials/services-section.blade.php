@push('styles')
<style>
/* Services Section Responsive */
@media (max-width: 991px) {
	.b-services .col-md-5,
	.b-services .col-md-7 {
		margin-bottom: 30px;
	}
}

@media (max-width: 767px) {
	.b-services {
		padding: 40px 0 !important;
	}

	.b-services .col-md-5 {
		text-align: center;
		margin-bottom: 25px;
	}

	.b-services .col-md-7 {
		margin-bottom: 0;
	}

	.b-services .ui-title-block {
		font-size: 1.75rem !important;
	}

	.b-services .ui-subtitle-block {
		font-size: 1rem !important;
	}

	.b-advantages-2 {
		margin-bottom: 25px;
		padding: 20px 15px;
		text-align: center;
	}

	.b-advantages-2__icon {
		font-size: 2.5rem;
		margin-bottom: 15px;
	}

	.b-advantages-2__title {
		font-size: 1.25rem;
		margin-bottom: 10px;
	}

	.b-advantages-2__info {
		font-size: 0.95rem;
		line-height: 1.7;
	}
}

@media (max-width: 480px) {
	.b-services {
		padding: 30px 0 !important;
	}

	.b-services .ui-title-block {
		font-size: 1.5rem !important;
	}

	.b-services .ui-decor-1 {
		text-align: center;
		margin-bottom: 15px;
	}

	.b-services .ui-decor-1 img {
		max-width: 80px;
		height: auto;
	}

	.b-advantages-2 {
		padding: 15px 10px;
	}

	.b-advantages-2__icon {
		font-size: 2rem;
	}

	.b-advantages-2__title {
		font-size: 1.1rem;
	}

	.bxslider {
		margin: 0;
	}
}
</style>
@endpush
<section class="b-services area-bg area-bg_dark area-bg_op_90 parallax">
	<div class="area-bg__inner">
		<div class="container">
			@php
			$servicesSection = $homePage->activeSections->where('name', 'services')->first();
			@endphp
			<div class="row">
				<div class="col-md-5 col-sm-12 col-xs-12">
					<div class="ui-decor-1"><img
							src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
							alt="decor" /></div>
					<h2 class="ui-title-block">{{ $servicesSection->title }}</h2>
					<div class="ui-subtitle-block">{{ $servicesSection->subtitle }}</div>
					{!! formatCmsContent($servicesSection->description) !!}
				</div>
				<div class="col-md-7 col-sm-12 col-xs-12">
					<div class="bxslider">
						@foreach($servicesSection->items as $item)
						<section class="b-advantages-2 b-advantages-2_light">
							@if($item->icon)
							@php
							$iconClass = trim($item->icon);
							@endphp
							<i
								class="b-advantages-2__icon text-primary {{ $iconClass }}"></i>
							@endif
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									{{ $item->title }}
								</h3>
								<div class="b-advantages-2__info">
									{!!
									formatCmsContent($item->content)
									!!}
								</div>
							</div>
						</section>
						@endforeach


						<!-- end .b-advantages-->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
