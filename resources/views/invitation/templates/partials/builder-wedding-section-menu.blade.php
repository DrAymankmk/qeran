@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'menu', 'label', 'قائمة الطعام');
$title = WeddingInvitationPresenter::blockValue($bc, 'menu', 'title', 'ماذا نقدّم');
$body = WeddingInvitationPresenter::blockValue($bc, 'menu', 'body', '');
$items = WeddingInvitationPresenter::blockRepeater($bc, 'menu', 'items');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'menu');
@endphp
  <!-- ⑬b Menu (dynamic) -->
  <section class="wi-section {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-section-label">{{ $label }}</p>
    <h2 class="wi-section-title">{{ $title }}</h2>
    @if($body !== '')
    <p class="wi-section-body">{{ $body }}</p>
    @endif
    @if(count($items) > 0)
    <div class="wi-schedule">
      @foreach($items as $item)
      <div class="wi-schedule-row">
        <div class="wi-sch-dot"></div>
        <div class="wi-sch-info">
          @if(!empty($item['name']))<p class="wi-sch-title">{{ $item['name'] }}</p>@endif
          @if(!empty($item['description']))<p class="wi-sch-place">{{ $item['description'] }}</p>@endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </section>
