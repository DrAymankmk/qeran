@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'wishes', 'label', 'تهانيكم');
$title = WeddingInvitationPresenter::blockValue($bc, 'wishes', 'title', 'رسائل المحبة');
$body = WeddingInvitationPresenter::blockValue($bc, 'wishes', 'body', '');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'wishes');
@endphp
  <!-- ⑫ Guestbook (dynamic) -->
  <div class="wi-guestbook-bg {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <section class="wi-section">
      <p class="wi-section-label">{{ $label }}</p>
      <h2 class="wi-section-title">{{ $title }}</h2>
      @if($body !== '')
      <p class="wi-section-body">{{ $body }}</p>
      @endif
      <div style="margin-top:24px;">
        <button type="button" class="wi-venue-btn" style="color:#2c2416;border-color:#2c2416;">اترك رسالة ↗</button>
      </div>
    </section>
  </div>
