@php
$designsSection = $homePage->activeSections->where('name', 'designs')->first();
@endphp
<section class="section-default">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="ui-decor-1"><img
						src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
						alt="decor"></div>
				<h2 class="ui-title-block">{{ $designsSection->title }}</h2>
				<div class="ui-subtitle-block">{{$designsSection->subtitle}}</div>
			</div>
		</div>
	</div>
	<div class="b-isotope">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<ul class="b-isotope-filter list-inline">
						<li><a href="" data-filter="*"
								class="current">{{ __('frontend.all designs') }}</a>
						</li>
						@foreach($categories as $category)
						<li><a href=""
								data-filter=".category-{{ $category->id }}">{{ $category->name }}</a>
						</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		<ul class="b-isotope-grid grid list-unstyled js-design-gallery">
			<li class="grid-sizer"></li>
			@foreach($categories as $category)
			@foreach($category->designs as $design)
			@php
			// Filter designs that should be shown on home page
			$showOn = $design->show_on ?? [];
			if (!is_array($showOn)) {
			$showOn = [];
			}
			// Skip if design doesn't have "home" in show_on
			if (!in_array('home', $showOn)) {
			continue;
			}
			$designImage = $design->image();
			$designName = $design->name ?? '';
			$designCode = $design->code ?? '';
			$categorySlug = $category->slug ?? 'category-' . $category->id;
			@endphp
			@if($designImage)
			<li class="b-isotope-grid__item grid-item category-{{ $category->id }}">
				<a href="javascript:void(0);" class="b-isotope-grid__inner design-item"
					data-design-id="{{ $design->id }}"
					data-design-name="{{ htmlspecialchars($designName, ENT_QUOTES, 'UTF-8') }}"
					data-design-code="{{ htmlspecialchars($designCode, ENT_QUOTES, 'UTF-8') }}"
					data-design-image="{{ htmlspecialchars($designImage, ENT_QUOTES, 'UTF-8') }}">
					<img src="{{ $designImage }}" alt="{{ $designName ?: 'Design' }}" />
					<span class="b-isotope-grid__wrap-info">
						<span class="b-isotope-grid__info">
							@if($designName)
							<span
								class="b-isotope-grid__title">{{ $designName }}</span>
							@endif
							<span
								class="b-isotope-grid__categorie">{{ $category->name }}</span>
						</span>
						<i class="icon icon-magnifier-add text-primary"></i>
					</span>
				</a>
			</li>
			@endif
			@endforeach
			@endforeach
		</ul>

		<div class="text-center"><span class="b-isotope__info">
			{{__('frontend.see_our_full_gallery_of_designs')}}
		</span><a href="{{ route('gallery') }}"
				class="b-isotope__btn btn btn-primary">{{ __('frontend.visit_full_gallery') }}
			</a>
		</div>
	</div>
	<!-- end .b-isotope-->
	<!-- <div class="text-center"><span class="b-isotope__info">See Our Full Gallery of
			Designs!</span><a href="home.html" class="b-isotope__btn btn btn-primary">visit full
			gallery</a></div> -->
</section>

<!-- Design Modal -->
<div class="modal fade" id="designModal" tabindex="-1" role="dialog" aria-labelledby="designModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="designModalLabel">Design Details</h4>
			</div>
			<div class="modal-body text-center">
				<div class="design-modal-image" style="margin-bottom: 20px;">
					<img id="modalDesignImage" src="" alt="Design" class="img-responsive"
						style="max-width: 100%; height: auto; margin: 0 auto;" />
				</div>
				<div class="design-modal-info">
					<h3 id="modalDesignName" style="margin-bottom: 10px;"></h3>
					<p id="modalDesignCodeContainer" style="display: none;">
						<strong>Code:</strong> <span id="modalDesignCode"></span>
					</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default"
					data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>