<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<title>Qeran</title>
	<meta content="" name="description" />
	<meta content="" name="keywords" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta content="telephone=no" name="format-detection" />
	<meta name="HandheldFriendly" content="true" />
	@if(app()->getLocale() == 'ar')
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/ar/master.css') }}" />
	@else
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/en/master.css') }}" />
	@endif
	<!-- CMS Content Styling -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/cms-content.css') }}" />
	<!-- Font Awesome 6 for CMS Icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<style>
	/* Ensure Font Awesome icons display correctly */
	.fa,
	.fas,
	.far,
	.fal,
	.fab {
		font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
		font-weight: 900;
		-webkit-font-smoothing: antialiased;
		display: inline-block;
		font-style: normal;
		font-variant: normal;
		text-rendering: auto;
		line-height: 1;
	}

	.fab {
		font-family: "Font Awesome 6 Brands" !important;
		font-weight: 400;
	}

	.far {
		font-weight: 400;
	}

	/* Prevent icon class from overriding Font Awesome */
	.fa.icon,
	.fas.icon,
	.far.icon,
	.fab.icon {
		font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
	}
	</style>
	<!-- Google Fonts - Almarai for Arabic -->
	<!-- @if(app()->getLocale() == 'ar')
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap"
		rel="stylesheet">
	@endif -->
	<!-- SWITCHER-->
	<link href="{{ asset('frontend/assets/plugins/switcher/css/switcher.css') }}" rel="stylesheet"
		id="switcher-css" />
	<link href="{{ asset('frontend/assets/plugins/switcher/css/color1.css') }}" rel="alternate stylesheet"
		title="color1" />
	<link href="{{ asset('frontend/assets/plugins/switcher/css/color2.css') }}" rel="alternate stylesheet"
		title="color2" />
	<link href="{{ asset('frontend/assets/plugins/switcher/css/color3.css') }}" rel="alternate stylesheet"
		title="color3" />
	<link href="{{ asset('frontend/assets/plugins/switcher/css/color4.css') }}" rel="alternate stylesheet"
		title="color4" />
	<link href="{{ asset('frontend/assets/plugins/switcher/css/color5.css') }}" rel="alternate stylesheet"
		title="color5" />
	<link rel="icon" type="image/x-icon" href="{{ asset('frontend/favicon.ico') }}" />


	<!-- Swiper CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

	<style>
	/* Mobile Sidebar Contact and Social Links Styles */
	.mobile-sidebar-contact,
	.mobile-sidebar-social {
		display: none;
	}

	@media (max-width: 767px) {
		.mobile-sidebar-contact,
		.mobile-sidebar-social {
			display: block;
		}

		.mobile-social-link:hover {
			background: rgba(255,255,255,0.2) !important;
			transform: scale(1.1);
		}

		.mobile-contact-list li,
		.mobile-social-list {
			animation: fadeInUp 0.3s ease-out;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
	}
	</style>

	<style>
	/* Hide top bar on small devices */
	@media (max-width: 767px) {
		/* .top-bar {
			display: none !important;
		} */

		.top-bar-contact {
			flex-direction: column !important;
			align-items: flex-start !important;
			gap: 10px !important;
		}
	}
	

	/* Make contact list column on small devices */
	@media (max-width: 991px) {
		.top-bar-contact {
			flex-direction: column !important;
			align-items: flex-start !important;
			gap: 10px !important;
		}

		.top-bar-contact li {
			width: 100%;
			margin-bottom: 5px;
		}
	}

	@media (max-width: 480px) {
		.top-bar-contact {
			gap: 8px !important;
		}
		.top-bar-contact {
			flex-direction: column !important;
			align-items: flex-start !important;
			gap: 10px !important;
		}


		.top-bar-contact li {
			font-size: 13px;
			justify-content: center;

			
		}
		.footer__bottom-content {
			text-align: center;
		}
		.navbar-brand {
			margin-right:30px;
		}
	}
	</style>
	@stack('styles')
</head>

<body>
	<!-- Loader-->
	<div id="page-preloader"><span class="spinner border-t_second_b border-t_prim_a"></span></div>
	<!-- Loader end-->

	<div data-header="sticky" data-header-top="200" data-canvas="container" class="l-theme animated-css">
		<!-- Start Switcher-->
		<div class="switcher-wrapper">
			<div class="demo_changer">
				<div class="demo-icon text-primary"><i class="fas fa-cog fa-spin fa-2x"></i>
				</div>
				<div class="form_holder">
					<div class="predefined_styles">
						<div class="skin-theme-switcher">
							<h4>Color</h4><a href="javascript:void(0);"
								data-switchcolor="color1"
								style="background-color:#fe3e01;"
								class="styleswitch"></a><a
								href="javascript:void(0);"
								data-switchcolor="color2"
								style="background-color:#FFAC3A;"
								class="styleswitch"></a><a
								href="javascript:void(0);"
								data-switchcolor="color3"
								style="background-color:#28af0f;"
								class="styleswitch"></a><a
								href="javascript:void(0);"
								data-switchcolor="color4"
								style="background-color:#e425e9;"
								class="styleswitch"></a><a
								href="javascript:void(0);"
								data-switchcolor="color5"
								style="background-color:#0c02bd;"
								class="styleswitch"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end switcher-->
		<!-- ==========================-->
		<!-- SEARCH MODAL-->
		<!-- ==========================-->
		<div class="header-search open-search">
			<div class="container">
				<div class="row">
					<div class="col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
						<div class="navbar-search">
							<form class="search-global">
								<input type="text"
									placeholder="Type to search"
									autocomplete="off" name="s"
									value=""
									class="search-global__input" />
								<button class="search-global__btn"><i
										class="icon stroke icon-Search"></i></button>
								<div class="search-global__note">Begin
									typing your search above and
									press return to search.</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<button type="button" class="search-close close"><i class="fas fa-times"></i></button>
		</div>
		<!-- ==========================-->
		<!-- MOBILE MENU-->
		<!-- ==========================-->
		@php
		$headerSection = \App\Models\CmsPage::where('slug', 'general')
		->where('is_active', true)
		->with(['activeSections' => function($query) {
		$query->where('name', 'header')
		->where('is_active', true)
		->with([
		'activeItems' => function($q) {
		$q->where('is_active', true)->orderBy('order');
		},
		'links' => function($q) {
		$q->where('is_active', true)->orderBy('order');
		}
		]);
		}])
		->first()
		?->activeSections
		->where('name', 'header')
		->first();
		@endphp
		<div data-off-canvas="mobile-slidebar left overlay">
			<ul class="nav navbar-nav">
				<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a></li>
				<li><a href="{{ route('services') }}">{{ __('frontend.services') }}</a></li>
				<li><a href="{{ route('about') }}">{{ __('frontend.about') }}</a></li>
				<li><a href="{{ route('faq') }}">{{ __('frontend.faq') }}</a></li>
				<li><a href="{{ route('contact') }}">{{ __('frontend.contact') }}</a></li>
			</ul>

		
		</div>
		<!-- ==========================-->
		<!-- FULL SCREEN MENU-->
		<!-- ==========================-->
		<div class="wrap-fixed-menu" id="fixedMenu">
			<nav class="fullscreen-center-menu">

				<div class="menu-main-menu-container">

					<ul class="nav navbar-nav">
						<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
						</li>
						<li><a href="{{ route('services') }}">{{ __('frontend.services') }}</a>
						</li>
						<li><a href="{{ route('about') }}">{{ __('frontend.about') }}</a>
						</li>
						<li><a href="{{ route('faq') }}">{{ __('frontend.faq') }}</a>
						</li>
						<li><a href="{{ route('contact') }}">{{ __('frontend.contact') }}</a>
						</li>
					</ul>



				</div>
			</nav>
			<button type="button" class="fullmenu-close"><i class="fas fa-times"></i></button>
		</div>

		@include('frontend.layouts.header')

		<!-- end .header-->




		@yield('content')

		@include('frontend.layouts.footer')
		<!-- .footer-->

	</div>
	<!-- end layout-theme-->


	<!-- ++++++++++++-->
	<!-- MAIN SCRIPTS-->
	<!-- ++++++++++++-->
	<script src="{{ asset('frontend/assets/libs/jquery-1.12.4.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/libs/jquery-migrate-1.2.1.js') }}"></script>
	<!-- Bootstrap-->
	<script src="{{ asset('frontend/assets/libs/bootstrap/bootstrap.min.js') }}"></script>
	<!-- User customization-->
	<script src="{{ asset('frontend/assets/js/custom.js') }}"></script>
	<!---->
	<!-- Color scheme-->
	<script src="{{ asset('frontend/assets/plugins/switcher/js/dmss.js') }}"></script>
	<!-- Select customization & Color scheme-->
	<script src="{{ asset('frontend/assets/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
	<!-- Slider 1-->
	<script src="{{ asset('frontend/assets/plugins/owl-carousel/owl.carousel.min.js') }}"></script>
	<!-- Slider 2-->
	<script src="{{ asset('frontend/assets/plugins/bxslider/vendor/jquery.easing.1.3.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/bxslider/vendor/jquery.fitvids.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/bxslider/jquery.bxslider.min.js') }}"></script>
	<!-- Pop-up window-->
	<script src="{{ asset('frontend/assets/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
	<!-- Headers scripts-->
	<script src="{{ asset('frontend/assets/plugins/headers/slidebar.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/headers/header.js') }}"></script>
	<!-- Mail scripts-->
	<script src="{{ asset('frontend/assets/plugins/jqBootstrapValidation.js') }}"></script>
	<!-- <script src="{{ asset('frontend/assets/plugins/contact_me.js') }}"></script> -->
	<!-- Parallax-->
	<script src="{{ asset('frontend/assets/plugins/stellar/jquery.stellar.min.js') }}"></script>
	<!-- Filter and sorting images-->
	<script src="{{ asset('frontend/assets/plugins/isotope/isotope.pkgd.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/isotope/imagesLoaded.js') }}"></script>
	<!-- Progress numbers-->
	<script src="{{ asset('frontend/assets/plugins/rendro-easy-pie-chart/jquery.easypiechart.min.js') }}">
	</script>
	<script src="{{ asset('frontend/assets/plugins/rendro-easy-pie-chart/waypoints.min.js') }}"></script>
	<!-- Animations-->
	<script src="{{ asset('frontend/assets/plugins/scrollreveal/scrollreveal.min.js') }}"></script>
	<!-- Main slider-->
	<script src="{{ asset('frontend/assets/plugins/slider-pro/jquery.sliderPro.min.js') }}"></script>

	<!-- Swiper JS -->
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	@stack('scripts')
</body>

</html>
