<section class="section-type-1">
	<div class="label-vertical">
		<div class="container">
			@php
			$aboutSection = $homePage->activeSections->where('name', 'about')->first();
			@endphp
			<div class="row">
				<div class="col-md-4">
					@php
					$settings = $aboutSection->settings ?? [];
					$videoUrl = $settings['video'] ?? null;
					$images = $settings['images'] ?? [];
					$firstImage = !empty($images) && is_array($images) ? $images[0] :
					null;
					$defaultImage = asset('frontend/assets/media/content/360x460/1.jpg');
					@endphp

					@if($videoUrl)
					{{-- Show video if available --}}
					<video class="img-w-radius img-responsive" controls
						style="width: 100%; height: auto; border-radius: 8px;">
						<source src="{{ $videoUrl }}" type="video/mp4">
						Your browser does not support the video tag.
					</video>
					@elseif($firstImage)
					{{-- Show image if available --}}
					<img src="{{ $firstImage }}"
						alt="{{ $aboutSection->title ?? 'About' }}"
						class="img-w-radius img-responsive">
					@else
					{{-- Show default image --}}
					<img src="{{ $defaultImage }}" alt="foto"
						class="img-w-radius img-responsive">
					@endif
				</div>
				<div class="col-md-8">
					<div class="section-type-1__inner">
						<div class="ui-decor-1"><img
								src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
								alt="decor"></div>
						<h2 class="ui-title-block">
							{{ $aboutSection->title }}</h2>
						<div class="ui-subtitle-block">
							{{ $aboutSection->subtitle }}
						</div>
						{!! formatCmsContent($aboutSection->description) !!}


						<a href="home.html" class="btn btn-default btn-xs"><i
								class="icon"></i>Read
							More</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
