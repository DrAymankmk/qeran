@push('styles')
<style>
/* Footer Responsive Styles */
@media (max-width: 991px) {
	.footer-section {
		margin-top: 40px !important;
	}

	.footer__main {
		padding-bottom: 50px;
	}

	.footer__bottom-content {
		flex-direction: column;
		text-align: center;
	}

	.footer .copyright {
		float: none !important;
		width: 100%;
		margin-bottom: 15px;
		text-align: center;
	}

	.social-net {
		float: none !important;
		width: 100%;
		justify-content: center;
	}
}

@media (max-width: 767px) {
	.footer {
		padding-top: 40px;
	}

	.footer__main {
		padding-bottom: 40px;
	}

	.footer__logo {
		margin-bottom: 30px;
	}

	.footer__logo img {
		max-width: 140px;
		height: auto;
	}

	.footer-section {
		margin-top: 30px !important;
		margin-bottom: 20px;
	}

	.footer-section__title {
		font-size: 18px !important;
		margin-bottom: 20px !important;
	}

	.footer__contact {
		margin-bottom: 15px;
		font-size: 13px;
		flex-wrap: wrap;
	}

	.footer__contact i {
		font-size: 16px;
		margin-right: 8px;
	}

	.footer__info {
		margin-right: 0;
		font-size: 13px;
		line-height: 1.6;
	}

	.footer-gallery {
		margin-top: 15px;
	}

	.footer-gallery__item {
		width: 33.333% !important;
		padding: 2px;
	}

	.footer-gallery__item img {
		width: 100%;
		height: auto;
	}

	.footer-list {
		margin-top: 10px;
	}

	.footer-list__item {
		margin-bottom: 8px;
	}

	.footer-list__link {
		font-size: 13px;
	}

	.footer__bottom {
		padding-top: 20px;
		padding-bottom: 20px;
	}

	.footer__bottom-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
	}

	.footer .copyright {
		padding-right: 0;
		margin-bottom: 15px;
		text-align: center;
		float: none !important;
		width: 100%;
	}

	.footer .copyright__link {
		display: inline-block;
		padding: 0 5px;
	}

	.social-net {
		text-align: center;
		float: none !important;
		margin: 0 auto;
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		width: 100%;
	}

	.social-net__item {
		margin: 5px;
	}

	.social-net__link {
		font-size: 18px;
		width: 35px;
		height: 35px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
}

