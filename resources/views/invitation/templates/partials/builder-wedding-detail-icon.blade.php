@php
	$iconUrl = trim((string) ($iconUrl ?? ''));
	$iconKey = $icon ?? 'calendar';
	$allowed = array_keys(config('invitation_builder.detail_card_icons', []));
	if (! in_array($iconKey, $allowed, true)) {
		$iconKey = 'calendar';
	}
@endphp
@if($iconUrl !== '')
<img src="{{ $iconUrl }}" class="wi-detail-icon wi-detail-icon-img" alt="" loading="lazy" decoding="async">
@else
<svg class="wi-detail-icon" viewBox="0 0 40 40" fill="none" aria-hidden="true">
@switch($iconKey)
	@case('clock')
	<circle cx="20" cy="20" r="13" stroke="currentColor" stroke-width="0.8"/>
	<line x1="20" y1="20" x2="20" y2="10" stroke="currentColor" stroke-width="0.8" stroke-linecap="round"/>
	<line x1="20" y1="20" x2="27" y2="24" stroke="currentColor" stroke-width="0.8" stroke-linecap="round"/>
	<circle cx="20" cy="20" r="1.5" fill="currentColor"/>
	@break
	@case('location')
	<path d="M20 6 C12 6 7 13 7 20 C7 30 20 36 20 36 C20 36 33 30 33 20 C33 13 28 6 20 6Z" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="20" cy="20" r="4" stroke="currentColor" stroke-width="0.6"/>
	@break
	@case('reception')
	<path d="M8 28 Q12 16 20 14 Q28 16 32 28" stroke="currentColor" stroke-width="0.8" fill="none"/>
	<circle cx="14" cy="18" r="2" stroke="currentColor" stroke-width="0.6"/>
	<circle cx="26" cy="18" r="2" stroke="currentColor" stroke-width="0.6"/>
	<path d="M14 28 Q17 32 20 28 Q23 32 26 28" stroke="currentColor" stroke-width="0.6" fill="none"/>
	@break
	@case('heart')
	<path d="M20 33 C20 33 8 24 8 16 C8 11 12 8 16 8 C18 8 20 10 20 10 C20 10 22 8 24 8 C28 8 32 11 32 16 C32 24 20 33 20 33Z" stroke="currentColor" stroke-width="0.8"/>
	@break
	@case('ring')
	<circle cx="15" cy="22" r="7" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="25" cy="22" r="7" stroke="currentColor" stroke-width="0.8"/>
	<path d="M18 16 L20 10 L22 16" stroke="currentColor" stroke-width="0.6" fill="none"/>
	@break
	@case('star')
	<path d="M20 7 L23 16 L33 16 L25 22 L28 31 L20 25 L12 31 L15 22 L7 16 L17 16 Z" stroke="currentColor" stroke-width="0.8" stroke-linejoin="round"/>
	@break
	@case('gift')
	<rect x="9" y="18" width="22" height="14" rx="2" stroke="currentColor" stroke-width="0.8"/>
	<line x1="20" y1="18" x2="20" y2="32" stroke="currentColor" stroke-width="0.6"/>
	<path d="M12 18 C12 14 16 12 20 14 C24 12 28 14 28 18" stroke="currentColor" stroke-width="0.8" fill="none"/>
	@break
	@case('music')
	<circle cx="14" cy="28" r="4" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="28" cy="24" r="4" stroke="currentColor" stroke-width="0.8"/>
	<line x1="18" y1="28" x2="18" y2="12" stroke="currentColor" stroke-width="0.8"/>
	<line x1="32" y1="24" x2="32" y2="10" stroke="currentColor" stroke-width="0.8"/>
	<line x1="18" y1="12" x2="32" y2="10" stroke="currentColor" stroke-width="0.8"/>
	@break
	@case('camera')
	<rect x="7" y="14" width="26" height="18" rx="3" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="20" cy="23" r="5" stroke="currentColor" stroke-width="0.8"/>
	<path d="M14 14 L17 10 H23 L26 14" stroke="currentColor" stroke-width="0.8" fill="none"/>
	@break
	@case('car')
	<path d="M8 24 H32 L30 18 H10 Z" stroke="currentColor" stroke-width="0.8" stroke-linejoin="round"/>
	<rect x="8" y="24" width="24" height="6" rx="2" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="14" cy="30" r="2" fill="currentColor"/>
	<circle cx="26" cy="30" r="2" fill="currentColor"/>
	@break
	@case('users')
	<circle cx="15" cy="16" r="4" stroke="currentColor" stroke-width="0.8"/>
	<circle cx="27" cy="16" r="4" stroke="currentColor" stroke-width="0.8"/>
	<path d="M8 30 C8 25 11 22 15 22 C19 22 22 25 22 30" stroke="currentColor" stroke-width="0.8" fill="none"/>
	<path d="M18 30 C18 25 21 22 27 22 C31 22 34 25 34 30" stroke="currentColor" stroke-width="0.8" fill="none"/>
	@break
	@default
	<rect x="5" y="9" width="30" height="26" rx="3" stroke="currentColor" stroke-width="0.8"/>
	<line x1="5" y1="17" x2="35" y2="17" stroke="currentColor" stroke-width="0.6"/>
	<line x1="14" y1="5" x2="14" y2="13" stroke="currentColor" stroke-width="0.8"/>
	<line x1="26" y1="5" x2="26" y2="13" stroke="currentColor" stroke-width="0.8"/>
	<rect x="12" y="22" width="6" height="5" rx="1" fill="currentColor" opacity="0.5"/>
@endswitch
</svg>
@endif
