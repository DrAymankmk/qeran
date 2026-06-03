@php
    $bc = $builderConfig ?? [];
    $blocks = $bc['blocks'] ?? ['countdown', 'venue', 'rsvp'];
    $has = fn (string $b) => in_array($b, $blocks, true);

    $wiName1 = $invitation->bride ?: ($invitation->groom ?: strtok($bc['opening_headline'] ?? $invitation->event_name, '&'));
    $wiName2 = ($invitation->bride && $invitation->groom)
        ? $invitation->groom
        : ($invitation->host_name ?: '');

    if ($wiName2 === '' && str_contains($wiName1, '&')) {
        $parts = array_map('trim', explode('&', $wiName1));
        $wiName1 = $parts[0] ?? $wiName1;
        $wiName2 = $parts[1] ?? '';
    }

    $eventDate = $bc['event_date'] ?? $invitation->date;
    $eventTime = $bc['event_time'] ?? $invitation->time;
    $dateBadge = $eventDate
        ? \Carbon\Carbon::parse($eventDate)->locale(app()->getLocale())->translatedFormat('j F Y')
        : '';
    $dateHero = $eventDate
        ? \Carbon\Carbon::parse($eventDate)->locale(app()->getLocale())->translatedFormat('j F Y')
        : '';
    $timeHero = $eventTime
        ? \Carbon\Carbon::parse($eventTime)->format('h:i A')
        : '';
    $venueLine = $invitation->address ?: ($invitation->event_name ?? '');
    $headline = $bc['opening_headline'] ?? $invitation->event_name;
    $hostLabel = $host_name ?? $invitation->host_name ?? '';

    $countdownIso = '2026-12-31T12:00:00';
    if ($eventDate) {
        try {
            $countdownIso = \Carbon\Carbon::parse(trim($eventDate.' '.($eventTime ?: '12:00')))->toIso8601String();
        } catch (\Throwable $e) {
            $countdownIso = \Carbon\Carbon::parse($eventDate)->endOfDay()->toIso8601String();
        }
    }

    $mapUrl = ($invitation->latitude && $invitation->longitude)
        ? 'https://www.google.com/maps?q='.$invitation->latitude.','.$invitation->longitude
        : ($invitation->address ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($invitation->address) : '#');

    $bodyPath = resource_path('views/invitation/templates/partials/builder-wedding-body.html');
    $bodyHtml = file_exists($bodyPath) ? file_get_contents($bodyPath) : '';

    $sectionMarkers = [
        '<!-- ② Countdown -->' => 'countdown',
        '<!-- ③ Our Story -->' => 'our_story',
        '<!-- ④ Event Details -->' => 'venue',
        '<!-- ⑤ Photo Gallery -->' => 'gallery',
        '<!-- ⑥ Schedule -->' => 'timeline',
        '<!-- ⑦ Venue -->' => 'venue',
        '<!-- ⑧ Accommodation -->' => 'accommodation',
        '<!-- ⑨ Dress Code -->' => 'dress_code',
        '<!-- ⑩ Gift Registry -->' => 'gift_list',
        '<!-- ⑪ RSVP -->' => 'rsvp',
        '<!-- ⑫ Guestbook -->' => 'wishes',
        '<!-- ⑬ Music Wishlist -->' => 'wishes',
    ];

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

    $replacements = [
        'Together at last · September 14, 2025' => e($hostLabel ? $hostLabel.' · '.$dateBadge : $dateBadge),
        "Layla<br>\n      <span class=\"wi-ampersand\">&</span><br>\n      Adam" => e($wiName1)."<br><span class=\"wi-ampersand\">&</span><br>".e($wiName2 ?: $wiName1),
        'are getting married' => e($category?->getTranslation('ar')?->name ?? 'نتشرف بدعوتكم لحضور حفل الزفاف'),
        "14 September 2025<br>\n      Château des Roses · Tuscany, Italy" => e($dateHero.($timeHero ? ' · '.$timeHero : '')).'<br>'.e($venueLine),
        'Layla & Adam' => e(trim($wiName1.' & '.$wiName2, ' &')),
        "14 · 09 · 2025 · Tuscany, Italy" => e($dateBadge.($venueLine ? ' · '.$venueLine : '')),
        "new Date('2025-09-14T16:30:00')" => "new Date('".$countdownIso."')",
        'onclick="submitRsvp()"' => 'type="button" onclick="wiSubmitRsvpAccept()"',
        'Joyfully accepts' => 'أوافق بحب',
        'Regretfully declines' => 'أعتذر',
        'Send my RSVP' => 'تأكيد الحضور',
        'Counting down the days' => 'العد التنازلي',
        'scroll' => 'مرر',
    ];

    $bodyHtml = str_replace(array_keys($replacements), array_values($replacements), $bodyHtml);
