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
			<div class="col-md-12">
				<div class="l-main-content posts-group">
					@foreach($faqSection->items as $item)
					<section class="b-post-1 clearfix">

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
