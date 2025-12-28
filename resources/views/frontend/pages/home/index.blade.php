@extends('frontend.layouts.app')

@push('styles')
<style>
:root {
	--font-body: system-ui, sans-serif;
	--color-primary: #000;
	--color-primary-hover: #555;
	--color-bg: #fff;
	--color-text: #333;
	--spacing-sm: 1rem;
	--spacing-md: 2rem;
	--transition-speed: 0.3s;
}

.reviews {
	padding: var(--spacing-md);
	text-align: center;
}

.reviews__container {
	position: relative;
}

.review {
	display: none;
	flex-direction: column;
	align-items: center;
	gap: var(--spacing-sm);
	width: 60%;
	margin: auto;
}

.review__quote-symbol::before {
	content: "❞";
	font-size: 5rem;
	color: var(--color-primary-hover);
}

.review__text {
	font-weight: 300;
	font-size: 1.25rem;
}

.review__author {
	font-weight: 500;
	font-size: 1.25rem;
}

.review__meta {
	font-weight: 400;
	font-size: 1rem;
	color: var(--color-primary-hover);
}

.reviews__controls {
	display: flex;
	justify-content: center;
	gap: 1rem;
	list-style: none;
	padding: var(--spacing-md) 0;
}

.reviews__control {
	width: 1rem;
	height: 1rem;
	border-radius: 50%;
	background-color: var(--color-primary);
	opacity: 0.2;
	cursor: pointer;
}

.reviews__control[aria-selected="true"] {
	opacity: 1;
}

.testimonials-modern {
	/* padding: 60px 20px 100px 20px; */
	position: relative;
}

.testimonials-modern__container {
	max-width: 1200px;
	margin: 0 auto;
	position: relative;
	display: flex;
	align-items: center;
	gap: 20px;
}

.testimonials-modern__wrapper {
	position: relative;
	min-height: 400px;
	flex: 1;
	overflow: hidden;
}

.testimonials-row {
	display: flex;
	gap: 30px;
	width: 100%;
	transition: opacity 0.6s ease, transform 0.6s ease;
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
}

.testimonials-row[style*="display: none"] {
	transform: translateX(30px);
}

.testimonial-card {
	flex: 1;
	width: 50%;
	position: relative;
}

.testimonial-card__inner {
	background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
	border-radius: 20px;
	padding: 40px 30px;
	box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
	position: relative;
	overflow: hidden;
	transition: transform 0.3s ease, box-shadow 0.3s ease;
	height: 100%;
	display: flex;
	flex-direction: column;
}

.testimonial-card__inner::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 5px;
	background: linear-gradient(90deg, #000000, #28a745, #ffc107);
}

