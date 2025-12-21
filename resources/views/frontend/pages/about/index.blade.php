@extends('frontend.layouts.app')

@section('content')

@php
$aboutSection = $aboutPage->activeSections->where('name', 'about')->first();
@endphp
<section class="section-default" style="margin-bottom: 80px;">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block ui-title-block_weight_normal">
						{{ $aboutSection->title }}
					</h2>
					<div class="ui-subtitle-block">
						{!! formatCmsContent($aboutSection->description) !!}
					</div>

				</div>
			</div>
		</div>
		<div class="row" style="display:flex">
			@foreach($aboutSection->items as $item)
			<div class="col-sm-4">
				<section class="b-post-sm b-post-sm-1 b-post-sm-1_align_center clearfix">
					<!-- <div class="entry-media"><a
							href="assets/media/content/posts/322x180/5.jpg"
							class="js-zoom-images"><img
								src="assets/media/content/posts/322x180/5.jpg"
								alt="Foto" class="img-responsive" /></a>
					</div> -->
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