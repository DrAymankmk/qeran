@php
$servicesSection = $servicesPage->activeSections->where('name', 'services')->first();
@endphp
<div class="b-title-page area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="ui-decor-2 ui-decor-2_vert bg-primary"></div>
					<h1 class="b-title-page__title">{{ __('frontend.services') }}</h1>
					<ol class="breadcrumb">
						<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
						</li>
						<li class="active">{{ __('frontend.services') }}</li>
					</ol>
					<!-- end breadcrumb-->
				</div>
			</div>
		</div>
	</div>
</div>
<section class="section-advantages">
	<div class="container">

		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">{{ $servicesSection->title ?? '' }}</h2>
					<div class="ui-subtitle-block">{{ $servicesSection->subtitle ?? '' }}
					</div>
				</div>
			</div>
		</div>

		<div class="row {{ app()->getLocale() == 'ar' ? 'row-rtl' : '' }}"
		style="display: flex; flex-wrap: wrap;">
			@foreach($servicesSection->activeItems as $item)

			<div class="col-md-4 col-sm-6">
				<section class="b-advantages b-advantages-1">
					@if($item->icon)
					@php
					$iconClass = trim($item->icon);

					// Normalize Font Awesome icons to Font Awesome 6 format
					if (strpos($iconClass, 'fa-') !== false || strpos($iconClass, 'fas ')
					!== false || strpos($iconClass, 'far ') !== false ||
					strpos($iconClass, 'fab ') !== false) {
					// Convert old 'fa fa-' format to 'fas fa-'
					if (strpos($iconClass, 'fa fa-') === 0) {
					$iconClass = str_replace('fa fa-', 'fas fa-', $iconClass);
					}
					// If it starts with just 'fa-' add 'fas' prefix
					elseif (strpos($iconClass, 'fa-') === 0 &&
					!preg_match('/^(fas|far|fab)\s+fa-/', $iconClass)) {
					$iconClass = 'fas ' . $iconClass;
					}
					}
					@endphp
					<i class="b-advantages__icon text-primary {{ $iconClass }}"></i>
					@else
					<i class="b-advantages__icon text-primary icon-emoticon-smile"></i>
					@endif
					<div class="b-advantages__inner">
						<h3 class="b-advantages__title ui-title-inner">
							{{ $item->title }}
						</h3>
						<div class="b-advantages__info">{!!
							formatCmsContent($item->content) !!}</div>
					</div>
				</section>
				<!-- end .b-advantages-->

			</div>
			@endforeach

		</div>
	</div>
</section>

@push('styles')
<style>
/* Features Section Responsive */
@media (max-width: 991px) {
	.section-advantages .col-md-4,
	.section-advantages .col-sm-6 {
		margin-bottom: 30px;
	}
}

@media (max-width: 767px) {
	.section-advantages {
		padding: 30px 0;
	}
	
	.section-advantages .col-md-4,
	.section-advantages .col-sm-6 {
		width: 100%;
		margin-bottom: 25px;
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
	
	.b-advantages__info {
		font-size: 0.95rem;
		line-height: 1.7;
	}
}

@media (max-width: 480px) {
	.b-advantages {
		padding: 15px 10px;
	}
	
	.b-advantages__icon {
		font-size: 2rem;
	}
	
	.b-advantages__title {
		font-size: 1.1rem;
	}
	
	.b-advantages__info {
		font-size: 0.9rem;
	}
}

@if(app()->getLocale() == 'ar')
.section-advantages .row-rtl {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
}

.section-advantages .row-rtl>[class*="col-"] {
	float: none;
}
@endif
</style>
@endpush