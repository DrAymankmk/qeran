@extends('frontend.layouts.app')


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
