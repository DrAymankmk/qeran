@if($testimonials && $testimonials->count() > 0)
@php
$testimonialsSection = $homePage->activeSections->where('name', 'testimonials')->first();
$testimonialsPairs = $testimonials->chunk(2);
@endphp
@push('styles')
<style>
/* Fix testimonials visibility on small devices */
@media (max-width: 768px) {
	.testimonials-modern__wrapper {
		position: relative !important;
		min-height: auto !important;
		overflow: visible !important;
		width: 100%;
		height: auto !important;
	}

	.testimonials-row {
		position: relative !important;
		display: flex !important;
		flex-direction: column !important;
		gap: 20px;
		width: 100% !important;
		opacity: 1 !important;
		transform: translateY(0) !important;
		transition: opacity 0.6s ease, transform 0.6s ease;
		will-change: opacity, transform;
		top: auto !important;
		left: auto !important;
		right: auto !important;
		bottom: auto !important;
		visibility: visible !important;
	}

	.testimonials-row[style*="display: none"] {
		display: none !important;
		visibility: hidden !important;
	}

	.testimonials-row[style*="opacity: 0"] {
		opacity: 0 !important;
	}

	.testimonial-card {
		width: 100% !important;
		flex: none !important;
		max-width: 100% !important;
		min-width: 0 !important;
	}

	/* Hide second item in each pair on mobile - show only one at a time */
	.testimonials-row .testimonial-card:not(:first-child) {
		display: none !important;
	}

	.testimonials-modern__container {
		position: relative;
		padding: 0 15px;
		overflow: visible;
		flex-direction: column;
		align-items: stretch;
	}

	.testimonial-nav {
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		z-index: 10;
		width: 40px;
		height: 40px;
		font-size: 14px;
		background: rgba(255, 255, 255, 0.9);
		border: 1px solid #e0e0e0;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	}

	.testimonial-nav--prev {
		left: 5px;
	}

	.testimonial-nav--next {
		right: 5px;
	}

	/* Ensure testimonial content is visible */
	.testimonial-card__inner {
		visibility: visible !important;
		opacity: 1 !important;
	}

	.testimonial-card__content,
	.testimonial-card__author {
		visibility: visible !important;
		opacity: 1 !important;
	}
}

@media (max-width: 480px) {
	.testimonials-modern__wrapper {
		min-height: auto;
		padding: 0;
	}

	.testimonials-row {
		gap: 15px;
	}

	.testimonial-card__inner {
		padding: 25px 20px !important;
	}

	.testimonial-nav {
		width: 35px;
		height: 35px;
		font-size: 12px;
	}

	.testimonial-nav--prev {
		left: 0;
	}

	.testimonial-nav--next {
		right: 0;
	}
}
</style>
@endpush
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

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
	const testimonialRows = document.querySelectorAll("#reviews .testimonials-row");
	const dots = document.querySelectorAll("#reviews .testimonial-dot");
	const prevBtn = document.querySelector("#reviews .testimonial-nav--prev");
	const nextBtn = document.querySelector("#reviews .testimonial-nav--next");

	if (testimonialRows.length === 0) return;

	let currentIndex = 0;
	const intervalTime = 5000;
	let autoPlayInterval = null;

	function getIsMobile() {
		return window.innerWidth <= 768;
	}

	let isMobile = getIsMobile();

	function showTestimonialPair(index) {
		const isMobileNow = getIsMobile();

		// Hide all rows
		testimonialRows.forEach((row, i) => {
			if (i === index) {
				// Show current row
				row.style.display = "flex";
				row.style.visibility = "visible";
				// Reset transform first
				row.style.transform = isMobileNow ?
					"translateY(0)" :
					"translateX(0)";
				// Force reflow
				void row.offsetHeight;
				// Then animate
				setTimeout(() => {
					row.style.opacity =
						"1";
					row.style.transform =
						isMobileNow ?
						"translateY(0)" :
						"translateX(0)";
					row.style.visibility =
						"visible";
				}, 10);

				// On mobile, hide second item in pair (show only first)
				if (isMobileNow) {
					const cards = row
						.querySelectorAll(
							'.testimonial-card'
							);
					cards.forEach((card, cardIndex) => {
						if (cardIndex ===
							0
							) {
							card.style.display =
								"block";
						} else {
							card.style.display =
								"none";
						}
					});
				} else {
					// Desktop: show all cards in the row
					const cards = row
						.querySelectorAll(
							'.testimonial-card'
							);
					cards.forEach(card => {
						card.style.display =
							"";
					});
				}
			} else {
				// Hide other rows
				row.style.opacity = "0";
				row.style.transform = isMobileNow ?
					"translateY(30px)" :
					"translateX(30px)";
				row.style.visibility = "hidden";
				setTimeout(() => {
					if (i !==
						index) {
						row.style.display =
							"none";
					}
				}, 600);
			}
		});

		// Update dots
		dots.forEach((dot, i) => {
			if (i === index) {
				dot.classList.add("active");
				dot.setAttribute("aria-selected",
					"true");
				dot.setAttribute("tabindex", "0");
			} else {
				dot.classList.remove("active");
				dot.setAttribute("aria-selected",
					"false");
				dot.setAttribute("tabindex", "-1");
			}
		});

		currentIndex = index;
	}

	function nextTestimonial() {
		const nextIndex = (currentIndex + 1) % testimonialRows.length;
		showTestimonialPair(nextIndex);
		resetAutoPlay();
	}

	function prevTestimonial() {
		const prevIndex = (currentIndex - 1 + testimonialRows.length) % testimonialRows
			.length;
		showTestimonialPair(prevIndex);
		resetAutoPlay();
	}

	function handleDotClick(e) {
		const index = parseInt(e.currentTarget.getAttribute("data-index"));
		showTestimonialPair(index);
		resetAutoPlay();
	}

	function startAutoPlay() {
		if (testimonialRows.length > 1) {
			autoPlayInterval = setInterval(nextTestimonial, intervalTime);
		}
	}

	function resetAutoPlay() {
		clearInterval(autoPlayInterval);
		startAutoPlay();
	}

	// Event listeners
	if (prevBtn) {
		prevBtn.addEventListener("click", prevTestimonial);
	}

	if (nextBtn) {
		nextBtn.addEventListener("click", nextTestimonial);
	}

	dots.forEach(dot => {
		dot.addEventListener("click", handleDotClick);
		dot.addEventListener("keydown", function(e) {
			if (e.key === "Enter" || e
				.key === " ") {
				e.preventDefault();
				dot.click();
			}
		});
	});

	// Touch swipe support for mobile
	let touchStartX = 0;
	let touchEndX = 0;
	const container = document.querySelector("#reviews .testimonials-modern__container");

	if (container) {
		container.addEventListener("touchstart", function(e) {
			touchStartX = e.changedTouches[0].screenX;
		}, {
			passive: true
		});

		container.addEventListener("touchend", function(e) {
			touchEndX = e.changedTouches[0].screenX;
			handleSwipe();
		}, {
			passive: true
		});

		function handleSwipe() {
			const swipeThreshold = 50;
			const diff = touchStartX - touchEndX;

			if (Math.abs(diff) > swipeThreshold) {
				if (diff > 0) {
					// Swipe left - next
					nextTestimonial();
				} else {
					// Swipe right - previous
					prevTestimonial();
				}
			}
		}

		// Pause on hover (desktop only)
		if (!isMobile) {
			container.addEventListener("mouseenter", () => clearInterval(
				autoPlayInterval));
			container.addEventListener("mouseleave", startAutoPlay);
		}
	}

	// Keyboard navigation
	document.addEventListener("keydown", function(e) {
		const container = document.querySelector(
			"#reviews .testimonials-modern__container"
		);
		if (container && (container.contains(document
					.activeElement) || document
				.activeElement === document.body)) {
			if (e.key === "ArrowLeft") {
				prevTestimonial();
			} else if (e.key === "ArrowRight") {
				nextTestimonial();
			}
		}
	});

	// Initialize - ensure first row is visible on mobile
	function initializeTestimonials() {
		const isMobileNow = getIsMobile();
		testimonialRows.forEach((row, i) => {
			if (i === 0) {
				// Force first row to be visible
				row.style.display = "flex";
				row.style.visibility = "visible";
				row.style.opacity = "1";
				row.style.transform = isMobileNow ?
					"translateY(0)" :
					"translateX(0)";

				// On mobile, hide second item in first pair
				if (isMobileNow) {
					const cards = row
						.querySelectorAll(
							'.testimonial-card'
							);
					cards.forEach((card, cardIndex) => {
						if (cardIndex ===
							0
							) {
							card.style.display =
								"block";
						} else {
							card.style.display =
								"none";
						}
					});
				}
			} else {
				row.style.display = "none";
				row.style.visibility = "hidden";
				row.style.opacity = "0";
			}
		});
		showTestimonialPair(0);
	}

	// Initialize immediately
	initializeTestimonials();
	startAutoPlay();

	// Handle window resize
	let resizeTimer;
	window.addEventListener("resize", function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			// Update mobile detection
			isMobile = getIsMobile();
			// Re-initialize on resize to fix layout issues
			showTestimonialPair(
				currentIndex
			);
		}, 250);
	});
});
</script>
@endpush
@endif