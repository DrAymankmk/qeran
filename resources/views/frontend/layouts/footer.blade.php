<footer class="footer">
	<div class="footer__main">
		<div class="container">
				@php
				$logoSetting = \App\Models\HubFile::where('original_name', 'logo_img')->first();
				$logoUrl = $logoSetting && $logoSetting->path ? $logoSetting->get_path() :
				asset('frontend/assets/media/logo.png');
				@endphp
			<div class="row">
				<div class="col-xs-12">
					<div class="text-center"><a href="{{ route('home') }}"
							class="footer__logo"><img
								src="{{ $logoUrl }}"
								style="height:41px; width:176px"
								alt="Logo" class="img-responsive" /></a>
					</div>
				</div>
			</div>
			<!-- <div class="row">
				<div class="col-md-8 col-md-offset-2">
					<form class="footer-form">
						<div class="row">
							<div class="col-sm-5">
								<h3 class="footer-form__title">
									Get the
									FREE
									Newsletter
								</h3>
								<div class="footer-form__info">
									Sign up to
									get the
									updates
									about new
									events
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
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
			</div> -->
			<div class="row">
				<div class="col-md-3">
					<div class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
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
						</div><a href="about.html" class="btn btn-default btn-xs"><i
								class="icon"></i>
							Read More</a>
					</div>
				</div>
				<div class="col-md-3">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							Keep In Touch
						</h3>
						<div class="footer__contact"><i class="icon icon-map"></i>
							38-2 Hilton Street,
							California, USA</div>
						<div class="footer__contact"><i
								class="icon icon-call-in"></i>
							(+01) 123 456 7890</div>
						<div class="footer__contact"><i
								class="icon icon-envelope-open"></i>
							info@dvents.org</div>
						<div class="footer__contact"><i class="icon icon-clock"></i>
							Mon - Fri 9.00 am - 6.00 pm
						</div>
					</section>
				</div>
				<div class="col-md-3">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							Events Gallery
						</h3>
						<ul
							class="footer-gallery list-unstyled js-zoom-gallery clearfix">
							<li class="footer-gallery__item">
								<a href="{{ asset('frontend/assets/media/components/footer/gallery-1.jpg') }}"
									class="footer-gallery__link js-zoom-gallery__item"><img
										src="{{ asset('frontend/assets/media/components/footer/gallery-1.jpg') }}"
										alt="foto"
										class="img-responsive" /></a>
							</li>
							<li class="footer-gallery__item">
								<a href="{{ asset('frontend/assets/media/components/footer/gallery-2.jpg') }}"
									class="footer-gallery__link js-zoom-gallery__item"><img
										src="{{ asset('frontend/assets/media/components/footer/gallery-2.jpg') }}"
										alt="foto"
										class="img-responsive" /></a>
							</li>
							<li class="footer-gallery__item">
								<a href="{{ asset('frontend/assets/media/components/footer/gallery-3.jpg') }}"
									class="footer-gallery__link js-zoom-gallery__item"><img
										src="{{ asset('frontend/assets/media/components/footer/gallery-3.jpg') }}"
										alt="foto"
										class="img-responsive" /></a>
							</li>
							<li class="footer-gallery__item">
								<a href="{{ asset('frontend/assets/media/components/footer/gallery-4.jpg') }}"
									class="footer-gallery__link js-zoom-gallery__item"><img
										src="{{ asset('frontend/assets/media/components/footer/gallery-4.jpg') }}"
										alt="foto"
										class="img-responsive" /></a>
							</li>
							<li class="footer-gallery__item">
								<a href="{{ asset('frontend/assets/media/components/footer/gallery-5.jpg') }}"
									class="footer-gallery__link js-zoom-gallery__item"><img
										src="{{ asset('frontend/assets/media/components/footer/gallery-5.jpg') }}"
										alt="foto"
										class="img-responsive" /></a>
							</li>
							<li class="footer-gallery__item">
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
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							Quick Links
						</h3>
						<ul class="footer-list list list-mark-4 list-unstyled">
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
						Specialists All Rights Reserved.<a href="terms-of-use.html"
							class="copyright__link"> Terms
							of Use</a><a href="privacy-policy.html"
							class="copyright__link">Privacy
							Policy</a></div>
					<ul class="social-net list-inline pull-right">
						<li class="social-net__item"><a href="youtube.com"
								class="social-net__link text-primary_h"><i
									class="fa-brands fa-youtube"></i></a>
						</li>
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
						<li class="social-net__item"><a href="instagram.com"
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
