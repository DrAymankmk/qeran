@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'gift_list', 'label', 'إن رغبت بالإهداء');
$title = WeddingInvitationPresenter::blockValue($bc, 'gift_list', 'title', 'قائمة الهدايا');
$body = WeddingInvitationPresenter::blockValue($bc, 'gift_list', 'body', '');
$items = WeddingInvitationPresenter::blockRepeater($bc, 'gift_list', 'items');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'gift_list');
@endphp
  <!-- ⑩ Gift Registry (dynamic) -->
  <section class="wi-section {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <p class="wi-section-label">{{ $label }}</p>
    <h2 class="wi-section-title">{{ $title }}</h2>
    @if($body !== '')
    <p class="wi-section-body">{{ $body }}</p>
    @endif
    @if(count($items) > 0)
    <div class="wi-gift-grid">
      @foreach($items as $item)
      <div class="wi-gift-card">
        <div class="wi-gift-icon">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M16 6C16 6 10 2 7 6C4 10 8 14 16 20C24 14 28 10 25 6C22 2 16 6 16 6Z" stroke="currentColor" stroke-width="0.8" fill="none"/></svg>
        </div>
        @if(!empty($item['url']))
        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-reset">
        @endif
          @if(!empty($item['name']))<p class="wi-gift-name">{{ $item['name'] }}</p>@endif
          @if(!empty($item['subtitle']))<p class="wi-gift-sub">{{ $item['subtitle'] }}</p>@endif
        @if(!empty($item['url']))
        </a>
        @endif
      </div>
      @endforeach
    </div>
    @endif
  </section>
