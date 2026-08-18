@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$wiMedia = WeddingInvitationPresenter::mediaSection($bc);
$wiMediaClasses = 'wi-hero wi-media-section';
$wiMediaImageStyle = '';
if (! empty($wiMedia['hasVideo'])) {
$wiMediaClasses .= ' wi-hero-has-video';
} elseif (! empty($wiMedia['hasImage']) && ! empty($wiMedia['url'])) {
$wiMediaClasses .= ' wi-hero-has-image';
$wiMediaImageStyle = '--wi-hero-image: url('.e($wiMedia['url']).')';
}
@endphp
@if(!empty($wiMedia['url']))
<!-- Media (image or video, no text) -->
<section class="{{ $wiMediaClasses }}" @if($wiMediaImageStyle !=='' ) style="{{ $wiMediaImageStyle }}" @endif>
	@if(!empty($wiMedia['hasVideo']))
	<div class="wi-hero-media" aria-hidden="true">
		<video class="wi-hero-video" autoplay muted loop playsinline webkit-playsinline preload="auto"
			src="{{ $wiMedia['url'] }}"></video>
	</div>
	@elseif(!empty($wiMedia['hasImage']))
	<div class="wi-hero-media" aria-hidden="true">
		<img class="wi-hero-image" src="{{ $wiMedia['url'] }}" alt="" style="object-fit: fill !important;">
	</div>
	@endif
</section>
@endif