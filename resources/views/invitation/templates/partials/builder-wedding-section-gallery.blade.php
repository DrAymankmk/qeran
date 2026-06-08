@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'gallery', 'label', 'لحظاتنا');
$title = WeddingInvitationPresenter::blockValue($bc, 'gallery', 'title', 'معرض الصور');
$photos = WeddingInvitationPresenter::blockRepeater($bc, 'gallery', 'photos');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'gallery');
@endphp
  <!-- ⑤ Photo Gallery (dynamic) -->
  <div class="wi-gallery-bg {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <section class="wi-section">
      <p class="wi-section-label">{{ $label }}</p>
      <h2 class="wi-section-title">{{ $title }}</h2>
      @if(count($photos) > 0)
      <div class="wi-gallery">
        @foreach($photos as $photo)
        @php $wide = !empty($photo['wide']); @endphp
        <div class="wi-photo" @if($wide) style="grid-column:span 2; aspect-ratio:2/1;" @endif>
          <div class="wi-photo-inner" style="background:#2e2618;position:relative;overflow:hidden;">
            @if(!empty($photo['url']))
            <img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] ?? '' }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            @endif
            @if(!empty($photo['caption']))
            <span style="position:relative;z-index:1;">{{ $photo['caption'] }}</span>
            @endif
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </section>
  </div>
