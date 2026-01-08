<style>
/* ============================================
   FRONTEND RESPONSIVE STYLES
   ============================================ */

/* Base Responsive Utilities */
@media (max-width: 1199px) {
	.container {
		max-width: 100%;
		padding-left: 15px;
		padding-right: 15px;
	}
}

/* Tablet Styles (768px - 991px) */
@media (max-width: 991px) {
	/* Typography */
	.ui-title-block {
		font-size: 2rem !important;
	}
	
	.ui-subtitle-block {
		font-size: 1rem !important;
	}
	
	.b-title-page__title {
		font-size: 2.5rem !important;
	}
	
	/* Sections */
	.section-default,
	.section-type-1,
	.section-contact {
		padding: 40px 0 !important;
	}
	
	/* Grid adjustments */
	.col-md-8,
	.col-md-6,
	.col-md-4 {
		margin-bottom: 30px;
	}
	
	/* Hero Slider */
	.main-slider {
		height: 600px !important;
	}
	
	.main-slider__title {
		font-size: 2rem !important;
	}
	
	.main-slider__info {
		font-size: 1.2rem !important;
	}
}

/* Mobile Styles (max-width: 767px) */
@media (max-width: 767px) {
	/* Typography */
	.ui-title-block {
		font-size: 1.75rem !important;
		line-height: 1.3 !important;
	}
	
	.ui-subtitle-block {
		font-size: 0.95rem !important;
	}
	
	.b-title-page__title {
		font-size: 2rem !important;
	}
	
	.entry-title {
		font-size: 1.5rem !important;
	}
	
	/* Sections */
	.section-default,
	.section-type-1,
	.section-contact {
		padding: 30px 0 !important;
	}
	
	/* Hero Slider */
	.main-slider {
		height: 400px !important;
	}
	
	.main-slider__title {
		font-size: 1.5rem !important;
		padding: 0 15px;
	}
	
	.main-slider__info {
		font-size: 1rem !important;
		padding: 0 15px;
	}
	
	.hero-slider-image {
		object-fit: cover;
		height: 100%;
		width: 100%;
	}
	
	/* About Section */
	.section-type-1 .col-md-4,
	.section-type-1 .col-md-8 {
		margin-bottom: 20px;
	}
	
	.section-type-1__inner {
		padding: 20px 0;
	}
	
	/* Services Section */
	.b-services .col-md-5,
	.b-services .col-md-7 {
		margin-bottom: 30px;
	}
	
	.b-services .row {
		flex-direction: column;
	}
	
	/* Why Choose Us Section */
	.b-info-section .col-lg-7,
	.b-info-section .col-lg-5,
	.b-info-section .col-md-6 {
		margin-bottom: 20px;
	}
	
	.b-info-section__img-1,
	.b-info-section__img-2 {
		width: 100%;
		height: auto;
		margin-bottom: 15px;
	}
	
	/* Contact Section */
	.section-contact .col-lg-6,
	.section-contact .col-lg-4 {
		margin-bottom: 20px;
	}
	
	.b-contact {
		margin-bottom: 20px;
		padding: 25px 20px !important;
	}
	
	/* Contact Form */
	.section-form-contact {
		padding: 30px 15px !important;
	}
	
	.section-form-contact .col-md-6 {
		margin-bottom: 15px;
	}
	
	.form-control {
		font-size: 16px !important; /* Prevents zoom on iOS */
		padding: 12px 15px;
	}
	
	/* Gallery/Isotope */
	.b-isotope-filter {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 10px;
		margin-bottom: 20px;
	}
	
	.b-isotope-filter li {
		margin: 5px 0;
	}
	
	.b-isotope-grid__item {
		width: 100% !important;
		margin-bottom: 15px;
	}
	
	/* Packages/Pricing */
	.b-pricing {
		margin-bottom: 30px;
		padding: 25px 20px !important;
	}
	
	/* Testimonials */
	.testimonials-modern__container {
		flex-direction: column;
	}
	
	.testimonial-card {
		width: 100% !important;
		margin-bottom: 20px;
	}
	
	/* Info Section */
	.b-taglines__title {
		font-size: 1.75rem !important;
	}
	
	.b-taglines__text {
		font-size: 1rem !important;
	}
	
	/* Block Table (Contact Section) */
	.block-table {
		display: block !important;
	}
	
	.block-table__cell {
		display: block !important;
		width: 100% !important;
	}
	
	/* Breadcrumb */
	.breadcrumb {
		font-size: 0.85rem;
		padding: 10px 0;
	}
	
	/* Buttons */
	.btn {
		padding: 12px 20px;
		font-size: 0.95rem;
		width: 100%;
		max-width: 100%;
	}
	
	.btn-block {
		width: 100%;
	}
	
	/* Images */
	img.img-responsive {
		max-width: 100%;
		height: auto;
	}
	
	/* Video */
	video {
		max-width: 100%;
		height: auto;
	}
	
	/* Modal */
	.modal-dialog {
		margin: 10px;
		width: calc(100% - 20px);
	}
	
	.modal-content {
		padding: 15px;
	}
	
	/* Swiper/Carousel */
	.swiper-slide {
		width: 100% !important;
	}
	
	/* Offers Section */
	.b-request-estimate {
		padding: 20px 15px;
		text-align: center;
	}
	
	.b-request-estimate__info {
		font-size: 1rem;
		margin-bottom: 15px;
	}
	
	.b-request-estimate__title {
		font-size: 1.25rem;
	}
}

