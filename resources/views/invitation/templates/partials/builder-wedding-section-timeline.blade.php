@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'timeline', 'label', 'يوم الاحتفال');
$title = WeddingInvitationPresenter::blockValue($bc, 'timeline', 'title', 'الجدول الزمني');
$items = WeddingInvitationPresenter::blockRepeater($bc, 'timeline', 'items');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'timeline');
@endphp
  <!-- ⑥ Schedule (dynamic) -->
  <section class="wi-section {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-section-label">{{ $label }}</p>
    <h2 class="wi-section-title">{{ $title }}</h2>
    @if(count($items) > 0)
    <div class="wi-schedule">
      @foreach($items as $item)
      <div class="wi-schedule-row">
        @if(!empty($item['time']))<span class="wi-sch-time">{{ WeddingInvitationPresenter::formatBlockDisplay('time', $item['time']) }}</span>@endif
        <div class="wi-sch-dot"></div>
        <div class="wi-sch-info">
          @if(!empty($item['title']))<p class="wi-sch-title">{{ $item['title'] }}</p>@endif
          @if(!empty($item['place']))<p class="wi-sch-place">{{ $item['place'] }}</p>@endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </section>
