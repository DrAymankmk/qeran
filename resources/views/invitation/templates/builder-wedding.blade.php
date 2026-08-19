@php
use App\Services\Invitation\WeddingInvitationPresenter;

$bc = $builderConfig ?? [];
$blocks = $bc['blocks'] ?? ['countdown', 'event_details', 'venue', 'rsvp'];

$present = WeddingInvitationPresenter::from(
$invitation,
$bc,
$host_name ?? null,
$category ?? null
);
extract($present, EXTR_SKIP);

$bodyPath = resource_path('views/invitation/templates/partials/builder-wedding-body.html');
$bodyHtml = file_exists($bodyPath) ? file_get_contents($bodyPath) : '';

$viewData = array_merge($present, [
'invitation' => $invitation,
'builderConfig' => $bc,
'category' => $category ?? null,
'host_name' => $host_name ?? null,
'user' => $user ?? null,
'contactLog' => $contactLog ?? null,
'guestQrCards' => $guestQrCards ?? [],
'routes' => $routes ?? ['accept' => '#', 'decline' => '#'],
'initialView' => $initialView ?? 'envelope',
'isBuilderPreview' => ! empty($isBuilderPreview),
'previewQrUrl' => $previewQrUrl ?? null,
]);

$heroHtml = ! empty($present['wiHeroEnabled'])
    ? view('invitation.templates.partials.builder-wedding-section-hero', $viewData)->render()
    : '';
$bodyHtml = WeddingInvitationPresenter::replaceBetweenMarkers(
    $bodyHtml,
    '<!-- ① Hero -->',
    '<!-- ② Countdown -->',
    $heroHtml
);

$sectionsHtml = WeddingInvitationPresenter::composeOrderedBlockSections($blocks, $viewData);
$bodyHtml = WeddingInvitationPresenter::replaceBetweenMarkers(
    $bodyHtml,
    '<!-- ② Countdown -->',
    '<!-- /⑭ Thank You Footer -->',
    $sectionsHtml
);

$gateMain = $showEnvelope && ! in_array($initialView ?? '', ['success', 'decline'], true);
if (! empty($isBuilderPreview)) {
$gateMain = false;
}
@endphp