.testimonial-card__inner:hover {
	transform: translateY(-5px);
	box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.testimonial-card__quote-icon {
	position: absolute;
	top: 30px;
	right: 40px;
	width: 60px;
	height: 60px;
	background: linear-gradient(135deg, #000000, #0056b3);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 24px;
	opacity: 0.1;
	animation: pulse 2s infinite;
}

@keyframes pulse {

	0%,
	100% {
		transform: scale(1);
		opacity: 0.1;
	}

	50% {
		transform: scale(1.1);
		opacity: 0.15;
	}
}

.testimonial-card__content {
	margin-bottom: 30px;
	position: relative;
	z-index: 1;
}

.testimonial-card__text {
	font-size: 18px;
	line-height: 1.8;
	color: #333;
	font-style: italic;
	margin: 0;
	position: relative;
	padding-left: 30px;
}

.testimonial-card__text::before {
	content: '"';
	position: absolute;
	left: 0;
	top: -10px;
	font-size: 60px;
	color: #000000;
	opacity: 0.2;
	font-family: Georgia, serif;
	line-height: 1;
}

.testimonial-card__rating {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-top: 20px;
	justify-content: center;
}

.testimonial-card__rating .fa-star {
	font-size: 18px;
	transition: transform 0.2s ease;
}

.testimonial-card__rating .fa-star.star-filled {
	color: #ffc107;
	text-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

.testimonial-card__rating .fa-star.star-empty {
	color: #e0e0e0;
}

.testimonial-card__rating .fa-star:hover {
	transform: scale(1.2);
}

.rating-value {
	margin-left: 10px;
	font-weight: 600;
	color: #666;
	font-size: 14px;
}

.testimonial-card__author {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 20px;
	padding-top: 25px;
	border-top: 1px solid #e9ecef;
}

.testimonial-card__avatar {
	width: 70px;
	height: 70px;
	border-radius: 50%;
	overflow: hidden;
	border: 4px solid #000000;
	box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
	flex-shrink: 0;
	transition: transform 0.3s ease;
}

.testimonial-card__avatar:hover {
	transform: scale(1.1) rotate(5deg);
}

.testimonial-card__avatar img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.avatar-placeholder {
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #000000, #0056b3);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 28px;
	font-weight: bold;
	text-transform: uppercase;
}

.testimonial-card__info {
	text-align: left;
}

.testimonial-card__name {
	margin: 0 0 5px 0;
	font-size: 20px;
	font-weight: 600;
	color: #333;
}

.testimonial-card__job {
	margin: 0;
	font-size: 14px;
	color: #666;
	font-style: italic;
}

.testimonials-modern__controls {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 20px;
	margin-top: 40px;
	width: 100%;
	position: absolute;
	bottom: -80px;
	left: 0;
	right: 0;
}

.testimonial-nav {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	border: 2px solid #000000;
	background: white;
	color: #000000;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.3s ease;
	font-size: 18px;
	flex-shrink: 0;
	z-index: 10;
	position: relative;
}

.testimonial-nav--prev {
	order: -1;
}

.testimonial-nav--next {
	order: 1;
}

.testimonial-nav:hover {
	background: #000000;
	color: white;
	transform: scale(1.1);
	box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
}

.testimonial-nav:active {
	transform: scale(0.95);
}

.testimonials-dots {
	display: flex;
	gap: 12px;
	list-style: none;
	padding: 0;
	margin: 0;
	align-items: center;
}

.testimonial-dot {
	width: 50px;
	height: 6px;
	border: none;
	background: #e0e0e0;
	border-radius: 3px;
	cursor: pointer;
	padding: 0;
	position: relative;
	overflow: hidden;
	transition: all 0.3s ease;
}

.testimonial-dot span {
	display: block;
	width: 100%;
	height: 100%;
	background: #000000;
	border-radius: 3px;
	transform: scaleX(0);
	transform-origin: left;
	transition: transform 0.3s ease;
}

.testimonial-dot.active span,
.testimonial-dot:hover span {
	transform: scaleX(1);
}

.testimonial-dot.active {
	background: #cce5ff;
}

.testimonial-dot:hover {
	background: #e0e0e0;
	transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
	.testimonials-modern__container {
		flex-direction: column;
		gap: 15px;
	}

	.testimonials-row {
		flex-direction: column;
		gap: 20px;
	}

	.testimonial-card {
		width: 100%;
	}

	.testimonial-nav {
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		width: 40px;
		height: 40px;
		font-size: 14px;
	}

	.testimonial-nav--prev {
		left: 10px;
		order: 0;
	}

	.testimonial-nav--next {
		right: 10px;
		order: 0;
	}

	.testimonial-card__inner {
		padding: 35px 25px;
	}

	.testimonial-card__text {
		font-size: 16px;
		padding-left: 20px;
	}

	.testimonial-card__quote-icon {
		width: 50px;
		height: 50px;
		font-size: 20px;
		top: 20px;
		right: 25px;
	}

	.testimonial-card__avatar {
		width: 60px;
		height: 60px;
	}

	.testimonial-card__name {
		font-size: 18px;
	}

	.testimonials-modern {
		padding: 40px 15px;
	}

	.testimonials-modern__wrapper {
		min-height: 350px;
	}

	.testimonials-modern__controls {
		position: relative;
		bottom: auto;
		margin-top: 30px;
	}
}

@media (max-width: 480px) {
	.testimonial-card__inner {
		padding: 30px 20px;
	}

	.testimonial-card__author {
		flex-direction: column;
		text-align: center;
	}

	.testimonial-card__info {
		text-align: center;
	}

	.testimonial-nav {
		width: 40px;
		height: 40px;
		font-size: 14px;
	}
}
</style>

@endpush

@section('content')


@include('frontend.pages.home.partials.hero-section')

@include('frontend.pages.home.partials.about-section')

@include('frontend.pages.home.partials.services-section')

@include('frontend.pages.home.partials.why-choose-us-section')

@include('frontend.pages.home.partials.info-section')

@include('frontend.pages.home.partials.designs-section')

@include('frontend.pages.home.partials.testimonials2-section')

@include('frontend.pages.home.partials.get-in-touch-section')

@include('frontend.pages.home.partials.guard-app-section')

@include('frontend.pages.home.partials.contact-section')


@include('frontend.pages.home.partials.offers')


@push('scripts')
<script>
jQuery(document).ready(function($) {
	// Use event delegation to handle clicks on dynamically loaded content
	$(document).on('click', '.design-item', function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $item = $(this);
		var designName = $item.attr('data-design-name') || '';
		var designCode = $item.attr('data-design-code') || '';
		var designImage = $item.attr('data-design-image') || '';

		if (!designImage) {
			console.warn('Design image not found');
			return false;
		}

		var $modal = $('#designModal');
		if ($modal.length === 0) {
			console.error('Modal element not found');
			return false;
		}

		// Set modal content
		$('#modalDesignImage').attr('src', designImage);

		// Handle design name
		var $nameElement = $('#modalDesignName');
		if (designName && designName.trim() !== '') {
			$nameElement.text(designName).show();
		} else {
			$nameElement.text('Design').show();
		}

		// Handle design code
		var $codeContainer = $('#modalDesignCodeContainer');
		if (designCode && designCode.trim() !== '') {
			$('#modalDesignCode').text(designCode);
			$codeContainer.show();
		} else {
			$codeContainer.hide();
		}

		// Show modal using Bootstrap
		$modal.modal({
			backdrop: true,
			keyboard: true,
			show: true
		});

		return false;
	});

	// Handle modal close - ensure backdrop is removed
	$('#designModal').on('hidden.bs.modal', function() {
		$(this).removeClass('in');
		$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
	});

	// Ensure close buttons work
	$('#designModal').on('click', '.close, [data-dismiss="modal"]', function(e) {
		e.preventDefault();
		$('#designModal').modal('hide');
	});

	// Handle backdrop clicks to close modal
	$(document).on('click', '.modal-backdrop', function() {
		$('#designModal').modal('hide');
	});
});

jQuery(document).ready(function($) {
	// Wait for Swiper to be available
	function initSwiper() {
		if (typeof Swiper !== 'undefined') {
			var swiper = new Swiper(".mySwiper", {
				slidesPerView: 3,
				spaceBetween: 5,
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				},
				navigation: {
					nextEl: ".swiper-button-nexts",
					prevEl: ".swiper-button-prevs",
				},
				mousewheel: true,
				keyboard: true,
				loop: true,
				breakpoints: {
					300: {
						slidesPerView: 1
					},
					501: {
						slidesPerView: 1
					},
					769: {
						slidesPerView: 3,
						spaceBetween: 10
					},
					1025: {
						slidesPerView: 3,
						spaceBetween: 10
					},
				}
			});
		} else {
			// Retry after a short delay if Swiper is not loaded yet
			setTimeout(initSwiper, 100);
		}
	}

	// Initialize Swiper
	initSwiper();
});

document.addEventListener("DOMContentLoaded", () => {
	const reviews = document.querySelectorAll(".review");
	const controls = document.querySelectorAll(".reviews__control");

	let currentIndex = 0;
	const intervalTime = 4000;
	let autoPlayInterval = null;

	function showReview(index) {
		reviews.forEach((review, i) => {
			review.style.display = i === index ? "flex" :
				"none";
		});

		controls.forEach((control, i) => {
			control.setAttribute("aria-selected", i ===
				index);
			control.setAttribute("tabindex", i === index ?
				"0" : "-1");
		});

		currentIndex = index;
	}

	function nextReview() {
		const nextIndex = (currentIndex + 1) % reviews.length;
		showReview(nextIndex);
	}

	function handleControlClick(e) {
		const index = Array.from(controls).indexOf(e.target);
		showReview(index);
		resetAutoPlay();
	}

	function setupControls() {
		controls.forEach((control) => {
			control.addEventListener("click",
				handleControlClick);
			control.addEventListener("keydown", (e) => {
				if (e.key ===
					"Enter" ||
					e.key ===
					" ") {
					e
						.preventDefault();
					control
						.click();
				}
			});
		});
	}

	function startAutoPlay() {
		autoPlayInterval = setInterval(nextReview, intervalTime);
	}

	function resetAutoPlay() {
		clearInterval(autoPlayInterval);
		startAutoPlay();
	}

	// Initialize
	showReview(currentIndex);
	setupControls();
	startAutoPlay();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const testimonialRows = document.querySelectorAll(".testimonials-row");
	const dots = document.querySelectorAll(".testimonial-dot");
	const prevBtn = document.querySelector(".testimonial-nav--prev");
	const nextBtn = document.querySelector(".testimonial-nav--next");

	if (testimonialRows.length === 0) return;

	let currentIndex = 0;
	const intervalTime = 5000;
	let autoPlayInterval = null;

	function showTestimonialPair(index) {
		// Hide all rows
		testimonialRows.forEach((row, i) => {
			if (i === index) {
				row.style.display = "flex";
				setTimeout(() => {
					row.style.opacity =
						"1";
					row.style.transform =
						"translateX(0)";
				}, 10);
			} else {
				row.style.opacity = "0";
				row.style.transform =
					"translateX(30px)";
				setTimeout(() => {
					if (i !==
						index
						) {
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

	// Keyboard navigation
	document.addEventListener("keydown", function(e) {
		const container = document.querySelector(
			".testimonials-modern__container");
		if (container && container.contains(document
				.activeElement) ||
			document.activeElement === document.body) {
			if (e.key === "ArrowLeft") {
				prevTestimonial();
			} else if (e.key === "ArrowRight") {
				nextTestimonial();
			}
		}
	});

	// Pause on hover
	const container = document.querySelector(".testimonials-modern__container");
	if (container) {
		container.addEventListener("mouseenter", () => clearInterval(autoPlayInterval));
		container.addEventListener("mouseleave", startAutoPlay);
	}

	// Initialize
	showTestimonialPair(0);
	startAutoPlay();
});
</script>
@endpush
@endsection