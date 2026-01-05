@extends('frontend.layouts.app')

@push('styles')
@include('frontend.partials.responsive-title-page')
<style>
/* Services Page Responsive Styles */
@media (max-width: 767px) {
	.b-title-page {
		padding: 40px 0 !important;
		min-height: auto !important;
	}
	
	.b-title-page__title {
		font-size: 1.75rem !important;
	}
	
	.section-advantages .col-md-4,
	.section-advantages .col-sm-6 {
		margin-bottom: 30px;
	}
	
	.b-advantages {
		padding: 20px 15px;
		text-align: center;
	}
	
	.b-advantages__icon {
		font-size: 2.5rem;
		margin-bottom: 15px;
	}
	
	.b-advantages__title {
		font-size: 1.25rem;
		margin-bottom: 10px;
	}
	
	.b-pricing {
		margin-bottom: 30px;
		padding: 25px 20px !important;
	}
	
	.b-pricing__title {
		font-size: 1.5rem;
	}
	
	.b-pricing-price__number {
		font-size: 2.5rem;
	}
}

@media (max-width: 480px) {
	.b-title-page__title {
		font-size: 1.5rem !important;
	}
	
	.section-advantages .col-sm-6 {
		width: 100%;
	}
	
	.b-advantages {
		padding: 15px 10px;
	}
	
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

@section('content')


@include('frontend.pages.services.partials.features-section')

@include('frontend.pages.home.partials.info-section')

@include('frontend.pages.home.partials.services-section')

@include('frontend.pages.home.partials.why-choose-us-section')

@include('frontend.pages.home.partials.guard-app-section')

@include('frontend.pages.services.partials.packages-section')

@include('frontend.pages.home.partials.contact-section')


@endsection