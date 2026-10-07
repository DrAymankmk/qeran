<div class="contact-block">
	<div class="contact-title">{{ $contact['contact_name'] }}</div>
	<div class="contact-meta">
		<div>{{ __('admin.phone') }}: {{ $contact['phone'] !== '' ? $contact['phone'] : __('admin.no-data-available') }}</div>
		<div>{{ __('admin.invitation-count') }}: {{ $contact['invitation_count'] }}</div>
		<div>{{ __('admin.status') }}: {{ $contact['send_status'] }}</div>
		<div>{{ __('admin.acceptance-status') }}: {{ $contact['acceptance_status'] }}</div>
	</div>

	@if(empty($contact['guest_qr_cards']))
		<div>{{ __('admin.ib-preview-qr-missing') }}</div>
	@else
		<table class="qr-table">
			<tr>
				@foreach($contact['guest_qr_cards'] as $i => $guestQr)
					@if($i > 0 && $i % 3 === 0)
						</tr><tr>
					@endif
					<td>
						@if(!empty($guestQr['qr_src']))
							<img src="{{ $guestQr['qr_src'] }}" width="110" height="110" alt="QR">
						@else
							<div>{{ __('admin.ib-preview-qr-missing') }}</div>
						@endif
						<div class="qr-name">{{ $guestQr['name'] ?? '' }}</div>
					</td>
				@endforeach
				@php
					$remainder = count($contact['guest_qr_cards']) % 3;
				@endphp
				@if($remainder > 0)
					@for($j = $remainder; $j < 3; $j++)
						<td></td>
					@endfor
				@endif
			</tr>
		</table>
	@endif
</div>
