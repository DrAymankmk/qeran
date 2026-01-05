@push('styles')
<style>
/* About Section Responsive */
@media (max-width: 991px) {
	.section-type-1 .col-md-4,
	.section-type-1 .col-md-8 {
		margin-bottom: 30px;
	}
	
	.section-type-1__inner {
		padding: 20px 0;
	}
}

@media (max-width: 767px) {
	.section-type-1 {
		padding: 30px 0;
	}
	
	.section-type-1 .col-md-4 {
		margin-bottom: 25px;
	}
	
	.section-type-1 .col-md-8 {
		margin-bottom: 0;
	}
	
	.section-type-1__inner {
		padding: 15px 0;
		text-align: center;
	}
	
	.section-type-1 img,
	.section-type-1 video {
		width: 100%;
		height: auto;
		margin-bottom: 20px;
	}
	
	.ui-title-block {
		font-size: 1.75rem !important;
		margin-bottom: 15px;
	}
	
	.ui-subtitle-block {
		font-size: 1rem !important;
		margin-bottom: 20px;
	}
	
	.btn {
		margin: 5px;
		display: inline-block;
		width: auto;
		min-width: 150px;
	}
}

@media (max-width: 480px) {
	.section-type-1 {
		padding: 25px 0;
	}
	
	.ui-title-block {
		font-size: 1.5rem !important;
	}
	
	.btn {
		width: 100%;
		margin: 5px 0;
	}
}
</style>
@endpush
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

						@if($aboutSection->links && $aboutSection->links->count() >
						0)
						@foreach($aboutSection->links as $link)
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
					</div>
				</div>
			</div>
		</div>
	</div>
</section>