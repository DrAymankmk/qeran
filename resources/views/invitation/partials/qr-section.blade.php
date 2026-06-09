@php
	$qrGuestId = $user->id ?? null;
	$qrUrl = $qrGuestId ? $invitation->qr($invitation->id, $qrGuestId) : null;
	$qrFilename = 'Qr-' . $invitation->id . '-' . $qrGuestId . '.png';
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
		function wiQrImageToPngBlob(image) {
			return new Promise(function (resolve, reject) {
				var width = image.naturalWidth || image.width || 200;
				var height = image.naturalHeight || image.height || 200;
				var canvas = document.createElement('canvas');
				canvas.width = width;
				canvas.height = height;
				var ctx = canvas.getContext('2d');
				ctx.fillStyle = '#ffffff';
				ctx.fillRect(0, 0, width, height);
				ctx.drawImage(image, 0, 0, width, height);
				canvas.toBlob(function (blob) {
					if (blob) {
						resolve(blob);
						return;
					}
					reject(new Error('png conversion failed'));
				}, 'image/png');
			});
		}

		function wiTriggerQrPngDownload(blob, filename) {
			var objectUrl = URL.createObjectURL(blob);
			var link = document.createElement('a');
			link.href = objectUrl;
			link.download = filename;
			document.body.appendChild(link);
			link.click();
			link.remove();
			URL.revokeObjectURL(objectUrl);
		}

		function wiSvgTextToPngBlob(svgText) {
			return new Promise(function (resolve, reject) {
				var svgImg = new Image();
				var svgUrl = URL.createObjectURL(new Blob([svgText], { type: 'image/svg+xml' }));
				svgImg.onload = function () {
					wiQrImageToPngBlob(svgImg)
						.then(resolve)
						.catch(reject)
						.finally(function () {
							URL.revokeObjectURL(svgUrl);
						});
				};
				svgImg.onerror = function () {
					URL.revokeObjectURL(svgUrl);
					reject(new Error('svg conversion failed'));
				};
				svgImg.src = svgUrl;
			});
		}

		function downloadInvitationQr(button) {
			var url = button.dataset.qrUrl;
			var filename = (button.dataset.qrFilename || 'qr-code.png').replace(/\.[^.]+$/, '') + '.png';
			var section = button.closest('.qr-section');
			var img = section ? section.querySelector('img') : null;

			function downloadPngBlob(blob) {
				wiTriggerQrPngDownload(blob, filename);
			}

			function fallbackFetch() {
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
						if (blob.type === 'image/svg+xml' || /\.svg(\?|$)/i.test(url)) {
							return blob.text().then(wiSvgTextToPngBlob);
						}
						if (blob.type === 'image/png') {
							return blob;
						}
						var rasterImg = new Image();
						var rasterUrl = URL.createObjectURL(blob);
						return new Promise(function (resolve, reject) {
							rasterImg.onload = function () {
								wiQrImageToPngBlob(rasterImg)
									.then(resolve)
									.catch(reject)
									.finally(function () {
										URL.revokeObjectURL(rasterUrl);
									});
							};
							rasterImg.onerror = function () {
								URL.revokeObjectURL(rasterUrl);
								reject(new Error('image conversion failed'));
							};
							rasterImg.src = rasterUrl;
						});
					})
					.then(downloadPngBlob)
					.catch(function () {
						window.open(url, '_blank');
					});
			}

			if (!img) {
				fallbackFetch();
				return;
			}

			function downloadFromImage() {
				wiQrImageToPngBlob(img).then(downloadPngBlob).catch(fallbackFetch);
			}

			if (img.complete && img.naturalWidth) {
				downloadFromImage();
				return;
			}

			img.addEventListener('load', downloadFromImage, { once: true });
			img.addEventListener('error', fallbackFetch, { once: true });
		}
	</script>
@endonce
