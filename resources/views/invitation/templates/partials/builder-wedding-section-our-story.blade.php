@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'our_story', 'label', 'كيف بدأت قصتنا');
$title = WeddingInvitationPresenter::blockValue($bc, 'our_story', 'title', 'قصتنا');
$body = WeddingInvitationPresenter::blockValue($bc, 'our_story', 'body', '');
$milestones = WeddingInvitationPresenter::blockRepeater($bc, 'our_story', 'milestones');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'our_story');
@endphp
  <!-- ③ Our Story (dynamic) -->
  <div class="wi-story-bg {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <section class="wi-section">
      <p class="wi-section-label">{{ $label }}</p>
      <h2 class="wi-section-title">{{ $title }}</h2>
      @if($body !== '')
      <p class="wi-section-body">{{ $body }}</p>
      @endif

      @if(count($milestones) > 0)
      <div class="wi-timeline">
        @foreach($milestones as $item)
        <div class="wi-tl-item">
          <div class="wi-tl-dot"><div class="wi-tl-dot-inner"></div></div>
          <div>
            @if(!empty($item['year']))<p class="wi-tl-year">{{ WeddingInvitationPresenter::formatBlockDisplay('date', $item['year']) }}</p>@endif
            @if(!empty($item['text']))<p class="wi-tl-text">{{ $item['text'] }}</p>@endif
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </section>
  </div>