@endphp

<style>
@include('invitation.templates.partials.builder-wedding-styles')
/* Builder theme overrides */
:root {
  --wi-gold: var(--ib-primary, #c8a97a);
  --wi-accent: var(--ib-secondary, #e8b4b8);
  --wi-bg: var(--ib-bg, #faf7f2);
  --wi-text: var(--ib-text, #2c2416);
}
.wi-root { background: var(--wi-bg) !important; color: var(--wi-text) !important; }
.wi-names, .wi-section-title, .wi-detail-main, .wi-footer-names { font-family: var(--ib-headline-font, 'Cormorant Garamond'), serif; }
.wi-countdown-bar { background: color-mix(in srgb, var(--wi-text) 92%, #000) !important; }
.wi-count-num, .wi-divider-diamond, .wi-divider::before, .wi-divider::after { color: var(--wi-gold); }
.wi-count-num { color: var(--wi-gold) !important; }
.wi-corner path, .wi-corner circle { stroke: var(--wi-gold); fill: var(--wi-gold); }
.wi-rsvp-submit { background: var(--wi-gold) !important; }
.wi-section-label, .wi-date-badge, .wi-subtitle { color: color-mix(in srgb, var(--wi-gold) 85%, var(--wi-text)); }
@if(!empty($bc['block_floral_border']))
.ib-block-card, .wi-detail-card, .wi-hotel-card { border-color: color-mix(in srgb, var(--ib-block-accent, var(--wi-gold)) 50%, transparent) !important; }
@endif
.wi-builder-status-overlay {
  position: fixed; inset: 0; z-index: 10001; display: none;
  align-items: center; justify-content: center; background: rgba(44,36,22,0.92);
  padding: 24px; text-align: center; color: #faf7f2;
}
.wi-builder-status-overlay.active { display: flex; }
</style>

{!! $bodyHtml !!}

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

<script>
function wiSubmitRsvpAccept() {
  if (typeof acceptInvitation === 'function') {
    acceptInvitation();
    return;
  }
  var btn = document.querySelector('.wi-rsvp-submit');
  if (btn) {
    btn.textContent = 'جاري الإرسال…';
    btn.disabled = true;
  }
  fetch(@json($routes['accept'] ?? ''), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        document.getElementById('wiStatusAccepted')?.classList.add('active');
      } else {
        alert(data.message || 'حدث خطأ');
        if (btn) { btn.disabled = false; btn.textContent = 'تأكيد الحضور'; }
      }
    })
    .catch(function () {
      alert('حدث خطأ');
      if (btn) { btn.disabled = false; btn.textContent = 'تأكيد الحضور'; }
    });
}

function setRsvp(val, el) {
  document.querySelectorAll('.wi-rsvp-opt').forEach(function (o) { o.classList.remove('active'); });
  if (el) el.classList.add('active');
  var meal = document.getElementById('mealField');
  if (meal) meal.style.display = val === 'yes' ? '' : 'none';
  if (val === 'no') {
    if (typeof declineInvitation === 'function') {
      declineInvitation();
    } else {
      fetch(@json($routes['decline'] ?? ''), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) document.getElementById('wiStatusDeclined')?.classList.add('active');
        });
    }
  }
}

function countdown() {
  var target = new Date(@json($countdownIso));
  var now = new Date();
  var diff = target - now;
  var els = ['cd-days','cd-hours','cd-mins','cd-secs'];
  if (diff <= 0) {
    els.forEach(function (id) { var el = document.getElementById(id); if (el) el.textContent = '00'; });
    return;
  }
  var d = Math.floor(diff / 86400000);
  var h = Math.floor((diff % 86400000) / 3600000);
  var m = Math.floor((diff % 3600000) / 60000);
  var s = Math.floor((diff % 60000) / 1000);
  var map = { 'cd-days': d, 'cd-hours': h, 'cd-mins': m, 'cd-secs': s };
  Object.keys(map).forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.textContent = String(map[id]).padStart(2, '0');
  });
}
countdown();
setInterval(countdown, 1000);
</script>
