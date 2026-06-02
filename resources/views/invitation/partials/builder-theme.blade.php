@if(!empty($builderConfig))
@php
	$bc = $builderConfig;
	$isLight = ($bc['theme_mode'] ?? 'dark') === 'light';
@endphp
<style>
:root {
	--ib-primary: {{ $bc['primary_color'] }};
	--ib-secondary: {{ $bc['secondary_color'] }};
	--ib-bg: {{ $bc['background_color'] }};
	--ib-text: {{ $bc['text_color'] }};
	--ib-font: '{{ $bc['font_family'] }}', 'Cairo', sans-serif;
}
body.invitation-builder-active {
	background: var(--ib-bg) !important;
	font-family: var(--ib-font) !important;
	color: var(--ib-text);
}
body.invitation-builder-active::before { opacity: 0.35; }
body.invitation-builder-active.theme-light {
	--ib-bg: #f5f0e8;
	--ib-text: #2d2435;
}
.invitation-builder-welcome {
	position: fixed;
	inset: 0;
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
	background: linear-gradient(160deg, var(--ib-bg), color-mix(in srgb, var(--ib-primary) 25%, var(--ib-bg)));
	padding: 24px;
	text-align: center;
}
.invitation-builder-welcome.hidden { display: none; }
.invitation-builder-welcome-inner {
	max-width: 420px;
	animation: ib-fade-in 0.8s ease;
}
.invitation-builder-welcome .ib-logo {
	max-width: 120px;
	max-height: 80px;
	margin-bottom: 20px;
}
.invitation-builder-welcome h1 {
	font-size: clamp(1.5rem, 5vw, 2.2rem);
	margin-bottom: 12px;
	color: var(--ib-text);
}
.invitation-builder-welcome p {
	font-size: 1.1rem;
	opacity: 0.85;
	margin-bottom: 28px;
}
.invitation-builder-welcome .ib-open-btn {
	background: linear-gradient(135deg, var(--ib-primary), color-mix(in srgb, var(--ib-primary) 70%, #000));
	color: #fff;
	border: none;
	padding: 14px 36px;
	border-radius: 50px;
	font-size: 1.1rem;
	font-weight: 700;
	cursor: pointer;
	box-shadow: 0 8px 28px color-mix(in srgb, var(--ib-primary) 45%, transparent);
}
.invitation-builder-bg-media {
	position: fixed;
	inset: 0;
	z-index: -2;
	object-fit: cover;
	width: 100%;
	height: 100%;
	pointer-events: none;
}
.invitation-builder-bg-overlay {
	position: fixed;
	inset: 0;
	z-index: -1;
	background: color-mix(in srgb, var(--ib-bg) 75%, transparent);
	pointer-events: none;
}
@keyframes ib-fade-in {
	from { opacity: 0; transform: translateY(12px); }
	to { opacity: 1; transform: translateY(0); }
}
{!! $bc['custom_css'] ?? '' !!}
</style>

@if(!empty($bc['background_media_url']) && !empty($bc['video_background']))
<video class="invitation-builder-bg-media" autoplay muted loop playsinline src="{{ $bc['background_media_url'] }}"></video>
<div class="invitation-builder-bg-overlay"></div>
@elseif(!empty($bc['background_media_url']))
<div class="invitation-builder-bg-media" style="background:url('{{ $bc['background_media_url'] }}') center/cover no-repeat;"></div>
<div class="invitation-builder-bg-overlay"></div>
@endif

@if(!empty($bc['intro_video_enabled']) && !empty($bc['background_media_url']) && !empty($bc['video_background']))
<div id="invitationBuilderIntro" class="invitation-builder-welcome">
	<video class="invitation-builder-intro-video" playsinline src="{{ $bc['background_media_url'] }}"></video>
	<div class="invitation-builder-welcome-inner" style="position:relative;z-index:2;">
		<button type="button" class="ib-open-btn" onclick="invitationBuilderDismissIntro()">تخطي / افتح الدعوة</button>
	</div>
</div>
<script>
function invitationBuilderDismissIntro() {
	var el = document.getElementById('invitationBuilderIntro');
	if (el) el.classList.add('hidden');
	var v = el && el.querySelector('video');
	if (v) { v.pause(); }
}
document.addEventListener('DOMContentLoaded', function() {
	var v = document.querySelector('#invitationBuilderIntro video');
	if (v) { v.play().catch(function(){}); }
});
</script>
<style>
.invitation-builder-intro-video {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
	z-index: 0;
}
</style>
@endif

@if(!empty($bc['welcome_enabled']) || ($bc['opening_type'] ?? '') === 'welcome')
<div id="invitationBuilderWelcome" class="invitation-builder-welcome @if(!empty($bc['intro_video_enabled'])) hidden @endif">
	<div class="invitation-builder-welcome-inner">
		@if(!empty($bc['logo_url']))
		<img src="{{ $bc['logo_url'] }}" alt="" class="ib-logo">
		@endif
		<h1>{{ $bc['welcome_title'] }}</h1>
		<p>{{ $bc['welcome_subtitle'] }}</p>
		<button type="button" class="ib-open-btn" onclick="invitationBuilderDismissWelcome()">
			افتح الدعوة
		</button>
	</div>
</div>
<script>
function invitationBuilderDismissWelcome() {
	var el = document.getElementById('invitationBuilderWelcome');
	if (el) el.classList.add('hidden');
}
</script>
@endif

<script>
document.body.classList.add('invitation-builder-active');
@if($isLight) document.body.classList.add('theme-light'); @endif
</script>
@endif
