@php
    $audioUrls = $invitation->getAudioUrls();
    $hasAudio = ! empty($audioUrls['mp3']) || ! empty($audioUrls['ogg']);

    $initials = '';
    if (! empty($builderConfig['envelope_initials'] ?? null)) {
        $initials = $builderConfig['envelope_initials'];
    } elseif (! empty($invitation->groom) && ! empty($invitation->bride)) {
        $initials = mb_substr(trim($invitation->groom), 0, 1).' & '.mb_substr(trim($invitation->bride), 0, 1);
    } elseif (! empty($host_name)) {
        $parts = preg_split('/\s+/u', trim($host_name), -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) >= 2) {
            $initials = mb_substr($parts[0], 0, 1).' & '.mb_substr($parts[1], 0, 1);
        } elseif (count($parts) === 1) {
            $initials = mb_substr($parts[0], 0, 2);
        }
    }
    if ($initials === '' && ! empty($invitation->event_name)) {
        $initials = mb_substr(trim($invitation->event_name), 0, 2);
    }

    $eventDate = $invitation->date
        ? \Carbon\Carbon::parse($invitation->date)->locale('ar')->translatedFormat('l، j F Y')
        : null;
    $eventTime = $invitation->time
        ? \Carbon\Carbon::parse($invitation->time)->format('h:i A')
        : null;
@endphp

<style>
/* Template 16 — Premium digital envelope (Wooow-style) */
.template16-ambient {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
    background: radial-gradient(ellipse at 30% 20%, rgba(255, 214, 192, 0.25) 0%, transparent 50%),
        radial-gradient(ellipse at 70% 80%, rgba(196, 164, 132, 0.2) 0%, transparent 45%),
        linear-gradient(160deg, #1a1520 0%, #2d2435 40%, #3d2f3a 100%);
}

.template16-particle {
    position: absolute;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 220, 180, 0.6);
    animation: t16-float 8s ease-in-out infinite;
}

@keyframes t16-float {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
    50% { transform: translateY(-30px) scale(1.2); opacity: 0.9; }
}

.invitation-wrapper.template16-wrapper {
    --t16-w: min(600px, 92vw);
    --t16-h: calc(var(--t16-w) * 369.2307692308 / 600);
    --t16-half: calc(var(--t16-w) / 2);
    --t16-flap-h: calc(var(--t16-h) / 2);
    --t16-gold: #c9a962;
    --t16-rose: #e8b4b8;
    --t16-cream: #faf6f0;
    width: var(--t16-w);
    max-width: 92vw;
    margin: 0 auto;
    min-height: calc(var(--t16-h) + 110px);
    position: relative;
    z-index: 2;
}

.template16-envelope {
    width: 100%;
    height: var(--t16-h);
    position: relative;
    border-radius: 14px;
    overflow: visible;
    background: linear-gradient(145deg, #d4a574 0%, #c4956a 50%, #b8845a 100%);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 255, 255, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.template16-envelope::before,
.template16-envelope::after {
    content: "";
    position: absolute;
    bottom: 0;
}

.template16-envelope::before {
    right: 0;
    border-top: var(--t16-h) solid transparent;
    border-right: var(--t16-w) solid #c9a066;
    border-radius: 0 14px 0 0;
    z-index: 2;
}

.template16-envelope::after {
    left: 0;
    border-top: var(--t16-h) solid transparent;
    border-left: var(--t16-w) solid #d4b078;
    border-radius: 0 0 0 14px;
    z-index: 3;
}

.template16-flap {
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 0;
    border-right: var(--t16-half) solid transparent;
    border-top: var(--t16-flap-h) solid #ddb88a;
    border-left: var(--t16-half) solid transparent;
    z-index: 4;
    transform-origin: 50% 0%;
    border-radius: 14px 14px 0 0;
    filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.25));
}

