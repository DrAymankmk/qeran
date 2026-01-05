@extends('frontend.layouts.app')

@push('styles')
@include('frontend.partials.responsive-title-page')
<style>
/* About Page Responsive Styles */
@media (max-width: 767px) {
	.b-title-page {
		padding: 40px 0 !important;
		min-height: auto !important;
	}
	
	.b-title-page__title {
		font-size: 1.75rem !important;
		margin-bottom: 15px;
	}
	
	.section-default {
		padding: 30px 0 !important;
	}
	
	.b-post-sm {
		margin-bottom: 30px;
	}
	
	.b-post-sm .col-sm-4 {
		width: 100%;
		margin-bottom: 20px;
	}
	
	.entry-media img {
		width: 100%;
		height: auto;
		margin-bottom: 15px;
	}
	
	.entry-title {
		font-size: 1.25rem !important;
	}
	
	.row[style*="display:flex"] {
		flex-direction: column;
	}
	
	.row[style*="display:flex"] > .col-sm-4 {
		width: 100%;
		flex: 1 1 100%;
	}
}

@media (max-width: 480px) {
	.b-title-page__title {
		font-size: 1.5rem !important;
	}
	
	.entry-title {
		font-size: 1.1rem !important;
	}
	
	.entry-content p {
		font-size: 0.9rem;
		line-height: 1.6;
	}
}
</style>
@endpush

@section('content')

@php
$aboutSection = $aboutPage->activeSections->where('name', 'about')->first();
@endphp
<div class="b-title-page area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="ui-decor-2 ui-decor-2_vert bg-primary"></div>
					<h1 class="b-title-page__title">{{ $aboutPage->title }}</h1>
					<ol class="breadcrumb">
						<li><a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
						</li>
						<li class="active">{{ __('frontend.about') }}</li>
					</ol>
					<!-- end breadcrumb-->
				</div>
			</div>
		</div>
	</div>
</div>
<section class="section-default" style="margin-bottom: 80px;">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block">{{ $aboutSection->title ?? '' }}</h2>
					<div class="ui-subtitle-block">{{ $aboutSection->subtitle ?? '' }}
					</div>
				</div>
			</div>
		</div>

		<div class="row" style="display:flex">
			@foreach($aboutSection->items as $item)
			<div class="col-sm-4">
				<section class="b-post-sm b-post-sm-1 b-post-sm-1_align_center clearfix">
					@if($item->getMedia('images')->count() > 0)
					<div class="entry-media"><a
							href="{{ $item->images->first()->getUrl() }}"
							class="js-zoom-images"><img
								src="{{ $item->images->first()->getUrl() }}"
								alt="{{ $item->images->first()->getCustomProperty('alt_text', $item->title) }}"
								class="img-responsive" /></a>
					</div>
					@endif
					<div class="entry-main">
						<div class="entry-header">
							<div
								class="ui-decor-2 ui-decor-2_vert bg-primary">
							</div>
							<h2
								class="entry-title entry-title_spacing ui-title-inner">
								{{ $item->title }}
							</h2>
						</div>
						<div class="entry-content">
							<p>{!! formatCmsContent($item->content) !!}</p>
						</div>
					</div>
				</section>
				<!-- end post-->

			</div>
			@endforeach

		</div>
	</div>
</section>



@endsection