/* Small Mobile (max-width: 480px) */
@media (max-width: 480px) {
	/* Typography */
	.ui-title-block {
		font-size: 1.5rem !important;
	}
	
	.b-title-page__title {
		font-size: 1.75rem !important;
	}
	
	.entry-title {
		font-size: 1.25rem !important;
	}
	
	/* Sections */
	.section-default,
	.section-type-1,
	.section-contact {
		padding: 20px 0 !important;
	}
	
	/* Hero Slider */
	.main-slider {
		height: 300px !important;
	}
	
	.main-slider__title {
		font-size: 1.25rem !important;
	}
	
	.main-slider__info {
		font-size: 0.9rem !important;
	}
	
	/* Forms */
	.form-control {
		padding: 10px 12px;
		font-size: 16px;
	}
	
	/* Contact Cards */
	.b-contact {
		padding: 20px 15px !important;
	}
	
	.b-contact__name {
		font-size: 1rem;
	}
	
	.b-contact__info {
		font-size: 0.9rem;
	}
	
	/* Gallery Filter */
	.b-isotope-filter {
		flex-direction: column;
		align-items: center;
	}
	
	.b-isotope-filter li {
		width: 100%;
		text-align: center;
	}
	
	/* Pricing */
	.b-pricing {
		padding: 20px 15px !important;
	}
	
	.b-pricing__title {
		font-size: 1.25rem;
	}
	
	.b-pricing-price__number {
		font-size: 2rem;
	}
	
	/* Testimonials */
	.testimonial-card__inner {
		padding: 25px 15px !important;
	}
	
	.testimonial-card__text {
		font-size: 0.95rem;
	}
	
	.testimonial-card__author {
		flex-direction: column;
		text-align: center;
	}
	
	.testimonial-card__avatar {
		width: 50px;
		height: 50px;
	}
	
	/* Buttons */
	.btn {
		padding: 10px 15px;
		font-size: 0.9rem;
	}
	
	/* Info Section */
	.b-info-section__inner {
		padding: 20px 15px;
	}
	
	.b-taglines__title {
		font-size: 1.5rem !important;
	}
	
	.b-taglines__text {
		font-size: 0.9rem !important;
	}
}

/* Landscape Mobile (481px - 767px) */
@media (min-width: 481px) and (max-width: 767px) and (orientation: landscape) {
	.main-slider {
		height: 350px !important;
	}
	
	.section-default,
	.section-type-1 {
		padding: 25px 0 !important;
	}
}

/* Print Styles */
@media print {
	.main-slider,
	.b-isotope-filter,
	.testimonials-modern__controls,
	.btn {
		display: none !important;
	}
	
	.section-default,
	.section-type-1 {
		page-break-inside: avoid;
	}
}

/* High DPI Displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
	img {
		image-rendering: -webkit-optimize-contrast;
		image-rendering: crisp-edges;
	}
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
	*,
	*::before,
	*::after {
		animation-duration: 0.01ms !important;
		animation-iteration-count: 1 !important;
		transition-duration: 0.01ms !important;
	}
}

/* RTL Support for Arabic */
[dir="rtl"] .col-md-4,
[dir="rtl"] .col-md-6,
[dir="rtl"] .col-md-8 {
	text-align: right;
}

[dir="rtl"] .b-contact__icon {
	left: auto;
	right: 20px;
}

/* Container Fluid Adjustments */
@media (max-width: 767px) {
	.container-fluid {
		padding-left: 15px;
		padding-right: 15px;
	}
}

/* Fix for flexbox issues on older browsers */
@supports not (display: flex) {
	.row[style*="display:flex"] {
		display: block;
	}
	
	.row[style*="display:flex"] > [class*="col-"] {
		float: left;
	}
}
</style>