@media (max-width: 480px) {
	.footer {
		padding-top: 30px;
	}

	.footer__main {
		padding-bottom: 30px;
	}

	.footer__logo {
		margin-bottom: 25px;
	}

	.footer__logo img {
		max-width: 120px;
	}

	.footer-section {
		margin-top: 25px !important;
	}

	.footer-section__title {
		font-size: 16px !important;
		margin-bottom: 15px !important;
	}

	.footer__contact {
		font-size: 12px;
		gap: 8px;
	}

	.footer__info {
		font-size: 12px;
	}

	.footer-gallery__item {
		width: 50% !important;
	}

	.footer__bottom-content {
		flex-direction: column;
		text-align: center;
	}

	.footer .copyright {
		font-size: 11px;
		line-height: 1.6;
		width: 100%;
		margin-bottom: 10px;
	}

	.footer .copyright__link {
		font-size: 11px;
		display: inline-block;
		padding: 0 3px;
	}

	.social-net {
		width: 100%;
		margin-top: 10px;
	}

	.social-net__link {
		font-size: 16px;
		width: 32px;
		height: 32px;
	}
}
</style>
@endpush
<footer class="footer">
	<div class="footer__main">
		<div class="container">
			@php
			$logoSetting = \App\Models\HubFile::where('original_name', 'footer_logo_img')->first();
			$logoUrl = $logoSetting && $logoSetting->path ? $logoSetting->get_path() :
			asset('frontend/assets/media/logo.png');
			@endphp
			<div class="row">
				<div class="col-xs-12">
					<div class="text-center"><a href="{{ route('home') }}"
							class="footer__logo"><img src="{{ $logoUrl }}"
								style="height:41px; width:176px"
								alt="Logo" class="img-responsive" /></a>
					</div>
				</div>
			</div>
			@php
			$footerSection = \App\Models\CmsPage::where('slug', 'general')
			->where('is_active', true)
			->with(['activeSections' => function($query) {
			$query->where('name', 'footer')
			->where('is_active', true)
			->with(['activeItems' => function($q) {
			$q->where('is_active', true)
			->with(['links' => function($linkQuery) {
			$linkQuery->where('is_active', true)->orderBy('order');
			}])
			->orderBy('order');
			}]);
			}])
			->first()
			?->activeSections
			->where('name', 'footer')
			->first();

			$itemCount = $footerSection && $footerSection->activeItems ?
			$footerSection->activeItems->count() : 0;
			$colClass = 'col-md-4';
			if ($itemCount > 0) {
			if ($itemCount == 1) {
			$colClass = 'col-md-4';
			} elseif ($itemCount == 2) {
			$colClass = 'col-md-4';
			} elseif ($itemCount == 3) {
			$colClass = 'col-md-4';
			} elseif ($itemCount == 4) {
			$colClass = 'col-md-4';
			} else {
			$colClass = 'col-md-4';
			}
			}
			@endphp

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
			<div class="row">
				@if($footerSection && $footerSection->activeItems &&
				$footerSection->activeItems->count() > 0)
				@foreach($footerSection->activeItems as $item)
				<div class="{{ $colClass }} col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							{{ $item->title }}
						</h3>
						@if($item->sub_title)
						<div class="footer-section__subtitle">{{ $item->sub_title }}
						</div>
						@endif
						@if($item->content)
						<div class="footer__info">{!!
							formatCmsContent($item->content) !!}</div>
						@endif
						@if($item->links && $item->links->count() > 0)
						@foreach($item->links as $link)
						<a href="{{ $link->url }}" target="{{ $link->target }}"
							class="btn btn-default btn-xs"
							rel="{{ $link->target === '_blank' ? 'noopener noreferrer' : '' }}">
							@if($link->icon)
							{!! $link->icon_html !!}
							@else
							<i class="icon"></i>
							@endif
							{{ $link->name }}
						</a>
						@endforeach
						@endif
					</section>
				</div>

				<div class="col-md-3 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							{{ __('frontend.keep_in_touch') }}
						</h3>
						@foreach($headerSection->activeItems as $item)
						@if($item->icon)
						@php
						$iconClass = trim($item->icon);
						// Normalize Font Awesome icons
						if (strpos($iconClass, 'fa-') !== false) {
						if (strpos($iconClass, 'fa fa-') === 0) {
						$iconClass = str_replace('fa fa-', 'fas fa-',
						$iconClass);
						} elseif (strpos($iconClass, 'fa-') === 0 &&
						!preg_match('/^(fas|far|fab)\s+fa-/', $iconClass))
						{
						$iconClass = 'fas ' . $iconClass;
						}
						}
						@endphp
						<div class="footer__contact"
							style="display: flex; align-items: center; gap: 10px;">
							<i class="{{ $iconClass }}"></i>
							@else
							<i class="icon icon-info"></i>
							@endif
							{!! formatCmsContent($item->content) !!}
						</div>
						@endforeach
					</section>
				</div>

				<!-- gallery -->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							{{ __('frontend.gallery') }}
						</h3>
						@php
						// Get designs that should be shown in footer
						$footerDesigns =
						\App\Models\Design::whereJsonContains('show_on', 'footer')
						->with(['translations', 'hubFiles', 'category'])
						->limit(6)
						->get();
						@endphp
						@if($footerDesigns && $footerDesigns->count() > 0)
						<ul
							class="footer-gallery list-unstyled js-zoom-gallery clearfix">
							@foreach($footerDesigns as $design)
							@php
							$designImage = $design->image();
							$designName = $design->name ?? 'Design';
							@endphp
							@if($designImage)
							<li class="footer-gallery__item">
								<a href="{{ $designImage }}"
									class="footer-gallery__link js-zoom-gallery__item">
									<img src="{{ $designImage }}"
										alt="{{ $designName }}"
										class="img-responsive" />
								</a>
							</li>
							@endif
							@endforeach
						</ul>
						@else
						{{-- Fallback to default gallery images if no designs found --}}
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
						@endif
					</section>
				</div>
				<div class="col-md-2 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							{{ __('frontend.quick_links') }}
						</h3>
						<ul class="footer-list list list-mark-4 list-unstyled">
							<li class="footer-list__item"><a
									href="{{ route('about') }}"
									class="footer-list__link">{{ __('frontend.about') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('services') }}"
									class="footer-list__link">{{ __('frontend.services') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('faq') }}"
									class="footer-list__link">{{ __('frontend.faq') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('contact') }}"
									class="footer-list__link">{{ __('frontend.contact') }}</a>
							</li>
						</ul>
					</section>
				</div>
				@endforeach
				@else
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							About Dvents
						</h3>
						<div class="footer-section__subtitle">The Events
							Specialists!</div>
						<div class="footer__info">
							<p>Aorem ipsum dolor sit amet elit sed lum tempor
								incididunt ut labore el dolore alg minim
								veniam quis nostrud lorem psum dolor sit
								amet sed incididunt.</p>
						</div>
						<a href="about.html" class="btn btn-default btn-xs"><i
								class="icon"></i> Read More</a>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							Keep In Touch
						</h3>
						<div class="footer__contact"><i class="icon icon-map"></i>
							38-2 Hilton Street, California, USA</div>
						<div class="footer__contact"><i
								class="icon icon-call-in"></i> (+01) 123
							456 7890</div>
						<div class="footer__contact"><i
								class="icon icon-envelope-open"></i>
							info@dvents.org</div>
						<div class="footer__contact"><i class="icon icon-clock"></i>
							Mon - Fri 9.00 am - 6.00 pm</div>
					</section>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							Events Gallery
						</h3>
						@php

						$footerDesignsFallback =
						\App\Models\Design::whereJsonContains('show_on', 'footer')
						->with(['translations', 'hubFiles', 'category'])
						->limit(6)
						->get();
						@endphp
						@if($footerDesignsFallback &&
						$footerDesignsFallback->count() > 0)
						<ul
							class="footer-gallery list-unstyled js-zoom-gallery clearfix">
							@foreach($footerDesignsFallback as $design)
							@php
							$designImage = $design->image();
							$designName = $design->name ?? 'Design';
							@endphp
							@if($designImage)
							<li class="footer-gallery__item">
								<a href="{{ $designImage }}"
									class="footer-gallery__link js-zoom-gallery__item">
									<img src="{{ $designImage }}"
										alt="{{ $designName }}"
										class="img-responsive" />
								</a>
							</li>
							@endif
							@endforeach
						</ul>
						@else
						{{-- Fallback to default gallery images if no designs found --}}
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
						@endif
					</section>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<section class="footer-section">
						<h3 class="footer-section__title ui-title-inner">
							<i class="ui-decor-2 bg-primary"></i>
							{{ __('frontend.quick_links') }}
						</h3>
						<ul class="footer-list list list-mark-4 list-unstyled">
							<li class="footer-list__item"><a
									href="{{ route('about') }}"
									class="footer-list__link">{{ __('frontend.about') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('services') }}"
									class="footer-list__link">{{ __('frontend.services') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('faq') }}"
									class="footer-list__link">{{ __('frontend.faq') }}</a>
							</li>
							<li class="footer-list__item"><a
									href="{{ route('contact') }}"
									class="footer-list__link">{{ __('frontend.contact') }}</a>
							</li>
						</ul>
					</section>
				</div>
				@endif
			</div>
		</div>
	</div>
	<div class="footer__bottom">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="footer__bottom-content">
						<div class="copyright pull-left">© 2026<strong>
								Qeran</strong> - The
							Events Specialists All Rights Reserved.<a href=""
								class="copyright__link"> Terms of
								Use</a><a href=""
								class="copyright__link">Privacy
								Policy</a>
						</div>
						@if($headerSection && $headerSection->links &&
						$headerSection->links->count() > 0)
						<ul class="social-net list-inline pull-right">
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
						<ul class="social-net list-inline pull-right">
							<li class="social-net__item"><a href="youtube.com"
									class="social-net__link text-primary_h"><i
										class="fa-brands fa-youtube"></i></a>
							</li>
							<li class="social-net__item"><a href="twitter.com"
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
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>