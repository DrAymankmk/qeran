@extends('frontend.layouts.app')

@push('styles')
@include('frontend.partials.responsive-title-page')
<style>
/* FAQ Page Responsive Styles */
@media (max-width: 767px) {
	.b-title-page {
		padding: 40px 0 !important;
		min-height: auto !important;
	}
	
	.b-title-page__title {
		font-size: 1.75rem !important;
	}
	
	.section-default {
		padding: 30px 0 !important;
	}
	
	.b-post-1 {
		margin-bottom: 30px;
		padding-bottom: 25px;
		border-bottom: 1px solid #e9ecef;
	}
	
	.b-post-1:last-child {
		border-bottom: none;
	}
	
	.entry-media {
		margin-bottom: 20px;
	}
	
	.entry-media img,
	.entry-media video {
		width: 100%;
		height: auto;
		border-radius: 8px;
	}
	
	.entry-title {
		font-size: 1.25rem !important;
		margin-bottom: 15px;
	}
	
	.entry-content {
		font-size: 0.95rem;
		line-height: 1.7;
	}
	
	.entry-content p {
		margin-bottom: 15px;
	}
	
	.l-main-content {
		padding: 0 10px;
	}
}

@media (max-width: 480px) {
	.b-title-page__title {
		font-size: 1.5rem !important;
	}
	
	.ui-title-block {
		font-size: 1.5rem !important;
	}
	
	.entry-title {
		font-size: 1.1rem !important;
	}
	
	.entry-content {
		font-size: 0.9rem;
	}
	
	.b-post-1 {
		margin-bottom: 25px;
		padding-bottom: 20px;
	}
	
	.entry-media {
		margin-bottom: 15px;
	}
}
</style>
@endpush

@section('content')
@php
$faqSection = $faqPage->activeSections->where('name', 'faq')->first();
@endphp
<div class="b-title-page area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="ui-decor-2 ui-decor-2_vert bg-primary"></div>
					<h1 class="b-title-page__title">{{ $faqSection->title }}</h1>
					<ol class="breadcrumb">
						<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
						</li>
						<li class="active">{{ __('frontend.faq') }}</li>
					</ol>
					<!-- end breadcrumb-->
				</div>
			</div>
		</div>
	</div>
</div>
<section class="section-default">
	<div class="container">

		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">{{ $faqSection->title ?? '' }}</h2>
					<div class="ui-subtitle-block">{{ $faqSection->subtitle ?? '' }}
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="l-main-content posts-group">
					@foreach($faqSection->items as $item)
					<section class="b-post-1 clearfix">
						@php
						$mediaItems = $item->getMedia('images');
						$firstMedia = $mediaItems->first();
						@endphp
						@if($firstMedia)
						@php
						$mimeType = $firstMedia->mime_type ?? '';
						$isVideo = strpos($mimeType, 'video/') === 0;
						$isImage = strpos($mimeType, 'image/') === 0;
						@endphp
						<div class="entry-media">
							@if($isImage)
							<a href="{{ $firstMedia->getUrl() }}"
								class="js-zoom-images">
								<img src="{{ $firstMedia->getUrl() }}"
									alt="{{ $firstMedia->getCustomProperty('alt_text', $item->title) }}"
									class="img-responsive" />
							</a>
							@elseif($isVideo)
							<video controls class="img-responsive"
								style="width: 100%; max-width: 100%; height: auto;">
								<source src="{{ $firstMedia->getUrl() }}"
									type="{{ $mimeType }}">
								{{ __('frontend.video-not-supported') }}
							</video>
							@endif
						</div>
						@endif

						<div class="entry-main">
							<div class="entry-header">
								<div
									class="ui-decor-2 ui-decor-2_vert bg-primary">
								</div>

								<h2 class="entry-title">
									{{ $item->title }}</h2>
							</div>
							<div class="entry-content">
								<p>{!! formatCmsContent($item->content)
									!!}</p>
							</div>

						</div>
					</section>
					@endforeach
					<!-- end post-->






				</div>
			</div>

		</div>
	</div>
</section>
@endsection
