@if(mediaDiskSupportsDirectUpload())
@php
    $designDirectUploadJsConfig = [
        'presignUrl' => route('media.direct-upload.presign'),
        'csrfToken' => csrf_token(),
        'bucketName' => \App\Helpers\Constant::DESIGN_IMAGE_FOLDER_NAME,
        'waitMsg' => __('validation.design_direct_upload_wait'),
        'progressMsg' => __('validation.design_direct_upload_progress'),
        'doneMsg' => __('validation.design_direct_upload_done'),
    ];
@endphp
<input type="hidden" name="media_upload_token" id="media_upload_token" value="">
<p class="form-text small text-muted d-none mb-0" id="design-media-direct-status" aria-live="polite"></p>
<script>
(function() {
	var cfg = @json($designDirectUploadJsConfig);
	var VIDEO_EXT = ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v'];

	function guessVideoMime(name) {
		var ext = (name.split('.').pop() || '').toLowerCase();
		var map = {
			mp4: 'video/mp4',
			webm: 'video/webm',
			mov: 'video/quicktime',
			m4v: 'video/mp4',
			ogv: 'video/ogg',
			ogg: 'video/ogg'
		};
		return map[ext] || 'video/mp4';
	}

	function isVideoFile(file) {
		if (!file) return false;
		if (file.type && file.type.indexOf('video/') === 0) return true;
		var ext = (file.name.split('.').pop() || '').toLowerCase();
		return VIDEO_EXT.indexOf(ext) !== -1;
	}

	function normHeaders(raw) {
		var h = new Headers();
		var skip = { host: true };
		Object.keys(raw || {}).forEach(function(hdrName) {
			if (skip[String(hdrName).toLowerCase()]) return;
			h.append(hdrName, raw[hdrName]);
		});
		return h;
	}

	document.addEventListener('DOMContentLoaded', function() {
		var input = document.getElementById('image');
		var tokenInput = document.getElementById('media_upload_token');
		var status = document.getElementById('design-media-direct-status');
		var form = input && input.closest('form');
		if (!input || !tokenInput || !form) return;

		form.addEventListener('submit', function(e) {
			var file = input.files && input.files[0];
			if (!file || !isVideoFile(file)) return;
			// Only block if we already dropped the file field for token-only submit but token never arrived
			if (!input.hasAttribute('name') && !tokenInput.value) {
				e.preventDefault();
				alert(cfg.waitMsg);
			}
		});

		input.addEventListener('change', function() {
			tokenInput.value = '';
			input.setAttribute('name', 'image');

			if (status) {
				status.textContent = '';
				status.classList.add('d-none');
			}

			var file = input.files && input.files[0];
			if (!file || !isVideoFile(file)) return;

			if (status) {
				status.textContent = cfg.progressMsg;
				status.classList.remove('d-none');
			}

			var payload = {
				bucket_name: cfg.bucketName,
				original_filename: file.name,
				content_type: file.type || guessVideoMime(file.name),
				content_length: file.size
			};

			fetch(cfg.presignUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': cfg.csrfToken,
					Accept: 'application/json'
				},
				credentials: 'same-origin',
				body: JSON.stringify(payload)
			})
				.then(function(res) {
					return res.json().then(function(data) {
						return { ok: res.ok, status: res.status, data: data };
					});
				})
				.then(function(wrap) {
					if (!wrap.ok) {
						throw new Error((wrap.data && wrap.data.message) || 'Presign failed');
					}
					return wrap.data;
				})
				.then(function(data) {
					return fetch(data.url, {
						method: 'PUT',
						headers: normHeaders(data.headers),
						body: file
					}).then(function(putRes) {
						if (!putRes.ok) {
							throw new Error(
								'Upload to storage failed (' + putRes.status + '). Check Wasabi CORS or try saving again without waiting for cloud upload.'
							);
						}
						tokenInput.value = data.upload_token;
						input.removeAttribute('name');
						if (status) {
							status.textContent = cfg.doneMsg;
						}
					});
				})
				.catch(function(err) {
					tokenInput.value = '';
					input.setAttribute('name', 'image');
					if (status) {
						status.textContent = '';
						status.classList.add('d-none');
					}
					alert(err.message || 'Upload failed');
				});
		});
	});
})();
</script>
@endif