.template16-wax-seal {
    position: absolute;
    left: 50%;
    top: calc(var(--t16-flap-h) - 28px);
    transform: translateX(-50%);
    width: clamp(56px, 14vw, 72px);
    height: clamp(56px, 14vw, 72px);
    border-radius: 50%;
    background: radial-gradient(circle at 35% 30%, #c41e3a, #8b1530 70%, #5c0f20);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4),
        inset 0 2px 4px rgba(255, 255, 255, 0.2),
        inset 0 -3px 6px rgba(0, 0, 0, 0.3);
    z-index: 6;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.template16-wax-seal span {
    font-family: "Cairo", serif;
    font-size: clamp(0.65rem, 2.8vw, 0.85rem);
    font-weight: 700;
    color: rgba(255, 220, 200, 0.95);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
}

.template16-wrapper .mask {
    width: 94%;
    max-width: calc(var(--t16-w) * 0.94);
    margin: 0 auto;
    clip-path: inset(100% 0 0 0);
}

.template16-wrapper .card {
    width: 100%;
    min-height: clamp(420px, 78vh, 820px);
    height: auto;
}

.template16-wrapper .front {
    min-height: clamp(420px, 78vh, 820px);
    padding: clamp(16px, 4vw, 28px);
    background: linear-gradient(180deg, var(--t16-cream) 0%, #f5ebe0 100%);
    border: 1px solid rgba(201, 169, 98, 0.35);
    text-align: center;
}

.template16-ornament {
    color: var(--t16-gold);
    font-size: 1.25rem;
    letter-spacing: 0.4em;
    margin-bottom: 8px;
    opacity: 0.85;
}

.template16-wrapper .front .event-name {
    font-family: "Cairo", serif;
    font-size: clamp(1.5rem, 5.5vw, 2.6rem);
    font-weight: 700;
    color: #3d2f3a;
    margin: 0 0 6px;
    line-height: 1.2;
}

.template16-host {
    font-size: clamp(0.95rem, 3vw, 1.15rem);
    color: #6b5b63;
    margin-bottom: 16px;
}

.template16-details {
    display: grid;
    gap: 12px;
    margin: 20px 0;
    text-align: right;
}

.template16-detail-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    background: rgba(255, 255, 255, 0.65);
    border-radius: 12px;
    border: 1px solid rgba(201, 169, 98, 0.25);
}

.template16-detail-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--t16-gold), #a8894a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.template16-detail-text {
    flex: 1;
    min-width: 0;
}

.template16-detail-label {
    font-size: 0.75rem;
    color: #8a7a82;
    margin-bottom: 2px;
}

.template16-detail-value {
    font-size: clamp(0.9rem, 2.8vw, 1.05rem);
    font-weight: 600;
    color: #2d2435;
    word-break: break-word;
}

.template16-wrapper .event-media-container {
    width: 100%;
    margin: 16px 0;
    border-radius: 14px;
    overflow: hidden;
    border: 2px solid rgba(201, 169, 98, 0.35);
}

.template16-wrapper .front .event-image,
.template16-wrapper .front .event-video {
    width: 100%;
    height: clamp(140px, 40vw, 260px);
    object-fit: cover;
    display: block;
}

.template16-wrapper .response-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    margin-top: 16px;
}

.template16-wrapper .response-buttons .btn {
    width: 100%;
    flex: none;
    border-radius: 50px;
    min-height: 48px;
}

.template16-wrapper .btn-primary.high-button {
    background: linear-gradient(135deg, var(--t16-gold), #a8894a);
    color: #fff;
    border: none;
}

.template16-wrapper .open-button {
    top: calc(var(--t16-h) + 18px);
    background: linear-gradient(135deg, rgba(201, 169, 98, 0.95), rgba(168, 137, 74, 0.95));
    border: 2px solid rgba(255, 255, 255, 0.5);
    color: #1a1520;
    font-weight: 700;
    width: min(100%, calc(var(--t16-w) - 24px));
    max-width: 340px;
    padding: clamp(14px, 3vw, 18px) clamp(24px, 5vw, 48px);
    font-size: clamp(1rem, 3.5vw, 1.25rem);
    animation: t16-pulse-btn 2.5s ease-in-out infinite;
}

@keyframes t16-pulse-btn {
    0%, 100% { box-shadow: 0 8px 28px rgba(201, 169, 98, 0.45); transform: translateX(-50%) scale(1); }
    50% { box-shadow: 0 12px 36px rgba(201, 169, 98, 0.65); transform: translateX(-50%) scale(1.02); }
}

.template16-wrapper .open-button {
    transform: translateX(-50%);
}

.template16-tap-hint {
    position: absolute;
    left: 50%;
    top: calc(var(--t16-h) + 72px);
    transform: translateX(-50%);
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.65);
    white-space: nowrap;
    z-index: 9;
    pointer-events: none;
}

