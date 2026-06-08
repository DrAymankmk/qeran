@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bs = WeddingInvitationPresenter::blockStyleAttributes($builderConfig ?? [], 'event_details');
@endphp
  <!-- ④ Event Details (dynamic) -->
  <section class="wi-section {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-section-label">{{ $wiDetailsLabel }}</p>
    <h2 class="wi-section-title">{{ $wiDetailsTitle }}</h2>
    <div class="wi-details-grid">
      <div class="wi-detail-card">
        <svg class="wi-detail-icon" viewBox="0 0 40 40" fill="none">
          <rect x="5" y="9" width="30" height="26" rx="3" stroke="currentColor" stroke-width="0.8"/>
          <line x1="5" y1="17" x2="35" y2="17" stroke="currentColor" stroke-width="0.6"/>
          <line x1="14" y1="5" x2="14" y2="13" stroke="currentColor" stroke-width="0.8"/>
          <line x1="26" y1="5" x2="26" y2="13" stroke="currentColor" stroke-width="0.8"/>
          <rect x="12" y="22" width="6" height="5" rx="1" fill="currentColor" opacity="0.5"/>
        </svg>
        <p class="wi-detail-heading">التاريخ</p>
        <p class="wi-detail-main">{!! $wiDateMain ?: '—' !!}</p>
        <p class="wi-detail-sub">{{ $wiDayName }}</p>
      </div>
      <div class="wi-detail-card">
        <svg class="wi-detail-icon" viewBox="0 0 40 40" fill="none">
          <circle cx="20" cy="20" r="13" stroke="currentColor" stroke-width="0.8"/>
          <line x1="20" y1="20" x2="20" y2="10" stroke="currentColor" stroke-width="0.8" stroke-linecap="round"/>
          <line x1="20" y1="20" x2="27" y2="24" stroke="currentColor" stroke-width="0.8" stroke-linecap="round"/>
          <circle cx="20" cy="20" r="1.5" fill="currentColor"/>
        </svg>
        <p class="wi-detail-heading">الحفل</p>
        <p class="wi-detail-main">{{ $wiCeremonyTime }}</p>
        @if($wiCeremonyNote)<p class="wi-detail-sub">{{ $wiCeremonyNote }}</p>@endif
      </div>
      <div class="wi-detail-card">
        <svg class="wi-detail-icon" viewBox="0 0 40 40" fill="none">
          <path d="M20 6 C12 6 7 13 7 20 C7 30 20 36 20 36 C20 36 33 30 33 20 C33 13 28 6 20 6Z" stroke="currentColor" stroke-width="0.8"/>
          <circle cx="20" cy="20" r="4" stroke="currentColor" stroke-width="0.6"/>
        </svg>
        <p class="wi-detail-heading">المكان</p>
        <p class="wi-detail-main">{!! nl2br(e($wiVenueName)) !!}</p>
        @if($wiVenueLocation)<p class="wi-detail-sub">{{ $wiVenueLocation }}</p>@endif
      </div>
      <div class="wi-detail-card">
        <svg class="wi-detail-icon" viewBox="0 0 40 40" fill="none">
          <path d="M8 28 Q12 16 20 14 Q28 16 32 28" stroke="currentColor" stroke-width="0.8" fill="none"/>
          <circle cx="14" cy="18" r="2" stroke="currentColor" stroke-width="0.6"/>
          <circle cx="26" cy="18" r="2" stroke="currentColor" stroke-width="0.6"/>
          <path d="M14 28 Q17 32 20 28 Q23 32 26 28" stroke="currentColor" stroke-width="0.6" fill="none"/>
        </svg>
        <p class="wi-detail-heading">الاستقبال</p>
        <p class="wi-detail-main">{{ $wiReceptionTime ?: '—' }}</p>
        @if($wiReceptionNote)<p class="wi-detail-sub">{{ $wiReceptionNote }}</p>@endif
      </div>
    </div>
  </section>
