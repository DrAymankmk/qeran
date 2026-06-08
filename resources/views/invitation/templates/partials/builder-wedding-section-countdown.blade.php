@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'countdown', 'label', 'العد التنازلي ليوم الحدث');
$daysUnit = WeddingInvitationPresenter::blockValue($bc, 'countdown', 'days_unit', 'يوم');
$hoursUnit = WeddingInvitationPresenter::blockValue($bc, 'countdown', 'hours_unit', 'ساعة');
$minsUnit = WeddingInvitationPresenter::blockValue($bc, 'countdown', 'mins_unit', 'دقيقة');
$secsUnit = WeddingInvitationPresenter::blockValue($bc, 'countdown', 'secs_unit', 'ثانية');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'countdown');
@endphp
  <!-- ② Countdown (dynamic) -->
  <div class="wi-countdown-bar {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-countdown-label">{{ $label }}</p>
    <div class="wi-countdown-grid">
      <div class="wi-count-block">
        <span class="wi-count-num" id="cd-days">—</span>
        <span class="wi-count-unit">{{ $daysUnit }}</span>
      </div>
      <div class="wi-count-block">
        <span class="wi-count-num" id="cd-hours">—</span>
        <span class="wi-count-unit">{{ $hoursUnit }}</span>
      </div>
      <div class="wi-count-block">
        <span class="wi-count-num" id="cd-mins">—</span>
        <span class="wi-count-unit">{{ $minsUnit }}</span>
      </div>
      <div class="wi-count-block">
        <span class="wi-count-num" id="cd-secs">—</span>
        <span class="wi-count-unit">{{ $secsUnit }}</span>
      </div>
    </div>
  </div>
