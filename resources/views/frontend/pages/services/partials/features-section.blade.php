@php
$servicesSection = $servicesPage->activeSections->where('name', 'services')->first();
@endphp
<section class="section-advantages">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor" class="center-block"></div>
				<div class="text-center">
					<h2 class="ui-title-block ui-title-block_weight_normal">
						{{ $servicesSection->title }}</h2>
					<div class="ui-subtitle-block">{{ $servicesSection->subtitle }}</div>
					{!! formatCmsContent($servicesSection->description) !!}
				</div>
			</div>
		</div>
		<div class="row">
			@foreach($servicesSection->activeItems as $item)

			<div class="col-md-4 col-sm-6">
				<section class="b-advantages b-advantages-1">
					@if($item->icon)
					@php
					$iconClass = trim($item->icon);

					// Normalize Font Awesome icons to Font Awesome 6 format
					if (strpos($iconClass, 'fa-') !== false || strpos($iconClass, 'fas ')
					!== false || strpos($iconClass, 'far ') !== false ||
					strpos($iconClass, 'fab ') !== false) {
					// Convert old 'fa fa-' format to 'fas fa-'
					if (strpos($iconClass, 'fa fa-') === 0) {
					$iconClass = str_replace('fa fa-', 'fas fa-', $iconClass);
					}
					// If it starts with just 'fa-' add 'fas' prefix
					elseif (strpos($iconClass, 'fa-') === 0 &&
					!preg_match('/^(fas|far|fab)\s+fa-/', $iconClass)) {
					$iconClass = 'fas ' . $iconClass;
					}
					}
					@endphp
					<i class="b-advantages__icon text-primary {{ $iconClass }}"></i>
					@else
					<i class="b-advantages__icon text-primary icon-emoticon-smile"></i>
					@endif
					<div class="b-advantages__inner">
						<h3 class="b-advantages__title ui-title-inner">
							{{ $item->title }}
						</h3>
						<div class="b-advantages__info">{!!
							formatCmsContent($item->content) !!}</div>
					</div>
				</section>
				<!-- end .b-advantages-->

			</div>
			@endforeach

		</div>
	</div>
</section>