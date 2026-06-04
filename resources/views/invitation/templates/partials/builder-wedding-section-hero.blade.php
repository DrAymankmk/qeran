  <!-- ① Hero (dynamic) -->
  <section class="wi-hero wi-date-pos-{{ $wiDatePosition }}@if(!empty($wiHeroHasVideo)) wi-hero-has-video @endif">
  	@if(!empty($wiHeroHasVideo) && !empty($wiHeroVideoUrl))
  	<div class="wi-hero-media" aria-hidden="true">
  		<video class="wi-hero-video" autoplay muted loop playsinline preload="metadata"
  			src="{{ $wiHeroVideoUrl }}"></video>
  		<div class="wi-hero-video-overlay"></div>
  	</div>
  	@endif

  	<!-- <svg class="wi-corner tl" width="60" height="60" viewBox="0 0 60 60" fill="none">
  		<path d="M4 56 L4 4 L56 4" stroke="currentColor" stroke-width="0.8" fill="none" />
  		<path d="M10 50 L10 10 L50 10" stroke="currentColor" stroke-width="0.4" fill="none" />
  		<circle cx="4" cy="4" r="2.5" fill="currentColor" />
  	</svg>
  	<svg class="wi-corner tr" width="60" height="60" viewBox="0 0 60 60" fill="none">
  		<path d="M4 56 L4 4 L56 4" stroke="currentColor" stroke-width="0.8" fill="none" />
  		<path d="M10 50 L10 10 L50 10" stroke="currentColor" stroke-width="0.4" fill="none" />
  		<circle cx="4" cy="4" r="2.5" fill="currentColor" />
  	</svg>
  	<svg class="wi-corner bl" width="60" height="60" viewBox="0 0 60 60" fill="none">
  		<path d="M4 56 L4 4 L56 4" stroke="currentColor" stroke-width="0.8" fill="none" />
  		<path d="M10 50 L10 10 L50 10" stroke="currentColor" stroke-width="0.4" fill="none" />
  		<circle cx="4" cy="4" r="2.5" fill="currentColor" />
  	</svg>
  	<svg class="wi-corner br" width="60" height="60" viewBox="0 0 60 60" fill="none">
  		<path d="M4 56 L4 4 L56 4" stroke="currentColor" stroke-width="0.8" fill="none" />
  		<path d="M10 50 L10 10 L50 10" stroke="currentColor" stroke-width="0.4" fill="none" />
  		<circle cx="4" cy="4" r="2.5" fill="currentColor" />
  	</svg> -->

  	@if($wiDateBadge || $wiHostLabel)
  	<p class="wi-date-badge wi-fade-in d1">
  		@if($wiHostLabel && $wiDateBadge){{ $wiHostLabel }} ·
  		{{ $wiDateBadge }}@elseif($wiDateBadge){{ $wiDateBadge }}@else{{ $wiHostLabel }}@endif
  	</p>
  	@endif

  	<h1 class="wi-names wi-fade-in d2">
  		{{ $wiName1 }}<br>
  		<span class="wi-ampersand">&</span><br>
  		{{ $wiName2 ?: $wiName1 }}
  	</h1>

  	<p class="wi-subtitle wi-fade-in d3">{{ $wiSubtitle }}</p>

  	<div class="wi-divider wi-fade-in d4">
  		<div class="wi-divider-diamond"></div>
  	</div>

  	@if($wiHeroDetail)
  	<div class="wi-hero-detail wi-fade-in d5">{!! $wiHeroDetail !!}</div>
  	@endif

  	<div class="wi-scroll-hint">
  		<svg width="14" height="22" viewBox="0 0 14 22" fill="none">
  			<rect x="1" y="1" width="12" height="20" rx="6" stroke="currentColor"
  				stroke-width="0.8" />
  			<circle cx="7" cy="7" r="1.5" fill="currentColor">
  				<animate attributeName="cy" values="7;14;7" dur="1.8s"
  					repeatCount="indefinite" />
  				<animate attributeName="opacity" values="1;0;1" dur="1.8s"
  					repeatCount="indefinite" />
  			</circle>
  		</svg>
  		مرر
  	</div>
  </section>