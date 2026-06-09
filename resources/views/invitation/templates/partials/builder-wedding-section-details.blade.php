@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'event_details');
@endphp
  <!-- ④ Event Details (dynamic) -->
  <section class="wi-section {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-section-label">{{ $wiDetailsLabel }}</p>
    <h2 class="wi-section-title">{{ $wiDetailsTitle }}</h2>
    <div class="wi-details-grid">
      <div class="wi-detail-card">
        @include('invitation.templates.partials.builder-wedding-detail-icon', [
          'icon' => WeddingInvitationPresenter::detailCardIcon($bc, 'date', 'calendar'),
          'iconUrl' => WeddingInvitationPresenter::detailCardIconUrl($bc, 'date'),
        ])
        <p class="wi-detail-heading">التاريخ</p>
        <p class="wi-detail-main">{!! $wiDateMain ?: '—' !!}</p>
        <p class="wi-detail-sub">{{ $wiDayName }}</p>
      </div>
      <div class="wi-detail-card">
        @include('invitation.templates.partials.builder-wedding-detail-icon', [
          'icon' => WeddingInvitationPresenter::detailCardIcon($bc, 'ceremony', 'clock'),
          'iconUrl' => WeddingInvitationPresenter::detailCardIconUrl($bc, 'ceremony'),
        ])
        <p class="wi-detail-heading">الحفل</p>
        <p class="wi-detail-main">{{ $wiCeremonyTime }}</p>
        @if($wiCeremonyNote)<p class="wi-detail-sub">{{ $wiCeremonyNote }}</p>@endif
      </div>
      <div class="wi-detail-card">
        @include('invitation.templates.partials.builder-wedding-detail-icon', [
          'icon' => WeddingInvitationPresenter::detailCardIcon($bc, 'venue', 'location'),
          'iconUrl' => WeddingInvitationPresenter::detailCardIconUrl($bc, 'venue'),
        ])
        <p class="wi-detail-heading">المكان</p>
        <p class="wi-detail-main">{!! nl2br(e($wiVenueName)) !!}</p>
        @if($wiVenueLocation)<p class="wi-detail-sub">{{ $wiVenueLocation }}</p>@endif
      </div>
      <div class="wi-detail-card">
        @include('invitation.templates.partials.builder-wedding-detail-icon', [
          'icon' => WeddingInvitationPresenter::detailCardIcon($bc, 'reception', 'reception'),
          'iconUrl' => WeddingInvitationPresenter::detailCardIconUrl($bc, 'reception'),
        ])
        <p class="wi-detail-heading">الاستقبال</p>
        <p class="wi-detail-main">{{ $wiReceptionTime ?: '—' }}</p>
        @if($wiReceptionNote)<p class="wi-detail-sub">{{ $wiReceptionNote }}</p>@endif
      </div>
    </div>
  </section>
