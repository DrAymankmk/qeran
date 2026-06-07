@php
    use App\Services\Invitation\WeddingInvitationPresenter;

    $bc = $builderConfig ?? [];
    $blocks = $bc['blocks'] ?? ['countdown', 'event_details', 'venue', 'rsvp'];
    $has = fn (string $b) => WeddingInvitationPresenter::hasBlock($blocks, $b);

    $present = WeddingInvitationPresenter::from(
        $invitation,
        $bc,
        $host_name ?? null,
        $category ?? null
    );
    extract($present, EXTR_SKIP);

    $bodyPath = resource_path('views/invitation/templates/partials/builder-wedding-body.html');
    $bodyHtml = file_exists($bodyPath) ? file_get_contents($bodyPath) : '';

    $sectionMarkers = [
        '<!-- ② Countdown -->' => 'countdown',
        '<!-- ③ Our Story -->' => 'our_story',
        '<!-- ④ Event Details' => 'event_details',
        '<!-- ⑤ Photo Gallery -->' => 'gallery',
        '<!-- ⑥ Schedule -->' => 'timeline',
        '<!-- ⑦ Venue' => 'venue',
        '<!-- ⑧ Accommodation -->' => 'accommodation',
        '<!-- ⑨ Dress Code -->' => 'dress_code',
        '<!-- ⑩ Gift Registry -->' => 'gift_list',
        '<!-- ⑪ RSVP -->' => 'rsvp',
        '<!-- ⑫ Guestbook -->' => 'wishes',
        '<!-- ⑬ Music Wishlist -->' => 'wishes',
    ];

    $viewData = array_merge($present, [
        'invitation' => $invitation,
        'builderConfig' => $bc,
        'category' => $category ?? null,
        'host_name' => $host_name ?? null,
        'routes' => $routes ?? ['accept' => '#', 'decline' => '#'],
        'initialView' => $initialView ?? 'envelope',
    ]);

    $heroHtml = view('invitation.templates.partials.builder-wedding-section-hero', $viewData)->render();
    $bodyHtml = WeddingInvitationPresenter::replaceBetweenMarkers(
        $bodyHtml,
        '<!-- ① Hero -->',
        '<!-- ② Countdown -->',
        $heroHtml
    );

    $detailsHtml = view('invitation.templates.partials.builder-wedding-section-details', $viewData)->render();
    $bodyHtml = WeddingInvitationPresenter::replaceBetweenMarkers(
        $bodyHtml,
        '<!-- ④ Event Details',
        '<!-- ⑤ Photo Gallery -->',
        $detailsHtml
    );

    $venueHtml = view('invitation.templates.partials.builder-wedding-section-venue', $viewData)->render();
    $bodyHtml = WeddingInvitationPresenter::replaceBetweenMarkers(
        $bodyHtml,
        '<!-- ⑦ Venue',
        '<!-- ⑧ Accommodation -->',
        $venueHtml
    );

    foreach ($sectionMarkers as $marker => $blockKey) {
        if ($has($blockKey)) {
            continue;
        }
        $pos = strpos($bodyHtml, $marker);
        if ($pos === false) {
            continue;
        }
        $nextPos = strlen($bodyHtml);
        foreach ($sectionMarkers as $m => $_) {
            if ($m === $marker) {
                continue;
            }
            $p = strpos($bodyHtml, $m, $pos + 1);
            if ($p !== false && $p < $nextPos) {
                $nextPos = $p;
            }
        }
        $footerPos = strpos($bodyHtml, '<!-- ⑭ Thank You Footer -->', $pos);
        if ($footerPos !== false && $footerPos < $nextPos) {
            $nextPos = $footerPos;
        }
        $bodyHtml = substr($bodyHtml, 0, $pos).substr($bodyHtml, $nextPos);
    }

    $bodyHtml = str_replace(
        ['Counting down the days', "new Date('2025-09-14T16:30:00')", 'onclick="submitRsvp()"'],
        ['العد التنازلي', "new Date('".$wiCountdownIso."')", 'type="button" onclick="wiSubmitRsvpAccept()"'],
        $bodyHtml
    );

    $bodyHtml = str_replace(
        ['Joyfully accepts', 'Regretfully declines', 'Send my RSVP'],
        ['أوافق بحب', 'أعتذر', 'تأكيد الحضور'],
        $bodyHtml
    );

    if ($wiNamesFooter) {
        $bodyHtml = preg_replace('/<p class="wi-footer-names">.*?<\/p>/s', '<p class="wi-footer-names">'.e($wiNamesFooter).'</p>', $bodyHtml, 1);
    }

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
.wi-root { background: var(--wi-bg) !important; color: var(--wi-text) !important; }
.wi-names, .wi-section-title, .wi-detail-main, .wi-footer-names { font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif; }
.wi-countdown-bar { background: color-mix(in srgb, var(--wi-text) 92%, #000) !important; }
.wi-count-num { color: var(--wi-gold) !important; }
.wi-corner { color: var(--wi-gold); }
.wi-detail-icon { color: var(--wi-gold); }
.wi-divider-diamond, .wi-divider::before, .wi-divider::after { background: var(--wi-gold); }
.wi-section-label, .wi-date-badge, .wi-subtitle, .wi-detail-heading { color: color-mix(in srgb, var(--wi-gold) 85%, var(--wi-text)); }
.wi-rsvp-submit { background: var(--wi-gold) !important; }
.wi-hero { overflow: hidden; }
.wi-hero-has-video::before { display: none; }
.wi-hero-media {
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  overflow: hidden;
}
.wi-hero-image {
	width: 100%;
	height: 100%;
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
  background: linear-gradient(
    180deg,
    color-mix(in srgb, var(--wi-bg, #1a1520) 35%, transparent) 0%,
    color-mix(in srgb, var(--wi-bg, #1a1520) 55%, transparent) 45%,
    color-mix(in srgb, var(--wi-bg, #1a1520) 75%, transparent) 100%
  );
}
.wi-hero-has-video > :not(.wi-hero-media) {
  position: relative;
  z-index: 1;
}
.wi-hero-has-video .wi-corner { opacity: 0.45; color: var(--wi-gold); }
.wi-hero-has-video .wi-date-badge,
.wi-hero-has-video .wi-subtitle,
.wi-hero-has-video .wi-parents,
.wi-hero-has-video .wi-hero-detail {
  color: color-mix(in srgb, var(--wi-text) 92%, #fff);
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
.wi-hero-has-video .wi-couple-stack .wi-parent-line {
  color: color-mix(in srgb, var(--wi-text) 92%, #fff);
}
.wi-hero-has-video .wi-names { color: var(--wi-text); text-shadow: 0 2px 24px rgba(0, 0, 0, 0.35); }
.wi-hero-has-video .wi-ampersand { color: var(--wi-gold); }
.wi-hero-has-video .wi-scroll-hint { color: color-mix(in srgb, var(--wi-text) 80%, #fff); }
.wi-hero.wi-date-pos-top .wi-hero-detail { order: -1; margin-bottom: 20px; }
.wi-hero.wi-date-pos-bottom .wi-hero-detail { margin-top: 12px; }
.wi-detail-card { border-color: color-mix(in srgb, var(--ib-block-accent, var(--wi-gold)) 35%, transparent); }
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
.wi-map-placeholder { color: var(--wi-gold); }
.wi-map-label .wi-map-address { font-size: 13px; font-weight: 400; opacity: 0.85; }
.wi-venue-address-line {
  text-align: center;
  margin-top: 16px;
  line-height: 1.6;
  opacity: 0.9;
}
.wi-venue-actions { margin-top: 24px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
a.wi-venue-btn { text-decoration: none; display: inline-block; }
.wi-builder-status-overlay {
  position: fixed; inset: 0; z-index: 10001; display: none;
  align-items: center; justify-content: center; background: rgba(44,36,22,0.92);
  padding: 24px; text-align: center; color: #faf7f2;
}
.wi-builder-status-overlay.active { display: flex; }
</style>

@if(!empty($bc['music_enabled']))
@php $audioUrls = $invitation->getAudioUrls(); @endphp
@if(!empty($audioUrls['mp3']) || !empty($audioUrls['ogg']))
<audio id="inviteOpeningAudio" preload="auto" style="display:none;">
  @if(!empty($audioUrls['ogg']))<source src="{{ $audioUrls['ogg'] }}" type="audio/ogg">@endif
  @if(!empty($audioUrls['mp3']))<source src="{{ $audioUrls['mp3'] }}" type="audio/mpeg">@endif
</audio>
@endif
@endif

@include('invitation.templates.partials.builder-wedding-envelope', $viewData)

<div id="wiMainContent" class="wi-main-content @if($gateMain) is-gated @endif" data-wi-countdown="{{ $wiCountdownIso }}">
{!! $bodyHtml !!}
</div>

<div id="wiStatusAccepted" class="wi-builder-status-overlay @if(($initialView ?? '') === 'success') active @endif">
  <div>
    <div style="font-size:48px;margin-bottom:16px;">✓</div>
    <h2 style="font-family:var(--ib-headline-font,serif);font-size:2rem;margin-bottom:12px;">تم قبول الدعوة</h2>
    <p style="opacity:0.85;">شكراً لك — نتطلع لرؤيتك</p>
  </div>
</div>
<div id="wiStatusDeclined" class="wi-builder-status-overlay @if(($initialView ?? '') === 'decline') active @endif">
  <div>
    <div style="font-size:48px;margin-bottom:16px;">✗</div>
    <h2 style="font-family:var(--ib-headline-font,serif);font-size:2rem;margin-bottom:12px;">تم رفض الدعوة</h2>
    <p style="opacity:0.85;">نأسف لعدم تمكنك من الحضور</p>
  </div>
</div>

@include('invitation.templates.partials.builder-wedding-scripts', $viewData)
