@php
$showEnvelope = $showEnvelope ?? true;
$skipEnvelope = ($initialView ?? '') === 'success' || ($initialView ?? '') === 'decline';
$wiSealShape = $wiSealShape ?? 'wax-round';
$wiSealPalette = $wiSealPalette ?? 'crimson';
$wiSealRing = $wiSealRing ?? true;
$wiSealDrip = $wiSealDrip ?? true;
$envImageUrl = $wiEnvelopeImageUrl ?? '';
$envBodyImageUrl = $wiEnvelopeBodyImageUrl ?? $envImageUrl;
$envFlapImageUrl = $wiEnvelopeFlapImageUrl ?? $envImageUrl;
$hasEnvImage = ! empty($wiEnvelopeHasImage) || $envImageUrl !== '';
$envLayout = is_array($wiEnvelopePhotoLayout ?? null) ? $wiEnvelopePhotoLayout : [];
$envImageFit = in_array($envLayout['body_fit'] ?? ($wiEnvelopeImageFit ?? 'contain'), ['contain', 'cover'], true)
? ($envLayout['body_fit'] ?? ($wiEnvelopeImageFit ?? 'contain'))
: 'contain';
$envBodyPosition = (string) ($envLayout['body_position'] ?? 'center');
$envBodyClip = (string) ($envLayout['body_clip_path'] ?? '');
$envShowPocketLiner = (bool) ($envLayout['show_pocket_liner'] ?? true);
$envHasBodyFlapSplit = (bool) ($envLayout['has_body_flap_split'] ?? ($envBodyClip !== ''));
$envHasSeparateFlap = (bool) ($envLayout['has_separate_flap'] ?? false);
$envStockSlug = (string) ($envLayout['stock_slug'] ?? '');
$envFlapHeight = (string) ($envLayout['flap_height'] ?? '54%');
$envFlapTop = (string) ($envLayout['flap_top'] ?? '-8px');
$envFlapLeft = (string) ($envLayout['flap_left'] ?? '0');
$envFlapWidth = (string) ($envLayout['flap_width'] ?? '100%');
$envFlapClip = (string) ($envLayout['flap_clip_path'] ?? 'polygon(0 0, 50% 100%, 100% 0)');
$envFlapOrigin = (string) ($envLayout['flap_transform_origin'] ?? '50% 0%');
$envFlapImgFit = in_array($envLayout['flap_image_fit'] ?? 'cover', ['contain', 'cover'], true)
? ($envLayout['flap_image_fit'] ?? 'cover')
: 'cover';
$envFlapImgPosition = (string) ($envLayout['flap_image_position'] ?? 'center top');
$envFlapImgMinHeight = (string) ($envLayout['flap_image_min_height'] ?? '185%');
$envFlapOpenRotate = (string) ($envLayout['flap_open_rotate'] ?? '-168deg');
$envMobileBreakpoint = (int) ($envLayout['mobile_breakpoint'] ?? 767);
$envHasMobileFlapTune = (bool) ($envLayout['has_mobile_flap_tune'] ?? false);
$envBodyClipSm = (string) ($envLayout['body_clip_path_sm'] ?? '');
$envFlapClipSm = (string) ($envLayout['flap_clip_path_sm'] ?? '');
$envFlapHeightSm = (string) ($envLayout['flap_height_sm'] ?? '');
$envFlapTopSm = (string) ($envLayout['flap_top_sm'] ?? '');
$envFlapLeftSm = (string) ($envLayout['flap_left_sm'] ?? '');
$envFlapWidthSm = (string) ($envLayout['flap_width_sm'] ?? '');
$envFlapImgPositionSm = (string) ($envLayout['flap_image_position_sm'] ?? '');
$envFlapImgMinHeightSm = (string) ($envLayout['flap_image_min_height_sm'] ?? '');
$envEnvelopeWidth = (string) ($envLayout['envelope_width'] ?? '');
$envEnvelopeHeight = (string) ($envLayout['envelope_height'] ?? '');
$envEnvelopeMaxWidth = (string) ($envLayout['envelope_max_width'] ?? 'min(92vw, 420px)');
$envEnvelopeMaxHeight = (string) ($envLayout['envelope_max_height'] ?? 'min(90dvh, 520px)');
$envEnvelopeAspectRatio = (string) ($envLayout['envelope_aspect_ratio'] ?? '4 / 5.2');
$envSceneWidth = (string) ($envLayout['scene_width'] ?? 'min(92vw, 440px)');
$envSceneMinHeight = (string) ($envLayout['scene_min_height'] ?? 'min(420px, calc(100dvh - 118px))');
$envHasMobileSizeTune = (bool) ($envLayout['has_mobile_size_tune'] ?? false);
$envEnvelopeWidthSm = (string) ($envLayout['envelope_width_sm'] ?? '');
$envEnvelopeHeightSm = (string) ($envLayout['envelope_height_sm'] ?? '');
$envEnvelopeMaxWidthSm = (string) ($envLayout['envelope_max_width_sm'] ?? '');
$envEnvelopeMaxHeightSm = (string) ($envLayout['envelope_max_height_sm'] ?? '');
$envEnvelopeAspectRatioSm = (string) ($envLayout['envelope_aspect_ratio_sm'] ?? '');
$envSceneWidthSm = (string) ($envLayout['scene_width_sm'] ?? '');
$envSceneMinHeightSm = (string) ($envLayout['scene_min_height_sm'] ?? '');
$envSizeStyle = '--env-max-width: '.$envEnvelopeMaxWidth.'; --env-max-height: '.$envEnvelopeMaxHeight.'; --env-aspect-ratio: '.$envEnvelopeAspectRatio.'; --env-scene-width: '.$envSceneWidth.'; --env-scene-min-height: '.$envSceneMinHeight.';'
    .($envEnvelopeWidth !== '' ? ' --env-width: '.$envEnvelopeWidth.';' : '')
    .($envEnvelopeHeight !== '' ? ' --env-height: '.$envEnvelopeHeight.';' : '')
    .($envEnvelopeWidthSm !== '' ? ' --env-width-sm: '.$envEnvelopeWidthSm.';' : '')
    .($envEnvelopeHeightSm !== '' ? ' --env-height-sm: '.$envEnvelopeHeightSm.';' : '')
    .($envEnvelopeMaxWidthSm !== '' ? ' --env-max-width-sm: '.$envEnvelopeMaxWidthSm.';' : '')
    .($envEnvelopeMaxHeightSm !== '' ? ' --env-max-height-sm: '.$envEnvelopeMaxHeightSm.';' : '')
    .($envEnvelopeAspectRatioSm !== '' ? ' --env-aspect-ratio-sm: '.$envEnvelopeAspectRatioSm.';' : '')
    .($envSceneWidthSm !== '' ? ' --env-scene-width-sm: '.$envSceneWidthSm.';' : '')
    .($envSceneMinHeightSm !== '' ? ' --env-scene-min-height-sm: '.$envSceneMinHeightSm.';' : '');
