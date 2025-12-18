@extends('frontend.layouts.app')


@section('content')
@php
$faqSection = $faqPage->activeSections->where('name', 'faq')->first();
@endphp
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