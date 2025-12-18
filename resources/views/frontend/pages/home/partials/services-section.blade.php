<section class="b-services area-bg area-bg_dark area-bg_op_90 parallax">
	<div class="area-bg__inner">
		<div class="container">
			@php
			$servicesSection = $homePage->activeSections->where('name', 'services')->first();
			@endphp
			<div class="row" style="display:flex">
				<div class="col-md-5">
					<div class="ui-decor-1"><img
							src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
							alt="decor" /></div>
					<h2 class="ui-title-block">{{ $servicesSection->title }}</h2>
					<div class="ui-subtitle-block">{{ $servicesSection->subtitle }}</div>
					{!! formatCmsContent($servicesSection->description) !!}
				</div>
				<div class="col-md-7">
					<div class="bxslider">
						@foreach($servicesSection->items as $item)
						<section class="b-advantages-2 b-advantages-2_light">
							@if($item->icon)
							@php
							$iconClass = trim($item->icon);
							@endphp
							<i
								class="b-advantages-2__icon text-primary {{ $iconClass }}"></i>
							@endif
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									{{ $item->title }}
								</h3>
								<div class="b-advantages-2__info">
									{!!
									formatCmsContent($item->content)
									!!}
								</div>
							</div>
						</section>
						@endforeach


						<!-- end .b-advantages-->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>