@media (max-width: 768px) {
    .invitation-wrapper.template16-wrapper {
        --t16-w: min(400px, 95vw);
    }
    .template16-tap-hint {
        font-size: 0.72rem;
        top: calc(var(--t16-h) + 68px);
    }
}

@media (max-width: 480px) {
    .invitation-wrapper.template16-wrapper {
        --t16-w: min(350px, 98vw);
    }
}
</style>

<div class="template16-ambient" aria-hidden="true">
    @for ($i = 0; $i < 12; $i++)
        <span class="template16-particle" style="left: {{ rand(5, 95) }}%; top: {{ rand(10, 90) }}%; animation-delay: {{ $i * 0.4 }}s;"></span>
    @endfor
</div>

@if ($hasAudio)
<audio id="inviteOpeningAudio" preload="auto" style="display:none">
    @if (! empty($audioUrls['ogg']))
        <source src="{{ $audioUrls['ogg'] }}" type="audio/ogg">
    @endif
    @if (! empty($audioUrls['mp3']))
        <source src="{{ $audioUrls['mp3'] }}" type="audio/mpeg">
    @endif
</audio>
@endif

<div id="envelopeView" class="invitation-wrapper template16-wrapper">
    <div class="envelope template16-envelope">
        <div class="template16-wax-seal" aria-hidden="true">
            <span>{{ $initials }}</span>
        </div>
        <div class="mask">
            <div class="card">
                <div class="face front">
                    <div class="template16-ornament">✦ ✦ ✦</div>
                    <p class="template16-host">{{ $host_name }}</p>
                    <h1 class="event-name">{{ $invitation->event_name }}</h1>

                    @if ($eventDate || $eventTime || $invitation->address)
                    <div class="template16-details">
                        @if ($eventDate)
                        <div class="template16-detail-row">
                            <div class="template16-detail-icon">📅</div>
                            <div class="template16-detail-text">
                                <div class="template16-detail-label">التاريخ</div>
                                <div class="template16-detail-value">{{ $eventDate }}</div>
                            </div>
                        </div>
                        @endif
                        @if ($eventTime)
                        <div class="template16-detail-row">
                            <div class="template16-detail-icon">🕐</div>
                            <div class="template16-detail-text">
                                <div class="template16-detail-label">الوقت</div>
                                <div class="template16-detail-value">{{ $eventTime }}</div>
                            </div>
                        </div>
                        @endif
                        @if ($invitation->address)
                        <div class="template16-detail-row" @if($invitation->latitude && $invitation->longitude) onclick="openLocation()" style="cursor:pointer" @endif>
                            <div class="template16-detail-icon">📍</div>
                            <div class="template16-detail-text">
                                <div class="template16-detail-label">المكان</div>
                                <div class="template16-detail-value">{{ $invitation->address }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if ($invitation->description)
                    <p class="event-description">{{ $invitation->description }}</p>
                    @endif

                    @if ($invitation->image())
                    <div class="event-media-container">
                        @php
                            $mediaUrl = $invitation->image();
                            $extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
                            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
                            $isVideo = in_array($extension, $videoExtensions);
                        @endphp
                        @if ($isVideo)
                        <a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer">
                            <video class="event-image event-video" autoplay muted loop playsinline preload="metadata">
                                <source src="{{ $mediaUrl }}" type="video/{{ $extension === 'mov' ? 'quicktime' : $extension }}">
                            </video>
                        </a>
                        @else
                        <a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $mediaUrl }}" alt="{{ $invitation->event_name }}" class="event-image" loading="lazy" />
                        </a>
                        @endif
                    </div>
                    @endif

                    <div class="response-buttons">
                        <button type="button" class="btn btn-primary high-button" onclick="openMediaInNewTab()">
                            اضغط هنا لعرض الدعوة
                        </button>
                    </div>
                    <div class="response-buttons">
                        <button type="button" class="btn btn-accept" onclick="acceptInvitation()">
                            ✓ قبول الدعوة
                        </button>
                        <button type="button" class="btn btn-decline" onclick="declineInvitation()">
                            ✗ رفض الدعوة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flap template16-flap"></div>
    <button type="button" class="open-button" onclick="openEnvelope()">
        افتح الدعوة
    </button>
    <span class="template16-tap-hint">اضغط لفتح الظرف</span>
</div>