$envPhotoStyle = '';
if ($hasEnvImage) {
    $envPhotoStyle = '--env-body-fit: '.$envImageFit.'; --env-body-position: '.$envBodyPosition.';'
        .($envBodyClip !== '' ? ' --env-body-clip: '.$envBodyClip.';' : '')
        .' --env-flap-left: '.$envFlapLeft.'; --env-flap-top: '.$envFlapTop.'; --env-flap-width: '.$envFlapWidth.'; --env-flap-height: '.$envFlapHeight.';'
        .' --env-flap-clip: '.$envFlapClip.'; --env-flap-origin: '.$envFlapOrigin.'; --env-flap-img-fit: '.$envFlapImgFit.';'
        .' --env-flap-img-position: '.$envFlapImgPosition.'; --env-flap-img-min-height: '.$envFlapImgMinHeight.'; --env-flap-open-rotate: '.$envFlapOpenRotate.';'
        .($envFlapClipSm !== '' ? ' --env-flap-clip-sm: '.$envFlapClipSm.';' : '')
        .($envBodyClipSm !== '' ? ' --env-body-clip-sm: '.$envBodyClipSm.';' : '')
        .($envFlapTopSm !== '' ? ' --env-flap-top-sm: '.$envFlapTopSm.';' : '')
        .($envFlapLeftSm !== '' ? ' --env-flap-left-sm: '.$envFlapLeftSm.';' : '')
        .($envFlapWidthSm !== '' ? ' --env-flap-width-sm: '.$envFlapWidthSm.';' : '')
        .($envFlapHeightSm !== '' ? ' --env-flap-height-sm: '.$envFlapHeightSm.';' : '')
        .($envFlapImgPositionSm !== '' ? ' --env-flap-img-position-sm: '.$envFlapImgPositionSm.';' : '')
        .($envFlapImgMinHeightSm !== '' ? ' --env-flap-img-min-height-sm: '.$envFlapImgMinHeightSm.';' : '');
}
$envEnvelopeStyle = trim($envSizeStyle.' '.$envPhotoStyle);
$wiSealStyleAttr = ! empty($wiSealInlineStyle ?? '') ? (string) ($wiSealInlineStyle ?? '') : '';
$envelopeBuilderConfig = $builderConfig ?? [];
$wiEnvelopeShape = $wiEnvelopeShape ?? 'classic';
@endphp
@if($showEnvelope && !$skipEnvelope)
<style>
.wi-envelope-gate {
	position: fixed;
	inset: 0;
	z-index: 10050;
	display: flex;
	flex-direction: column;
	min-height: 100dvh;
	height: 100dvh;
	background:
		radial-gradient(ellipse 120% 80% at 50% 0%, color-mix(in srgb, var(--wi-gold) 12%, transparent), transparent 55%),
		linear-gradient(175deg, color-mix(in srgb, var(--wi-bg, #1a1520) 92%, #000) 0%, color-mix(in srgb, var(--wi-text) 75%, #0a0806) 100%);
	padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
	transition: opacity 0.55s ease, visibility 0.55s ease;
	overflow: hidden;
}

.wi-envelope-gate::before {
	content: '';
	position: absolute;
	inset: 0;
	opacity: 0.04;
	background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
	pointer-events: none;
}

.wi-envelope-gate.is-open {
	opacity: 0;
	visibility: hidden;
	pointer-events: none;
}

.wi-env-stage {
	flex: 1;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 100%;
	min-height: 0;
	padding: clamp(12px, 3vh, 28px) clamp(10px, 4vw, 24px);
	perspective: 1400px;
}

.wi-env-scene {
	width: var(--env-scene-width, min(92vw, 440px));
	/* height: min(calc(100dvh - 118px), 82dvh); */
	height: 100%;
	/* max-height: calc(100dvh - 118px); */
	min-height: var(--env-scene-min-height, min(420px, calc(100dvh - 118px)));
	position: relative;
	display: flex;
	align-items: center;
	justify-content: center;
	transform-style: preserve-3d;
}

.wi-env-envelope {
	--env-paper: var(--wi-envelope, #f5f0e6);
	--env-paper-dark: color-mix(in srgb, var(--env-paper) 72%, #5c4a38);
	--env-paper-light: color-mix(in srgb, var(--env-paper) 88%, #fff);
	--env-paper-mid: color-mix(in srgb, var(--env-paper) 82%, var(--env-paper-dark));
	width: var(--env-width, 100%);
	height: var(--env-height, 100%);
	max-width: var(--env-max-width, min(92vw, 420px));
	max-height: var(--env-max-height, min(90dvh, 520px));
	aspect-ratio: var(--env-aspect-ratio, 4 / 5.2);
	margin: 0 auto;
	position: relative;
	transform-style: preserve-3d;
	filter:
		drop-shadow(0 32px 56px rgba(0, 0, 0, 0.42)) drop-shadow(0 12px 24px rgba(0, 0, 0, 0.28)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.12));
}

.wi-env-envelope::after {
	content: '';
	position: absolute;
	inset: 8% 6% 14%;
	border-radius: 2px;
	box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.06);
	pointer-events: none;
	z-index: 2;
}

/* Custom envelope image (full photo) */
.wi-env-photo-stack {
	display: none;
	position: absolute;
	inset: 0;
	border-radius: 4px;
	overflow: hidden;
}

.wi-env-envelope.has-env-image .wi-env-photo-stack {
	display: block;
}

.wi-env-envelope.has-env-image .wi-env-built {
	display: none;
}

.wi-env-envelope.has-env-image::after {
	display: none;
}

.wi-env-photo-body {
	position: absolute;
	inset: 0;
	background-color: var(--env-paper, #f5f0e6);
	overflow: hidden;
}

.wi-env-envelope.has-body-flap-split .wi-env-photo-body {
	clip-path: var(--env-body-clip, none);
	-webkit-clip-path: var(--env-body-clip, none);
}

.wi-env-photo-pocket-liner {
	display: none;
	position: absolute;
	left: 6%;
	right: 6%;
	top: 22%;
	height: 38%;
	background: linear-gradient(180deg, color-mix(in srgb, var(--wi-gold) 18%, transparent), transparent 70%);
	clip-path: polygon(0 0, 50% 100%, 100% 0);
	z-index: 3;
	opacity: 0.65;
	pointer-events: none;
}

.wi-env-envelope.has-body-flap-split.show-pocket-liner .wi-env-photo-pocket-liner {
	display: block;
}

.wi-env-photo-body-img {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	object-fit: var(--env-body-fit, contain);
	object-position: var(--env-body-position, center);
	display: block;
	pointer-events: none;
}

.wi-env-photo-flap {
	position: absolute;
	left: var(--env-flap-left, 0);
	top: var(--env-flap-top, -8px);
	width: var(--env-flap-width, 100%);
	height: var(--env-flap-height, 54%);
	overflow: hidden;
	clip-path: var(--env-flap-clip, polygon(0 0, 50% 100%, 100% 0));
	-webkit-clip-path: var(--env-flap-clip, polygon(0 0, 50% 100%, 100% 0));
	transform-origin: var(--env-flap-origin, 50% 0%);
	transform: rotateX(0deg) translateZ(2px);
	transition: transform 1.05s cubic-bezier(0.45, 0.05, 0.2, 1);
	z-index: 6;
	filter: brightness(1.02);
	transform-style: preserve-3d;
	backface-visibility: hidden;
}

.wi-env-envelope.has-separate-flap .wi-env-photo-flap {
	clip-path: none;
	-webkit-clip-path: none;
}

.wi-env-envelope.has-separate-flap .wi-env-photo-flap-img {
	min-height: 100%;
	height: 100%;
	object-fit: contain;
	object-position: center bottom;
}

.wi-env-photo-flap-img {
	width: 100%;
	height: auto;
	min-height: var(--env-flap-img-min-height, 185%);
	object-fit: var(--env-flap-img-fit, cover);
	object-position: var(--env-flap-img-position, center top);
	display: block;
	pointer-events: none;
}

.wi-env-photo-flap-shade {
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(255, 255, 255, 0.15), transparent 45%);
	clip-path: inherit;
	pointer-events: none;
}

.wi-envelope-gate.is-opening .wi-env-photo-flap,
.wi-envelope-gate.is-open .wi-env-photo-flap {
	transform: rotateX(var(--env-flap-open-rotate, -168deg));
	z-index: 1;
}

.wi-env-built {
	position: absolute;
	inset: 0;
}

.wi-env-paper-texture {
	position: absolute;
	inset: 0;
	border-radius: 4px;
	opacity: 0.35;
	background:
		repeating-linear-gradient(90deg,
			transparent,
			transparent 2px,
			color-mix(in srgb, var(--env-paper-dark) 8%, transparent) 2px,
			color-mix(in srgb, var(--env-paper-dark) 8%, transparent) 3px),
		repeating-linear-gradient(0deg,
			transparent,
			transparent 3px,
			color-mix(in srgb, var(--env-paper-dark) 5%, transparent) 3px,
			color-mix(in srgb, var(--env-paper-dark) 5%, transparent) 4px);
	pointer-events: none;
	z-index: 20;
	mix-blend-mode: multiply;
}

.wi-env-back {
	position: absolute;
	inset: 0;
	background:
		linear-gradient(165deg, var(--env-paper-light) 0%, var(--env-paper) 42%, var(--env-paper-mid) 72%, var(--env-paper-dark) 100%);
	border-radius: 5px;
	box-shadow:
		inset 0 1px 0 rgba(255, 255, 255, 0.5),
		inset 0 -8px 20px rgba(0, 0, 0, 0.04);
}

.wi-env-liner {
	position: absolute;
	left: 6%;
	right: 6%;
	top: 22%;
	height: 38%;
	background: linear-gradient(180deg, color-mix(in srgb, var(--wi-gold) 18%, transparent), transparent 70%);
	clip-path: polygon(0 0, 50% 100%, 100% 0);
	z-index: 2;
	opacity: 0.65;
	pointer-events: none;
}

.wi-env-pocket {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	height: 58%;
	background: linear-gradient(0deg, var(--env-paper-dark), var(--env-paper) 35%);
	clip-path: polygon(0 0, 50% 28%, 100% 0, 100% 100%, 0 100%);
	z-index: 3;
	box-shadow: inset 0 12px 24px rgba(0, 0, 0, 0.08);
}

.wi-env-side {
	position: absolute;
	top: 0;
	width: 50%;
	height: 55%;
	z-index: 4;
}

.wi-env-side.left {
	left: 0;
	background: linear-gradient(135deg, var(--env-paper-dark), var(--env-paper));
	clip-path: polygon(0 0, 100% 55%, 100% 100%, 0 100%);
	box-shadow: inset 2px 0 6px rgba(0, 0, 0, 0.06);
}

.wi-env-side.right {
	right: 0;
	background: linear-gradient(225deg, var(--env-paper-dark), var(--env-paper));
	clip-path: polygon(0 55%, 100% 0, 100% 100%, 0 100%);
	/* box-shadow: inset -2px 0 6px rgba(0, 0, 0, 0.06); */
}

.wi-env-bottom-flap {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	height: 46%;
	background: linear-gradient(0deg, var(--env-paper-dark) 0%, var(--env-paper-mid) 40%, var(--env-paper-light) 100%);
	clip-path: polygon(0 100%, 50% 6%, 100% 100%);
	z-index: 5;
	box-shadow: inset 0 6px 14px rgba(0, 0, 0, 0.06);
}

.wi-env-top-flap {
	position: absolute;
	left: 0;
	top: 0;
	width: 100%;
	height: 54%;
	background: linear-gradient(180deg, var(--env-paper-light), var(--env-paper) 55%, var(--env-paper-dark));
	clip-path: polygon(0 0, 50% 100%, 100% 0);
	transform-origin: 50% 0%;
	transform: rotateX(0deg) translateZ(1px);
	transition: transform 1.05s cubic-bezier(0.45, 0.05, 0.2, 1);
	z-index: 8;
	box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
	backface-visibility: hidden;
	transform-style: preserve-3d;
}

.wi-env-top-flap::after {
	content: '';
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(255, 255, 255, 0.2), transparent 40%);
	clip-path: inherit;
	pointer-events: none;
}

.wi-env-fold-line {
	position: absolute;
	left: 8%;
	right: 8%;
	top: 50%;
	height: 1px;
	background: color-mix(in srgb, var(--env-paper-dark) 35%, transparent);
	z-index: 7;
	opacity: 0.5;
}

.wi-env-inner-card {
	position: absolute;
	left: 10%;
	right: 10%;
	top: 18%;
	bottom: 22%;
	background: linear-gradient(180deg, #fffef9, #f8f4ec);
	border-radius: 2px;
	z-index: 1;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
	border: 1px solid color-mix(in srgb, var(--wi-gold) 25%, transparent);
}

.wi-envelope-gate.is-opening .wi-env-top-flap,
.wi-envelope-gate.is-open .wi-env-top-flap {
	transform: rotateX(-168deg);
	z-index: 2;
}

.wi-env-seal-wrap {
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -42%);
	z-index: 12;
	width: clamp(72px, 18vw, 96px);
	height: clamp(72px, 18vw, 96px);
	transition: opacity 0.45s ease, transform 0.45s ease;
}

.wi-env-seal {
	position: relative;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif;
	font-size: clamp(0.65rem, 2.8vw, 0.9rem);
	font-weight: 700;
	letter-spacing: 0.06em;
	line-height: 1.1;
	text-align: center;
	padding: 8px;
}

@include('invitation.templates.partials.builder-wedding-envelope-shape-styles') @include('invitation.templates.partials.builder-wedding-seal-styles') .wi-envelope-gate.is-opening .wi-env-seal-wrap,
.wi-envelope-gate.is-open .wi-env-seal-wrap {
	opacity: 0;
	transform: translate(-50%, -42%) scale(0.85);
}

.wi-env-footer {
	flex-shrink: 0;
	padding: 12px 20px max(20px, env(safe-area-inset-bottom));
	text-align: center;
	z-index: 2;
}

.wi-env-open-btn {
	display: inline-block;
	padding: 15px 40px;
	border: none;
	border-radius: 50px;
	background: linear-gradient(135deg, var(--wi-gold), color-mix(in srgb, var(--wi-gold) 65%, #4a3520));
	color: #fff;
	font-size: 1.05rem;
	font-weight: 600;
	cursor: pointer;
	box-shadow:
		0 10px 28px color-mix(in srgb, var(--wi-gold) 40%, transparent),
		0 2px 0 color-mix(in srgb, var(--wi-gold) 80%, #000);
	letter-spacing: 0.02em;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.wi-env-open-btn:hover {
	transform: translateY(-2px);
	box-shadow: 0 14px 32px color-mix(in srgb, var(--wi-gold) 45%, transparent);
}

.wi-env-open-btn:active {
	transform: translateY(0);
}

.wi-env-hint {
	margin-top: 10px;
	font-size: 0.8rem;
	opacity: 0.55;
	color: color-mix(in srgb, var(--wi-bg) 20%, #fff);
}

.wi-main-content {
	display: block;
	width: 100%;
}

.wi-main-content.is-gated {
	display: none;
}

@media (max-width: {{ $envMobileBreakpoint }}px) {
	.wi-env-scene.has-mobile-size-tune {
		width: var(--env-scene-width-sm, var(--env-scene-width, min(92vw, 440px)));
		min-height: var(--env-scene-min-height-sm, var(--env-scene-min-height, min(420px, calc(100dvh - 118px))));
	}

	.wi-env-envelope.has-mobile-size-tune {
		width: var(--env-width-sm, var(--env-width, 100%));
		height: var(--env-height-sm, var(--env-height, 100%));
		max-width: var(--env-max-width-sm, var(--env-max-width, min(92vw, 420px)));
		max-height: var(--env-max-height-sm, var(--env-max-height, min(90dvh, 520px)));
		aspect-ratio: var(--env-aspect-ratio-sm, var(--env-aspect-ratio, 4 / 5.2));
	}

	.wi-env-envelope.has-env-image.has-mobile-flap-tune .wi-env-photo-flap {
		top: var(--env-flap-top-sm, var(--env-flap-top, -8px));
		left: var(--env-flap-left-sm, var(--env-flap-left, 0));
		width: var(--env-flap-width-sm, var(--env-flap-width, 100%));
		height: var(--env-flap-height-sm, var(--env-flap-height, 54%));
		clip-path: var(--env-flap-clip-sm, var(--env-flap-clip, polygon(0 0, 50% 100%, 100% 0)));
		-webkit-clip-path: var(--env-flap-clip-sm, var(--env-flap-clip, polygon(0 0, 50% 100%, 100% 0)));
	}

	.wi-env-envelope.has-env-image.has-mobile-flap-tune .wi-env-photo-flap-img {
		object-position: var(--env-flap-img-position-sm, var(--env-flap-img-position, center top));
		min-height: var(--env-flap-img-min-height-sm, var(--env-flap-img-min-height, 185%));
	}

	.wi-env-envelope.has-env-image.has-mobile-flap-tune.has-body-flap-split .wi-env-photo-body,
	.wi-env-envelope.has-env-image.has-mobile-flap-tune.has-mobile-body-clip .wi-env-photo-body {
		clip-path: var(--env-body-clip-sm, var(--env-body-clip, none));
		-webkit-clip-path: var(--env-body-clip-sm, var(--env-body-clip, none));
	}
}

@media (max-height: 520px) {
	.wi-env-scene {
		/* height: min(calc(100dvh - 100px), 68dvh); */
		height: 100%;
	}

	.wi-env-footer {
		padding-top: 8px;
	}

	.wi-env-open-btn {
		padding: 12px 28px;
		font-size: 0.95rem;
	}
}
</style>

<div id="wiEnvelopeGate"
	class="wi-envelope-gate wi-env-shape-{{ $wiEnvelopeShape }}-gate @if($hasEnvImage) has-env-image-gate @endif"
	data-envelope-ref="{{ $envelopeBuilderConfig['envelope_image_ref'] ?? '' }}"
	data-envelope-shape="{{ $wiEnvelopeShape }}" @if($envStockSlug !=='' )
	data-envelope-stock="{{ $envStockSlug }}" @endif
	style="--wi-envelope: {{ $wiEnvelopeHex }}; --wi-gold: var(--ib-primary, #c8a97a); --wi-accent: var(--ib-secondary, #e8b4b8);">
	<div class="wi-env-stage">
		<div class="wi-env-scene @if($envHasMobileSizeTune) has-mobile-size-tune @endif"
			style="{{ $envSizeStyle }}">
			<div class="wi-env-envelope wi-env-shape-{{ $wiEnvelopeShape }} @if($hasEnvImage) has-env-image @endif @if($envHasBodyFlapSplit) has-body-flap-split @endif @if($envBodyClipSm !== '') has-mobile-body-clip @endif @if($envHasMobileFlapTune) has-mobile-flap-tune @endif @if($envHasMobileSizeTune) has-mobile-size-tune @endif @if($envHasSeparateFlap) has-separate-flap @endif @if($envShowPocketLiner) show-pocket-liner @endif @if($envStockSlug !== '') wi-env-stock-{{ $envStockSlug }} @endif"
				role="presentation"
				style="{{ $envEnvelopeStyle }}">
				@if($hasEnvImage)
				<div class="wi-env-photo-stack" aria-hidden="true">
					<div class="wi-env-photo-body">
						<img class="wi-env-photo-body-img"
							src="{{ $envBodyImageUrl }}" alt=""
							loading="eager" decoding="async">
					</div>
					{{-- @if($envShowPocketLiner && $envHasBodyFlapSplit)
					<div class="wi-env-photo-pocket-liner" aria-hidden="true"></div>
					@endif --}}
					<div class="wi-env-photo-flap">
						<img class="wi-env-photo-flap-img"
							src="{{ $envFlapImageUrl }}" alt=""
							loading="eager" decoding="async">
						<div class="wi-env-photo-flap-shade"></div>
					</div>
				</div>
				@endif
				<div class="wi-env-built">
					<div class="wi-env-back"></div>
					<div class="wi-env-liner" aria-hidden="true"></div>
					<div class="wi-env-inner-card"></div>
					<div class="wi-env-pocket"></div>
					<div class="wi-env-side left"></div>
					<div class="wi-env-side right"></div>
					<div class="wi-env-bottom-flap"></div>
					<div class="wi-env-fold-line"></div>
					<div class="wi-env-top-flap"></div>
					<div class="wi-env-paper-texture"></div>
				</div>
				<div class="wi-env-seal-wrap">
					<div class="wi-env-seal has-seal-custom-color wi-seal-shape-{{ $wiSealShape }} wi-seal-pal-{{ $wiSealPalette }} @if($wiSealRing) has-seal-ring @endif @if($wiSealDrip) has-seal-drip @endif"@if($wiSealStyleAttr !== '') style="{{ $wiSealStyleAttr }}"@endif>
						<span class="wi-seal-ring" aria-hidden="true"></span>
						<button type="button" class="wi-env-seal-button"
							onclick="wiOpenEnvelope()"
							aria-label="افتح الدعوة">
							<span
								class="wi-seal-initials">{{ $wiInitials ?: '♥' }}</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- <div class="wi-env-footer">
		<button type="button" class="wi-env-open-btn" onclick="wiOpenEnvelope()">افتح الدعوة</button>
		<p class="wi-env-hint">اضغط لفتح الظرف</p>
	</div> -->
</div>
<script>
function wiOpenEnvelope() {
	var gate = document.getElementById('wiEnvelopeGate');
	var main = document.getElementById('wiMainContent');
	if (!gate) return;
	gate.classList.add('is-opening');
	if (window.wiPlayOpeningAudio) {
		window.wiPlayOpeningAudio();
	}
	setTimeout(function() {
		gate.classList.add('is-open');
		if (main) {
			main.classList.remove('is-gated');
			main.style.display = 'block';
		}
		if (window.wiEnsureHeroVideosPlay) {
			window.wiEnsureHeroVideosPlay();
		}
	}, 900);
}
</script>
@endif
