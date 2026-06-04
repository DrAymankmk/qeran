<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>{{ $invitation->event_name }} — {{ __('admin.invitation-builder-live-preview') }}</title>
	@if(empty($useBuilderWedding))
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js"></script>
	@endif
	<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
	<link href="https://fonts.bunny.net/css?family=cairo:400,600,700|playfair-display:400,700|cormorant-garamond:400,600|great-vibes:400" rel="stylesheet">
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body {
			min-height: 100vh;
			background: {{ !empty($useBuilderWedding) ? '#faf7f2' : 'linear-gradient(135deg, #121223 0%, #1a1a3a 50%, #2d2d5f 100%)' }};
			font-family: "Cairo", sans-serif;
			overflow-x: hidden;
		}
		.ib-preview-shell {
			min-height: 100vh;
			display: flex;
			align-items: {{ !empty($useBuilderWedding) ? 'stretch' : 'center' }};
			justify-content: {{ !empty($useBuilderWedding) ? 'stretch' : 'center' }};
			padding: {{ !empty($useBuilderWedding) ? '0' : '16px 8px 32px' }};
			width: 100%;
		}
		.ib-preview-badge {
			position: fixed;
			top: 8px;
			left: 50%;
			transform: translateX(-50%);
			z-index: 10000;
			background: rgba(0,0,0,0.55);
			color: #fff;
			font-size: 11px;
			padding: 4px 12px;
			border-radius: 20px;
			pointer-events: none;
		}
	</style>
	@if(empty($builderConfig['music_enabled']))
	<style>#inviteOpeningAudio { display: none !important; }</style>
	@endif
</head>
<body>
	@include('invitation.partials.builder-theme')
	<span class="ib-preview-badge">{{ __('admin.invitation-builder-live-preview') }}</span>
	<div class="ib-preview-shell" style="flex-direction: column;">
		@include($view)
	</div>
	@if(!empty($useBuilderWedding))
	@include('admin.invitation-builder.partials.preview-wedding-bridge')
	@else
	@include('admin.invitation-builder.preview-scripts')
	@endif
</body>
</html>
