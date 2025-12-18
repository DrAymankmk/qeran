@if($testimonials && $testimonials->count() > 0)
@php
$testimonialsSection = $homePage->activeSections->where('name', 'testimonials')->first();
$testimonialsPairs = $testimonials->chunk(2);
@endphp
<div class="section-default">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">
						{{ $testimonialsSection->title ?? 'What Our Clients Say' }}
					</h2>
					<div class="ui-subtitle-block">
						{{ $testimonialsSection->subtitle ?? 'Real experiences from real customers' }}
					</div>
				</div>
			</div>
		</div>
		<section id="reviews" class="testimonials-modern" aria-label="Customer Testimonials">
			<div class="testimonials-modern__container">
				@if($testimonialsPairs->count() > 1)
				<button class="testimonial-nav testimonial-nav--prev"
					aria-label="Previous testimonial">
					<i class="fas fa-chevron-left"></i>
				</button>
				@endif

				<div class="testimonials-modern__wrapper">
					@foreach($testimonialsPairs as $pairIndex => $pair)
					<div class="testimonials-row" data-pair-index="{{ $pairIndex }}"
						style="display: {{ $pairIndex === 0 ? 'flex' : 'none' }}; opacity: {{ $pairIndex === 0 ? '1' : '0' }};">
						@foreach($pair as $testimonial)
						<article class="testimonial-card" role="group"
							aria-roledescription="testimonial">
							<div class="testimonial-card__inner">
								<div
									class="testimonial-card__quote-icon">
									<i
										class="fas fa-quote-left"></i>
								</div>
								<div class="testimonial-card__content">
									<p
										class="testimonial-card__text">
										"{{ $testimonial->message }}"
									</p>
									@if($testimonial->rating)
									<div
										class="testimonial-card__rating">
										@for($i = 1; $i <=
											5; $i++)
											<i
											class="fas fa-star {{ $i <= $testimonial->rating ? 'star-filled' : 'star-empty' }}">
											</i>
											@endfor
											<span
												class="rating-value">{{ $testimonial->rating }}/5</span>
									</div>
									@endif
								</div>
								<div class="testimonial-card__author">
									<div
										class="testimonial-card__avatar">
										@if($testimonial->image())
										<img src="{{ $testimonial->image() }}"
											alt="{{ $testimonial->name }}" />
										@else
										<div
											class="avatar-placeholder">
											<span>{{ substr($testimonial->name, 0, 1) }}</span>
										</div>
										@endif
									</div>
									<div
										class="testimonial-card__info">
										<h4
											class="testimonial-card__name">
											{{ $testimonial->name }}
										</h4>
										@if($testimonial->job)
										<p
											class="testimonial-card__job">
											{{ $testimonial->job }}
										</p>
										@endif
									</div>
								</div>
							</div>
						</article>
						@endforeach
					</div>
					@endforeach
				</div>

				@if($testimonialsPairs->count() > 1)
				<button class="testimonial-nav testimonial-nav--next"
					aria-label="Next testimonial">
					<i class="fas fa-chevron-right"></i>
				</button>
				@endif

				@if($testimonialsPairs->count() > 1)
				<div class="testimonials-modern__controls">
					<ul class="testimonials-dots" role="tablist"
						aria-label="Testimonials navigation">
						@foreach($testimonialsPairs as $index => $pair)
						<li>
							<button class="testimonial-dot {{ $index === 0 ? 'active' : '' }}"
								role="tab"
								tabindex="{{ $index === 0 ? '0' : '-1' }}"
								aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
								data-index="{{ $index }}"
								aria-label="Go to testimonials {{ $index + 1 }}">
								<span></span>
							</button>
						</li>
						@endforeach
					</ul>
				</div>
				@endif
			</div>
		</section>
	</div>
</div>
@endif