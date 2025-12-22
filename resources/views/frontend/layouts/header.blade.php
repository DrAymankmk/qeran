<header
	class="header header-boxed-width header-background-trans header-logo-black header-topbarbox-1-left header-topbarbox-2-right header-navibox-1-left header-navibox-2-right header-navibox-3-right header-navibox-4-right">
	<div class="top-bar">
		<div class="container container-boxed-width">
			<div class="container">
				<div class="header-topbarbox-1">
					<ul class="top-bar-contact">
						<li class="top-bar-contact__item"><i
								class="icon icon-call-in"></i>
							(+01) 123 456 7899</li>
						<li class="top-bar-contact__item"><i
								class="icon icon-envelope-open"></i>
							Contact [at] Dvents.com</li>
						<li class="top-bar-contact__item"><i
								class="icon icon-clock"></i>
							Mon – Fri 9.00 am – 6.00 pm
						</li>
					</ul>
				</div>
				<div class="header-topbarbox-2">
					@php
					$headerSection = \App\Models\CmsPage::where('slug', 'general')
					->where('is_active', true)
					->with(['activeSections' => function($query) {
					$query->where('name', 'header')
					->where('is_active', true)
					->with(['links' => function($q) {
					$q->where('is_active', true)->orderBy('order');
					}]);
					}])
					->first()
					?->activeSections
					->where('name', 'header')
					->first();
					@endphp
					@if($headerSection && $headerSection->links &&
					$headerSection->links->count() > 0)
					<ul class="social-net list-inline">
						@foreach($headerSection->links as $link)
						<li class="social-net__item">
							<a href="{{ $link->url }}"
								target="{{ $link->target }}"
								class="social-net__link text-primary_h"
								rel="{{ $link->target === '_blank' ? 'noopener noreferrer' : '' }}">
								@if($link->icon)
								{!! $link->icon_html !!}
								@else
								<i class="fa fa-link"></i>
								@endif
							</a>
						</li>
						@endforeach
					</ul>
					@else
					{{-- Fallback to default social links if no header section found --}}
					<ul class="social-net list-inline">
						<li class="social-net__item"><a href="twitter.com"
								class="social-net__link text-primary_h"><i
									class="fa-brands fa-twitter"></i></a>
						</li>
						<li class="social-net__item"><a href="facebook.com"
								class="social-net__link text-primary_h"><i
									class="fa-brands fa-facebook"></i></a>
						</li>
						<li class="social-net__item"><a href="plus.google.com"
								class="social-net__link text-primary_h"><i
									class="fa-brands fa-google"></i></a>
						</li>
						<li class="social-net__item"><a href="linkedin.com"
								class="social-net__link text-primary_h"><i
									class="fa-brands fa-linkedin"></i></a>
						</li>
					</ul>
					@endif
					<!-- end social-list-->
				</div>
			</div>
		</div>
	</div>
	<div class="container container-boxed-width">
		<nav id="nav" class="navbar">
			<!-- <div class="container"> -->
			<div class="header-navibox-1">
				<!-- Mobile Trigger Start-->
				<button
					class="menu-mobile-button visible-xs-block js-toggle-mobile-slidebar toggle-menu-button"><i
						class="toggle-menu-button-icon"><span></span><span></span><span></span><span></span><span></span><span></span></i></button>
				<!-- Mobile Trigger End-->
				@php
				$logoSetting = \App\Models\HubFile::where('original_name', 'logo_img')->first();
				$logoUrl = $logoSetting && $logoSetting->path ? $logoSetting->get_path() :
				asset('frontend/assets/media/logo.png');
				@endphp
				<a href="{{ route('home') }}" class="navbar-brand scroll"><img
						src="{{ $logoUrl }}" style="height:41px; width:176px"
						alt="logo" class="normal-logo" /><img src="{{ $logoUrl }}"
						style="height:41px; width:176px" alt="logo"
						class="scroll-logo hidden-xs" /></a>
			</div>
			<div class="header-navibox-2">
				<ul class="main-menu nav navbar-nav" style="display: flex; align-items: center">

					<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
					</li>
					<li><a href="{{route('services')}}">{{ __('frontend.services') }}</a>
					</li>
					<li><a href="{{ route('about') }}">{{ __('frontend.about') }}</a></li>

					<li><a href="{{ route('faq') }}">{{ __('frontend.faq') }}</a></li>


					<li><a href="{{ route('contact') }}">{{ __('frontend.contact') }}</a>
					</li>

					<li class="dropdown">
						<a href="#" data-toggle="dropdown" class="dropdown-toggle">
							@if(app()->getLocale()
							== 'ar')
							<img src="{{ asset('admin_assets/images/flags/saudi.png') }}"
								alt="Arabic"
								style="width: 20px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 5px;">
							العربية
							@else
							<img src="{{ asset('admin_assets/images/flags/us.jpg') }}"
								alt="English"
								style="width: 20px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 5px;">
							English
							@endif
							<b class="caret"></b>
						</a>
						<ul role="menu" class="dropdown-menu">
							@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
							<li>
								<a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
									rel="alternate"
									hreflang="{{ $localeCode }}">
									@if($localeCode
									==
									'ar')
									<img src="{{ asset('admin_assets/images/flags/saudi.png') }}"
										alt="Arabic"
										style="width: 20px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 5px;">
									@else
									<img src="{{ asset('admin_assets/images/flags/us.jpg') }}"
										alt="English"
										style="width: 20px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 5px;">
									@endif
									{{ $properties['native'] }}
								</a>
							</li>
							@endforeach
						</ul>
					</li>
				</ul>
			</div>
			<div class="header-navibox-3">
				<ul class="nav navbar-nav hidden-xs clearfix vcenter">
					<li>
						<button class="js-toggle-screen toggle-menu-button"><i
								class="toggle-menu-button-icon"><span></span><span></span><span></span><span></span><span></span><span></span></i></button>
					</li>
					<!-- <li><a href="#" class="btn_header_search"><i
								class="fas fa-search"></i></a>
					</li> -->
				</ul>
			</div>

			<!-- </div> -->
		</nav>
	</div>
</header>