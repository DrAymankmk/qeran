@php
	$qrGuestId = $user->id ?? null;
	$qrUrl = $qrGuestId ? $invitation->qr($invitation->id, $qrGuestId) : null;
	$qrExtension = $qrUrl
		? (pathinfo(parse_url($qrUrl, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'png')
		: 'png';
	$qrFilename = 'Qr-' . $invitation->id . '-' . $qrGuestId . '.' . $qrExtension;
@endphp
<div class="qr-section {{ $wrapperClass ?? '' }}">
	@if($qrUrl)
		<img src="{{ $qrUrl }}" id="invitationQrImage" alt="{{ __('admin.ib-preview-qr-alt') }}" />
		<p>{{ __('admin.ib-preview-qr-hint') }}</p>
		<button type="button"
			class="qr-download-button"
			data-qr-url="{{ $qrUrl }}"
			data-qr-filename="{{ $qrFilename }}"
			onclick="downloadInvitationQr(this)">
			{{ __('admin.ib-preview-qr-download') }}
		</button>
	@else
		<p>{{ __('admin.ib-preview-qr-missing') }}</p>
	@endif
</div>
@once
	<script>
		function downloadInvitationQr(button) {
			const url = button.dataset.qrUrl;
			const filename = button.dataset.qrFilename;

			if (!url) {
				return;
			}

			fetch(url)
				.then(function (response) {
					if (!response.ok) {
						throw new Error('download failed');
					}

					return response.blob();
				})
				.then(function (blob) {
					const objectUrl = URL.createObjectURL(blob);
					const link = document.createElement('a');
					link.href = objectUrl;
					link.download = filename;
					document.body.appendChild(link);
					link.click();
					link.remove();
					URL.revokeObjectURL(objectUrl);
				})
				.catch(function () {
					window.open(url, '_blank');
				});
		}
	</script>
@endonce
