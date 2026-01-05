@push('styles')
<style>
/* Why Choose Us Section Responsive */
@media (max-width: 991px) {
	.b-info-section .col-lg-7,
	.b-info-section .col-lg-5,
	.b-info-section .col-md-6 {
		margin-bottom: 30px;
	}
	
	.b-info-section__inner {
		padding: 20px 0;
	}
}

@media (max-width: 767px) {
	.b-info-section {
		padding: 30px 0;
	}
	
	.b-info-section .col-lg-7 {
		order: 2;
		margin-bottom: 25px;
	}
	
	.b-info-section .col-lg-5 {
		order: 1;
		margin-bottom: 25px;
	}
	
	.b-info-section .col-sm-6 {
		margin-bottom: 15px;
	}
	
	.b-info-section__img-1,
	.b-info-section__img-2 {
		width: 100%;
		height: auto;
		margin-bottom: 15px;
	}
	
	.b-info-section__inner {
		text-align: center;
		padding: 15px 0;
	}
	
	.b-info-section .ui-title-block {
		font-size: 1.75rem !important;
	}
	
	.b-info-section .list {
		text-align: left;
		margin-top: 20px;
	}
	
	.b-info-section .list li {
		margin-bottom: 15px;
		padding-left: 25px;
	}
	
	.b-info-section .list li h3 {
		font-size: 1.1rem;
		margin-bottom: 8px;
	}
	
	.b-info-section .list li p {
		font-size: 0.95rem;
		line-height: 1.6;
	}
}

@media (max-width: 480px) {
	.b-info-section {
		padding: 25px 0;
	}
	
	.b-info-section .ui-title-block {
		font-size: 1.5rem !important;
	}
	
	.b-info-section__img-1,
	.b-info-section__img-2 {
		margin-bottom: 10px;
	}
	
	.b-info-section .list li {
		margin-bottom: 12px;
		padding-left: 20px;
	}
	
	.b-info-section .list li h3 {
		font-size: 1rem;
	}
	
	.b-info-section .list li p {
		font-size: 0.9rem;
	}
}
</style>
@endpush
<section class="b-info-section">
	<div class="container-fluid">
		@php
		$whyChooseUsSection = $homePage->activeSections->where('name', 'why-choose-us')->first();
		$settings = $whyChooseUsSection->settings ?? [];
		$images = $settings['images'] ?? [];
		$firstImage = !empty($images) && is_array($images) && isset($images[0]) ? $images[0] :
		asset('frontend/assets/media/components/b-info-section/1.png');
		$secondImage = !empty($images) && is_array($images) && isset($images[1]) ? $images[1] :
		asset('frontend/assets/media/components/b-info-section/2.png');
		@endphp
		<div class="row">
			<div class="col-lg-7 col-md-6">
				<div class="row">
					<div class="col-sm-6"><img src="{{ $firstImage }}"
							alt="{{ $whyChooseUsSection->title ?? 'Why Choose Us' }}"
							class="b-info-section__img-1 img-mask" />
					</div>
					<div class="col-sm-6"><img src="{{ $secondImage }}"
							alt="{{ $whyChooseUsSection->title ?? 'Why Choose Us' }}"
							class="b-info-section__img-2 img-mask" />
					</div>
				</div>
			</div>
			<div class="col-lg-5 col-md-6">
				<div class="b-info-section__inner">
					<div class="ui-decor-1"><img
							src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
							alt="decor" /></div>
					<h2 class="ui-title-block">{{ $whyChooseUsSection->title }}</h2>
					<div class="ui-subtitle-block">
						{{ $whyChooseUsSection->subtitle ?? '' }}
					</div>
					{!! formatCmsContent($whyChooseUsSection->description ?? '') !!}
					<ul class="list list-mark-5 list_bold list_icon_color-primary">

						@foreach($whyChooseUsSection->items as $item)
						<li>
							<h3>
								{{ $item->title }}
							</h3>
							<p>{!! formatCmsContent( $item->content
								?? '') !!}
							</p>

						</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>