<style>
@include('invitation.templates.partials.builder-wedding-styles')
:root {
	--wi-gold: var(--ib-primary, #c8a97a);
	--wi-accent: var(--ib-secondary, #e8b4b8);
	--wi-bg: var(--ib-bg, #faf7f2);
	--wi-text: var(--ib-text, #2c2416);
}

.wi-root {
	background: var(--wi-bg) !important;
	color: var(--wi-text) !important;
}

.wi-names,
.wi-section-title,
.wi-detail-main,
.wi-footer-names {
	font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif;
}

.wi-block-custom .wi-section-title,
.wi-block-custom .wi-detail-main,
.wi-block-custom .wi-sch-title,
.wi-block-custom .wi-count-num,
.wi-block-custom .wi-footer-names {
	font-family: var(--wi-block-headline-font, var(--ib-headline-font, 'Cormorant Garamond')), serif;
	font-size: var(--wi-block-title-size, inherit);
	font-weight: var(--wi-block-title-weight, inherit);
	color: var(--wi-block-title-color) !important;
}

.wi-block-custom .wi-section-label,
.wi-block-custom .wi-countdown-label,
.wi-block-custom .wi-detail-heading {
	font-size: var(--wi-block-label-size, inherit);
	font-weight: var(--wi-block-label-weight, inherit);
	color: var(--wi-block-label-color) !important;
}

.wi-block-custom .wi-section-body,
.wi-block-custom .wi-detail-sub,
.wi-block-custom .wi-count-unit,
.wi-block-custom .wi-sch-place,
.wi-block-custom .wi-footer-msg,
.wi-block-custom .wi-footer-date {
	font-family: inherit;
	font-size: var(--wi-block-body-size, inherit);
	font-weight: var(--wi-block-body-weight, inherit);
	color: var(--wi-block-body-color) !important;
}

.wi-countdown-bar:not(.wi-block-custom) {
	background: color-mix(in srgb, var(--wi-text) 92%, #000) !important;
}

.wi-block-custom {
	background: var(--wi-block-bg) !important;
	background-color: var(--wi-block-bg) !important;
}

.wi-count-num {
	color: var(--wi-gold) !important;
}

.wi-corner {
	color: var(--wi-gold);
}

.wi-detail-icon {
	color: var(--wi-gold);
}

.wi-divider-diamond,
.wi-divider::before,
.wi-divider::after {
	background: var(--wi-hero-divider-color, var(--wi-gold));
}

.wi-section-label,
.wi-date-badge,
.wi-subtitle,
.wi-detail-heading {
	color: color-mix(in srgb, var(--wi-gold) 85%, var(--wi-text));
}

.wi-rsvp-submit {
	background: var(--wi-gold) !important;
}

.wi-hero {
	overflow: hidden;
	isolation: isolate;
	position: relative;
}

.wi-hero-has-video::before,
.wi-hero-has-image::before {
	display: none;
}

.wi-hero-has-image {
	background-image: var(--wi-hero-image);
	background-size: cover;
	background-position: center center;
	background-repeat: no-repeat;
	/* background-color: transparent; */
}

.wi-hero-media {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	min-height: 100%;
	z-index: 0;
	pointer-events: none;
	overflow: hidden;
}

.wi-hero-image {
	position: absolute;
	top: 50%;
	left: 50%;
	min-width: 100%;
	min-height: 100%;
	width: 100%;
	height: 100%;
	transform: translate(-50%, -50%);
	object-fit: cover;
	object-position: center center;
	display: block;
}

.wi-hero-video {
	position: absolute;
	top: 50%;
	left: 50%;
	min-width: 100%;
	min-height: 100%;
	width: auto;
	height: auto;
	transform: translate(-50%, -50%);
	object-fit: cover;
	object-position: center center;
	display: block;
}

.wi-hero-video-overlay {
	position: absolute;
	inset: 0;
	/* background: linear-gradient(180deg,
			color-mix(in srgb, var(--wi-bg, #1a1520) 35%, transparent) 0%,
			color-mix(in srgb, var(--wi-bg, #1a1520) 55%, transparent) 45%,
			color-mix(in srgb, var(--wi-bg, #1a1520) 75%, transparent) 100%); */
}

.wi-hero-has-video> :not(.wi-hero-media),
.wi-hero-has-image> :not(.wi-hero-media) {
	position: relative;
	z-index: 1;
}

.wi-hero-has-video .wi-corner,
.wi-hero-has-image .wi-corner {
	opacity: 0.45;
	color: var(--wi-gold);
}

.wi-hero-has-video .wi-date-badge,
.wi-hero-has-video .wi-subtitle,
.wi-hero-has-video .wi-parents,
.wi-hero-has-video .wi-hero-detail,
.wi-hero-has-video .wi-honorific,
.wi-hero-has-image .wi-date-badge,
.wi-hero-has-image .wi-subtitle,
.wi-hero-has-image .wi-parents,
.wi-hero-has-image .wi-hero-detail,
.wi-hero-has-image .wi-honorific {
	color: color-mix(in srgb, var(--wi-text) 92%, #fff);
}

.wi-honorific {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.85rem;
	width: min(92%, 520px);
	margin: 0 auto 1.6rem;
	text-align: center;
}

.wi-honorific-intro {
	margin: 0;
	font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif;
	font-size: clamp(1.15rem, 3.2vw, 1.55rem);
	font-weight: 400;
	letter-spacing: 0.12em;
	color: color-mix(in srgb, var(--wi-gold) 85%, var(--wi-text));
}

.wi-honorific-parties {
	display: grid;
	grid-template-columns: 1fr auto 1fr;
	align-items: center;
	gap: 0.75rem 1.1rem;
	width: 100%;
}

.wi-honorific-parties-one {
	grid-template-columns: 1fr;
}

.wi-honorific-party {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.2rem;
	min-width: 0;
}

.wi-honorific-title {
	display: block;
	font-family: var(--ib-font, 'Cairo'), sans-serif;
	font-size: clamp(0.78rem, 2vw, 0.95rem);
	font-weight: 400;
	letter-spacing: 0.08em;
	opacity: 0.88;
}

.wi-honorific-name {
	display: block;
	font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif;
	font-size: clamp(1.35rem, 4.2vw, 2.1rem);
	font-weight: 500;
	line-height: 1.2;
	color: var(--wi-text);
}

.wi-honorific-sep {
	width: 1px;
	height: 2.6rem;
	background: color-mix(in srgb, var(--wi-gold) 70%, transparent);
	align-self: center;
}

.wi-honorific-footer {
	margin: 0.15rem 0 0;
	font-family: var(--ib-font, 'Cairo'), sans-serif;
	font-size: clamp(0.82rem, 2.2vw, 1rem);
	font-weight: 400;
	line-height: 1.7;
	white-space: pre-line;
	color: color-mix(in srgb, var(--wi-gold) 70%, var(--wi-text));
}

@media (max-width: 520px) {
	.wi-honorific-parties {
		gap: 0.55rem 0.7rem;
	}

	.wi-honorific-sep {
		height: 2.1rem;
	}
}

.wi-couple-stack {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.35rem;
}

.wi-couple-block {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.2rem;
}

.wi-couple-block.wi-couple-inline {
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: baseline;
	gap: 0.25em 0.55em;
}

.wi-couple-block.wi-couple-inline .wi-couple-name,
.wi-couple-block.wi-couple-inline .wi-parent-line {
	display: inline;
}

.wi-couple-name {
	display: block;
	line-height: 1.05;
}

.wi-couple-stack .wi-ampersand {
	margin: 0.15rem 0 0.25rem;
}

.wi-couple-stack .wi-parent-line {
	display: block;
	margin: 0;
	font-family: var(--ib-font, 'Cairo'), sans-serif;
	font-size: clamp(0.85rem, 2.2vw, 1rem);
	font-weight: 400;
	letter-spacing: 0.02em;
	opacity: 0.88;
	line-height: 1.5;
}

.wi-hero-has-video .wi-couple-stack .wi-parent-line,
.wi-hero-has-image .wi-couple-stack .wi-parent-line {
	color: color-mix(in srgb, var(--wi-text) 92%, #fff);
}

.wi-hero-has-video .wi-names,
.wi-hero-has-image .wi-names {
	color: var(--wi-text);
	text-shadow: 0 2px 24px rgba(0, 0, 0, 0.35);
}

.wi-hero-has-video .wi-ampersand,
.wi-hero-has-image .wi-ampersand {
	color: var(--wi-gold);
}

.wi-hero-has-video .wi-scroll-hint,
.wi-hero-has-image .wi-scroll-hint {
	color: color-mix(in srgb, var(--wi-text) 80%, #fff);
}

.wi-hero.wi-date-pos-top .wi-hero-detail {
	order: -1;
	margin-bottom: 20px;
}

.wi-hero.wi-date-pos-bottom .wi-hero-detail {
	margin-top: 12px;
}

.wi-media-section {
	padding: 0;
	min-height: 100vh;
}

.wi-media-section.wi-hero-has-video::before,
.wi-media-section.wi-hero-has-image::before {
	display: none;
}

.wi-detail-card {
	border-color: color-mix(in srgb, var(--ib-block-accent, var(--wi-gold)) 35%, transparent);
}

.wi-map-embed {
	margin: 24px 0;
	border-radius: 8px;
	overflow: hidden;
	border: 1px solid color-mix(in srgb, var(--wi-gold) 35%, transparent);
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.wi-map-embed iframe {
	display: block;
	width: 100%;
	height: min(240px, 42vw);
	min-height: 200px;
	border: 0;
}

.wi-map-placeholder {
	color: var(--wi-gold);
}

.wi-map-label .wi-map-address {
	font-size: 13px;
	font-weight: 400;
	opacity: 0.85;
}

.wi-venue-address-line {
	text-align: center;
	margin-top: 16px;
	line-height: 1.6;
	opacity: 0.9;
}

.wi-venue-actions {
	margin-top: 24px;
	display: flex;
	gap: 12px;
	justify-content: center;
	flex-wrap: wrap;
}

a.wi-venue-btn {
	text-decoration: none;
	display: inline-block;
}

.wi-builder-status-overlay {
	position: fixed;
	inset: 0;
	z-index: 10001;
	display: none;
	align-items: center;
	justify-content: center;
	background: rgba(44, 36, 22, 0.92);
	padding: 24px;
	text-align: center;
	color: #faf7f2;
}

.wi-builder-status-overlay.active {
	display: flex;
}
</style>

@php
$wiMusicUrl = WeddingInvitationPresenter::backgroundMusicUrl($bc, $invitation);
$wiMusicVolume = WeddingInvitationPresenter::backgroundMusicVolume($bc);
$wiMusicLoop = WeddingInvitationPresenter::backgroundMusicLoop($bc);
@endphp
@if($wiMusicUrl !== '')
<audio id="inviteOpeningAudio" preload="auto" data-volume="{{ $wiMusicVolume }}" @if($wiMusicLoop) loop @endif
	style="display:none;">
	<source src="{{ $wiMusicUrl }}" type="{{ WeddingInvitationPresenter::backgroundMusicMime($bc) }}">
</audio>
@endif

@include('invitation.templates.partials.builder-wedding-envelope', $viewData)

<div id="wiMainContent" class="wi-main-content @if($gateMain) is-gated @endif"
	data-wi-countdown="{{ $wiCountdownIso }}">
	{!! $bodyHtml !!}
</div>

<div id="wiStatusAccepted" class="wi-builder-status-overlay"></div>
<div id="wiStatusDeclined" class="wi-builder-status-overlay"></div>

@include('invitation.templates.partials.builder-wedding-scripts', $viewData)