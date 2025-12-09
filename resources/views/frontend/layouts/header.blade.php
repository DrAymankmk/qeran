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
					<ul class="social-net list-inline">
						<li class="social-net__item"><a href="twitter.com"
								class="social-net__link text-primary_h"><i
									class="icon fa fa-twitter"></i></a>
						</li>
						<li class="social-net__item"><a href="facebook.com"
								class="social-net__link text-primary_h"><i
									class="icon fa fa-facebook"></i></a>
						</li>
						<li class="social-net__item"><a href="plus.google.com"
								class="social-net__link text-primary_h"><i
									class="icon fa fa-google-plus"></i></a>
						</li>
						<li class="social-net__item"><a href="linkedin.com"
								class="social-net__link text-primary_h"><i
									class="icon fa fa-linkedin"></i></a>
						</li>
					</ul>
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
				<!-- Mobile Trigger End--><a href="home.html" class="navbar-brand scroll"><img
						src="{{ asset('frontend/assets/media/general/logo.png') }}"
						alt="logo" class="normal-logo" /><img
						src="{{ asset('frontend/assets/media/general/logo-dark.png') }}"
						alt="logo" class="scroll-logo hidden-xs" /></a>
			</div>
			<div class="header-navibox-2">
				<ul class="main-menu nav navbar-nav" style="display: flex; align-items: center">
					<!-- <li class="dropdown"><a href="#" data-toggle="dropdown"
							class="dropdown-toggle">{{ __('Home') }}<b
								class="caret"></b></a>
						<ul role="menu" class="dropdown-menu">
							<li><a href="home.html">Home
									ver
									01</a>
							</li>
							<li><a href="home-2.html">Home
									ver
									02</a>
							</li>
						</ul>
					</li> -->
					<li><a href="">{{ __('frontend.Home') }}</a>
					</li>
					<li><a href="services.html">{{ __('frontend.Services') }}</a>
					</li>
					<li><a href="about.html">{{ __('frontend.About') }}</a></li>
					<li class="dropdown"><a href="#" data-toggle="dropdown"
							class="dropdown-toggle">{{ __('frontend.News') }}<b
								class="caret"></b></a>
						<ul role="menu" class="dropdown-menu">
							<li><a href="blog-main.html">Blog
									main</a>
							</li>
							<li><a href="blog-post.html">Blog
									post</a>
							</li>
						</ul>
					</li>
					<li><a href="contact.html">{{ __('frontend.Contact') }}</a>
					</li>
					<li class="dropdown"><a href="#" data-toggle="dropdown"
							class="dropdown-toggle">{{ __('frontend.Pages') }}<b
								class="caret"></b></a>
						<ul role="menu" class="dropdown-menu">
							<li><a href="404.html">Page
									404</a>
							</li>
							<li><a href="headers.html">Headers</a>
							</li>
							<li><a href="typography.html">Typography</a>
							</li>
							<li><a href="privacy-policy.html">Privacy
									policy</a>
							</li>
							<li><a href="terms-of-use.html">Terms
									of
									use</a>
							</li>
						</ul>
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
					<li><a href="#" class="btn_header_search"><i
								class="fa fa-search"></i></a>
					</li>
				</ul>
			</div>

			<!-- </div> -->
		</nav>
	</div>
</header>