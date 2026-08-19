@php
	$guestQrCards = $guestQrCards ?? [];
	$wrapperClass = $wrapperClass ?? '';
@endphp

<div class="qr-guests-section {{ $wrapperClass }}">
	@if(count($guestQrCards) === 0)
		@include('invitation.partials.qr-section', [
			'invitation' => $invitation,
			'user' => $user ?? null,
			'contactLog' => $contactLog ?? null,
		])
	@else
		<p class="qr-guests-heading">{{ __('messages.invitation_qr_codes_heading') }}</p>
		@foreach($guestQrCards as $guestQr)
			<div class="qr-section qr-guest-card" data-slot="{{ $guestQr['slot'] }}">
				<p class="qr-guest-label">
					@if(!empty($guestQr['is_primary']))
						{{ __('messages.invitation_qr_primary_guest') }} — {{ $guestQr['name'] }}
					@else
						{{ __('messages.invitation_qr_companion_guest') }} — {{ $guestQr['name'] }}
					@endif
				</p>
				<img src="{{ $guestQr['qr_url'] ?? '' }}"
					class="invitation-guest-qr-image"
					alt="{{ __('admin.ib-preview-qr-alt') }}"
					@if(empty($guestQr['qr_url'])) style="display:none" @endif />
				<p class="qr-missing-message" @if(!empty($guestQr['qr_url'])) style="display:none" @endif>
					{{ __('admin.ib-preview-qr-missing') }}
				</p>
				<p class="qr-hint-message" @if(empty($guestQr['qr_url'])) style="display:none" @endif>
					{{ __('admin.ib-preview-qr-hint') }}
				</p>
				<button type="button"
					class="qr-download-button"
					data-qr-url="{{ $guestQr['qr_url'] ?? '' }}"
					data-qr-filename="{{ $guestQr['qr_filename'] ?? '' }}"
					onclick="downloadInvitationQr(this)"
					@if(empty($guestQr['qr_url'])) style="display:none" @endif>
					{{ __('admin.ib-preview-qr-download') }}
				</button>
			</div>
		@endforeach
	@endif
</div>

@include('invitation.partials.qr-download-scripts')

@once
	<style>
		.qr-guests-section {
			display: flex;
			flex-direction: column;
			gap: 1.25rem;
			width: 100%;
		}

		.qr-guests-heading {
			font-weight: 700;
			text-align: center;
			margin-bottom: 0.25rem;
		}

		.qr-guest-card {
			border: 1px solid rgba(255, 255, 255, 0.12);
			border-radius: 16px;
			padding: 1rem;
		}

		.qr-guest-label {
			font-weight: 600;
			margin-bottom: 0.75rem;
			text-align: center;
		}
	</style>
@endonce
