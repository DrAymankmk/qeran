@php
	$qrUrl = null;
	$qrFilename = null;
	if (!empty($contactLog)) {
		$qrUrl = $invitation->qrForContact($contactLog->id);
		$qrFilename = 'Qr-' . $invitation->id . '-contact-' . $contactLog->id . '.png';
	} else {
		$qrGuestId = $user->id ?? null;
		$qrUrl = $qrGuestId ? $invitation->qr($invitation->id, $qrGuestId) : null;
		$qrFilename = 'Qr-' . $invitation->id . '-' . $qrGuestId . '.png';
	}
@endphp
<div class="qr-section {{ $wrapperClass ?? '' }}">
	<img src="{{ $qrUrl ?? '' }}"
		id="invitationQrImage"
		alt="{{ __('admin.ib-preview-qr-alt') }}"
		@if(!$qrUrl) style="display:none" @endif />
	<p class="qr-missing-message" @if($qrUrl) style="display:none" @endif>{{ __('admin.ib-preview-qr-missing') }}</p>
	<p class="qr-hint-message" @if(!$qrUrl) style="display:none" @endif>{{ __('admin.ib-preview-qr-hint') }}</p>
	<button type="button"
		class="qr-download-button"
		data-qr-url="{{ $qrUrl ?? '' }}"
		data-qr-filename="{{ $qrFilename ?? '' }}"
		onclick="downloadInvitationQr(this)"
		@if(!$qrUrl) style="display:none" @endif>
		{{ __('admin.ib-preview-qr-download') }}
	</button>
</div>
@include('invitation.partials.qr-download-scripts')
