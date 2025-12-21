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
		.fa, .fas, .far, .fal, .fab {
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
		.fa.icon, .fas.icon, .far.icon, .fab.icon {
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
		<div data-off-canvas="mobile-slidebar left overlay">
			<ul class="nav navbar-nav">
				<li><a href="home.html">Home</a></li>
				<li><a href="services.html">Services</a></li>
				<li><a href="home.html">Works</a></li>
				<li><a href="about.html">About</a></li>
				<li><a href="blog-main.html">News</a></li>
				<li><a href="contact.html">Contact</a></li>
			</ul>
		</div>
		<!-- ==========================-->
		<!-- FULL SCREEN MENU-->
		<!-- ==========================-->
		<div class="wrap-fixed-menu" id="fixedMenu">
			<nav class="fullscreen-center-menu">

				<div class="menu-main-menu-container">

					<ul class="nav navbar-nav">
						<li><a href="home.html">Home</a></li>
						<li><a href="services.html">Services</a></li>
						<li><a href="home.html">Works</a></li>
						<li><a href="about.html">About</a></li>
						<li><a href="blog-main.html">News</a></li>
						<li><a href="contact.html">Contact</a></li>
					</ul>



				</div>
			</nav>
			<button type="button" class="fullmenu-close"><i class="fas fa-times"></i></button>
		</div>

		@include('frontend.layouts.header')

		<!-- end .header-->




		@yield('content')

		<footer class="footer">
			<div class="footer__main">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<div class="text-center"><a href="home.html"
									class="footer__logo"><img
										src="{{ asset('frontend/assets/media/general/logo.png') }}"
										alt="Logo"
										class="img-responsive" /></a>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<form class="footer-form">
								<div class="row">
									<div class="col-sm-5">
										<h3
											class="footer-form__title">
											Get the
											FREE
											Newsletter
										</h3>
										<div
											class="footer-form__info">
											Sign up to
											get the
											updates
											about new
											events
										</div>
									</div>
									<div class="col-sm-7">
										<div
											class="form-group">
											<input type="email"
												placeholder="Your email address ..."
												class="footer-form__input" />
											<button
												class="footer-form__btn form-control-feedback"><i
													class="icon icon-envelope-open text-primary_h"></i></button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="footer-section">
								<h3
									class="footer-section__title ui-title-inner">
									<i
										class="ui-decor-2 bg-primary"></i>
									About Dvents
								</h3>
								<div class="footer-section__subtitle">
									The Events Specialists!</div>
								<div class="footer__info">
									<p>Aorem ipsum dolor sit amet
										elit sed lum tempor
										incididunt ut labore
										el dolore alg minim
										veniam quis nostrud
										lorem psum dolor sit
										amet sed incididunt.
									</p>
								</div><a href="about.html"
									class="btn btn-default btn-xs"><i
										class="icon"></i>
									Read More</a>
							</div>
						</div>
						<div class="col-md-3">
							<section class="footer-section">
								<h3
									class="footer-section__title ui-title-inner">
									<i
										class="ui-decor-2 bg-primary"></i>
									Keep In Touch
								</h3>
								<div class="footer__contact"><i
										class="icon icon-map"></i>
									38-2 Hilton Street,
									California, USA</div>
								<div class="footer__contact"><i
										class="icon icon-call-in"></i>
									(+01) 123 456 7890</div>
								<div class="footer__contact"><i
										class="icon icon-envelope-open"></i>
									info@dvents.org</div>
								<div class="footer__contact"><i
										class="icon icon-clock"></i>
									Mon - Fri 9.00 am - 6.00 pm
								</div>
							</section>
						</div>
						<div class="col-md-3">
							<section class="footer-section">
								<h3
									class="footer-section__title ui-title-inner">
									<i
										class="ui-decor-2 bg-primary"></i>
									Events Gallery
								</h3>
								<ul
									class="footer-gallery list-unstyled js-zoom-gallery clearfix">
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-1.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-1.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-2.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-2.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-3.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-3.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-4.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-4.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-5.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-5.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
									<li
										class="footer-gallery__item">
										<a href="{{ asset('frontend/assets/media/components/footer/gallery-6.jpg') }}"
											class="footer-gallery__link js-zoom-gallery__item"><img
												src="{{ asset('frontend/assets/media/components/footer/gallery-6.jpg') }}"
												alt="foto"
												class="img-responsive" /></a>
									</li>
								</ul>
							</section>
						</div>
						<div class="col-md-3">
							<section class="footer-section">
								<h3
									class="footer-section__title ui-title-inner">
									<i
										class="ui-decor-2 bg-primary"></i>
									Quick Links
								</h3>
								<ul
									class="footer-list list list-mark-4 list-unstyled">
									<li class="footer-list__item">
										<a href="services.html"
											class="footer-list__link">Our
											Services</a>
									</li>
									<li class="footer-list__item">
										<a href="home.html"
											class="footer-list__link">Our
											Team</a>
									</li>
									<li class="footer-list__item">
										<a href="about.html"
											class="footer-list__link">About
											Dvents</a>
									</li>
									<li class="footer-list__item">
										<a href="home.html"
											class="footer-list__link">Clients
											List</a>
									</li>
									<li class="footer-list__item">
										<a href="blog-main.html"
											class="footer-list__link">News
											Blog</a>
									</li>
									<li class="footer-list__item">
										<a href="assets/downloads/doc-2.pdf"
											class="footer-list__link">Brochure</a>
									</li>
									<li class="footer-list__item">
										<a href="contact.html"
											class="footer-list__link">Get
											In
											Touch</a>
									</li>
								</ul>
							</section>
						</div>
					</div>
				</div>
			</div>
			<div class="footer__bottom">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<div class="copyright pull-left">© 2016<strong>
									Dvents</strong> - The Events
								Specialists All Rights Reserved.<a
									href="terms-of-use.html"
									class="copyright__link"> Terms
									of Use</a><a
									href="privacy-policy.html"
									class="copyright__link">Privacy
									Policy</a></div>
							<ul class="social-net list-inline pull-right">
								<li class="social-net__item"><a
										href="youtube.com"
										class="social-net__link text-primary_h"><i
											class="fa-brands fa-youtube"></i></a>
								</li>
								<li class="social-net__item"><a
										href="twitter.com"
										class="social-net__link text-primary_h"><i
											class="fa-brands fa-twitter"></i></a>
								</li>
								<li class="social-net__item"><a
										href="facebook.com"
										class="social-net__link text-primary_h"><i
											class="fa-brands fa-facebook"></i></a>
								</li>
								<li class="social-net__item"><a
										href="plus.google.com"
										class="social-net__link text-primary_h"><i
											class="fa-brands fa-google"></i></a>
								</li>
								<li class="social-net__item"><a
										href="instagram.com"
										class="social-net__link text-primary_h"><i
											class="fa-brands fa-instagram"></i></a>
								</li>
							</ul>
							<!-- end social-list-->
						</div>
					</div>
				</div>
			</div>
		</footer>
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
	<script src="{{ asset('frontend/assets/plugins/contact_me.js') }}"></script>
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
