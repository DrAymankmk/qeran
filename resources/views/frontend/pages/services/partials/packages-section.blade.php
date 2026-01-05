@php
$packagesSection = $servicesPage->activeSections->where('name', 'packages')->first();
@endphp
@push('styles')
<style>
/* Packages Section Responsive */
@media (max-width: 991px) {
	.b-pricing {
		margin-bottom: 30px;
	}
}

@media (max-width: 767px) {
	.b-pricing {
		margin-bottom: 30px;
		padding: 25px 20px !important;
	}
	
	.b-pricing__title {
		font-size: 1.5rem;
		margin-bottom: 10px;
	}
	
	.b-pricing__subtitle {
		font-size: 0.95rem;
		margin-bottom: 15px;
	}
	
	.b-pricing-price {
		margin: 20px 0;
	}
	
	.b-pricing-price__title {
		font-size: 0.9rem;
	}
	
	.b-pricing-price__number {
		font-size: 2.5rem;
	}
	
	.b-pricing__btn {
		width: 100%;
		margin-top: 20px;
	}
}

@media (max-width: 480px) {
	.b-pricing {
		padding: 20px 15px !important;
	}
	
	.b-pricing__title {
		font-size: 1.25rem;
	}
	
	.b-pricing-price__number {
		font-size: 2rem;
	}
}
</style>
@endpush
<section class="section-default">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor"></div>
				<h2 class="ui-title-block">{{ $packagesSection->title }}</h2>
				<div class="ui-subtitle-block">{{ $packagesSection->subtitle }}</div>
			</div>
		</div>
		<div class="row">
			@foreach($packages as $package)
			<div class="col-md-4">
				<section class="b-pricing">
					<h3 class="b-pricing__title">{{ $package->title }}</h3>
					<div class="b-pricing__subtitle">{{ $package->subtitle }}</div>
					<div class="b-pricing-price"><span
							class="b-pricing-price__title">Starts from</span>
						$<span
							class="b-pricing-price__number">{{ $package->price }}</span>
					</div>
					{!! formatCmsContent($package->content) !!}
					<!-- <a href="home.html" class="b-pricing__btn btn btn-default">order
						now</a> -->
				</section>
				<!-- end .b-pricing-->
			</div>
			@endforeach
		</div>
	</div>
</section>