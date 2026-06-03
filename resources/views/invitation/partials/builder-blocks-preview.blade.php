@if(!empty($builderConfig['blocks']) && is_array($builderConfig['blocks']))
@php
	$blockCatalog = config('invitation_builder.information_blocks', []);
	$floral = !empty($builderConfig['block_floral_border']);
@endphp
<style>
.ib-blocks-preview {
	max-width: min(600px, 92vw);
	margin: 24px auto 0;
	padding: 0 12px 40px;
	position: relative;
	z-index: 3;
}
.ib-block-card {
	background: color-mix(in srgb, var(--ib-bg) 88%, #fff);
	border-radius: 12px;
	padding: 16px 18px;
	margin-bottom: 12px;
	border: 1px solid color-mix(in srgb, var(--ib-block-accent) 35%, transparent);
	@if($floral)
	border-image: linear-gradient(135deg, var(--ib-block-accent), var(--ib-secondary)) 1;
	@endif
}
.ib-block-card h4 {
	margin: 0 0 6px;
	font-size: 1rem;
	color: var(--ib-block-accent);
	font-family: var(--ib-headline-font);
}
.ib-block-card p { margin: 0; font-size: 0.9rem; opacity: 0.85; }
</style>
<div class="ib-blocks-preview">
	@if(!empty($builderConfig['opening_headline']))
	<div class="ib-builder-headline ib-builder-date-pos-{{ $builderConfig['date_position'] ?? 'center' }} mb-3">
		<h2 style="font-size: clamp(1.25rem, 4vw, 1.75rem); line-height: 1.4; white-space: pre-line;">{{ $builderConfig['opening_headline'] }}</h2>
		@if(!empty($builderConfig['event_date']))
		<p style="opacity: 0.8; margin-top: 8px;">
			{{ \Illuminate\Support\Carbon::parse($builderConfig['event_date'])->locale('ar')->translatedFormat('l، j F Y') }}
			@if(!empty($builderConfig['event_time']))
			 — {{ \Illuminate\Support\Carbon::parse($builderConfig['event_time'])->format('h:i A') }}
			@endif
		</p>
		@endif
	</div>
	@endif
	@foreach($builderConfig['blocks'] as $blockKey)
		@if(isset($blockCatalog[$blockKey]))
		@php $b = $blockCatalog[$blockKey]; @endphp
		<div class="ib-block-card">
			<h4>{{ $b['icon'] }} {{ $b['label_ar'] }}</h4>
			<p>{{ $b['description_ar'] }}</p>
		</div>
		@endif
	@endforeach
</div>
@endif
