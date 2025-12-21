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
							<span>
								{{ $item->title }}
							</span><br>
							<p>{{ $item->content ?? '' }}</p>

						</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
