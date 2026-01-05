@push('styles')
<style>
/* Info Section Responsive */
@media (max-width: 767px) {
	.b-taglines {
		padding: 40px 0 !important;
	}
	
	.b-taglines__inner {
		padding: 20px 15px;
	}
	
	.b-taglines__title {
		font-size: 1.75rem !important;
		margin-bottom: 15px;
		line-height: 1.3;
	}
	
	.b-taglines__text {
		font-size: 1rem !important;
		line-height: 1.6;
	}
	
	.b-taglines .col-sm-10 {
		width: 100%;
		margin-left: 0;
	}
}

@media (max-width: 480px) {
	.b-taglines {
		padding: 30px 0 !important;
	}
	
	.b-taglines__title {
		font-size: 1.5rem !important;
	}
	
	.b-taglines__text {
		font-size: 0.9rem !important;
	}
}
</style>
@endpush
<section class="b-taglines area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			@php
			$infoSection = $homePage->activeSections->where('name', 'info')->first();
			@endphp
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="b-taglines__inner">
						<h2 class="b-taglines__title">{{ $infoSection->title }}</h2>
						<div class="b-taglines__text">{{ $infoSection->subtitle }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
