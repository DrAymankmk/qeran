@php
$offersSection = $homePage->activeSections->where('name', 'offers')->first();
$hasPromoCodes = isset($promoCodes) && $promoCodes->count() > 0;
@endphp

@if($hasPromoCodes && $offersSection)
<section class="section-default">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">
						{{ $offersSection->title ?? 'Special Offers' }}</h2>
					<div class="ui-subtitle-block">{{ $offersSection->subtitle ?? '' }}
					</div>
				</div>
				<div data-min480="1" data-min768="2" data-min992="3" data-min1200="3"
					data-pagination="false" data-navigation="false" data-auto-play="4000"
					data-stop-on-hover="true"
					class="owl-carousel owl-theme enable-owl-carousel">
					@foreach($promoCodes as $index => $promoCode)
					@php
					$defaultImages = [
					asset('frontend/assets/media/content/posts/380x290/1.jpg'),
					asset('frontend/assets/media/content/posts/380x290/2.jpg'),
					asset('frontend/assets/media/content/posts/380x290/3.jpg')
					];
					$imageIndex = $index % count($defaultImages);
					$promoImage = $defaultImages[$imageIndex];
					@endphp
					<section class="b-post-sm b-post-sm-2 clearfix">
						<div class="entry-media">
							<a href="{{ $promoImage }}"
								class="js-zoom-images">
								<img src="{{ $promoImage }}"
									alt="{{ $promoCode->name }}"
									class="img-responsive" />
							</a>
						</div>
						<div class="entry-main">
							<div class="entry-header">
								<div
									class="ui-decor-2 ui-decor-2_vert bg-primary">
								</div>
								<h2
									class="entry-title entry-title_spacing ui-title-inner">
									<a
										href="javascript:void(0);">{{ $promoCode->name }}</a>
								</h2>
							</div>
							<div class="entry-body" style="padding: 15px;">
								<div class="promo-code-info">
									<div class="promo-code-badge"
										style="background: #f0f0f0; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;">
										<strong
											style="font-size: 18px; color: #333;">Code:
											<span
												style="color: #007bff;">{{ $promoCode->code }}</span></strong>
									</div>
									<div
										style="margin-bottom: 8px;">
										<strong
											style="color: #28a745; font-size: 20px;">{{ number_format($promoCode->discount_percentage, 0) }}%
											OFF</strong>
									</div>
									@if($promoCode->package)
									<div
										style="margin-bottom: 8px; color: #666;">
										<i
											class="icon icon-package"></i>
										Package:
										<strong>{{ $promoCode->package->title ?? 'N/A' }}</strong>
									</div>
									@else
									<div
										style="margin-bottom: 8px; color: #666;">
										<i
											class="icon icon-package"></i>
										Valid for: <strong>All
											Packages</strong>
									</div>
									@endif
									@if($promoCode->usage_limit)
									<div
										style="margin-bottom: 8px; color: #666; font-size: 12px;">
										<i
											class="icon icon-users"></i>
										Remaining:
										{{ $promoCode->usage_limit - $promoCode->used_count }}
										uses
									</div>
									@endif
								</div>
							</div>
							<div class="entry-footer">
								<div class="entry-meta">
									<span
										class="entry-meta__item">
										<i
											class="icon icon-calendar"></i>Valid
										from
										<span
											class="entry-meta__link">
											{{ \Carbon\Carbon::parse($promoCode->valid_date)->format('d M Y') }}
										</span>
									</span>
									<span class="entry-meta__item"
										style="margin-left: 10px;">
										<i
											class="icon icon-calendar"></i>Expires
										<span
											class="entry-meta__link">
											{{ \Carbon\Carbon::parse($promoCode->expire_date)->format('d M Y') }}
										</span>
									</span>
								</div>
							</div>
						</div>
					</section>
					<!-- end post-->
					@endforeach
				</div>
				<!-- end slider-->
			</div>
		</div>
	</div>
</section>
@endif
