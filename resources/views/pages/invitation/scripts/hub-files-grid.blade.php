{{-- Shared: render invitation hub_files in admin modals (small previews + type + uploader) --}}
<style>
.invitation-hub-files-grid {
	display: flex;
	flex-wrap: wrap;
	gap: 0.75rem;
	justify-content: flex-start;
}

.invitation-hub-file-card {
	flex: 0 0 auto;
	width: 140px;
	max-width: 100%;
	border: 1px solid #e9ecef;
	border-radius: 0.375rem;
	overflow: hidden;
	background: #f8f9fa;
}

.invitation-hub-file-card__preview {
	height: 100px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: #fff;
	overflow: hidden;
}

.invitation-hub-file-card__preview img,
.invitation-hub-file-card__preview video {
	max-width: 100%;
	max-height: 100px;
	width: auto;
	height: auto;
	object-fit: contain;
	vertical-align: middle;
}

.invitation-hub-file-card__preview audio {
	width: 100%;
	max-height: 36px;
	transform: scale(0.9);
	transform-origin: center;
}

.invitation-hub-file-card__meta {
	padding: 0.4rem 0.5rem;
	font-size: 0.7rem;
	line-height: 1.3;
	text-align: center;
}

.invitation-hub-file-card__meta .badge {
	font-weight: 500;
	display: inline-block;
	margin: 0.1rem;
	max-width: 100%;
	white-space: normal;
	word-break: break-word;
}
</style>
<script>
(function() {
	function escapeAttr(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

	window.renderInvitationHubFilesGrid = function(data) {
		var el = document.getElementById('modal_invitation_hub_files');
		if (!el) {
			return;
		}
		var hubFiles = data.hub_files;
		if (!hubFiles || !hubFiles.length) {
			el.innerHTML =
				'<p class="text-muted small mb-0">{{ __("admin.no-data-available") }}</p>';
			return;
		}

		var FT_IMAGE = 1;
		var FT_VIDEO = 2;
		var FT_AUDIO = 3;
		var FT_GIF = 4;

		var html = '<div class="invitation-hub-files-grid">';
		hubFiles.forEach(function(f) {
			var url = f.url || '';
			var safeUrl = escapeAttr(url);
			var typeLabel = escapeAttr(f.type_label || '');
			var upLabel = escapeAttr(f.uploader_label || '');
			var uploader = f.uploader || 'unknown';
			var badgeClass = uploader === 'admin' ?
				'bg-danger' : (uploader === 'user' ?
					'bg-info' : 'bg-secondary');

			var preview = '';
			var ft = parseInt(f.file_type, 10);
			if (ft === FT_IMAGE || ft === FT_GIF) {
				preview = '<a href="' + safeUrl +
					'" target="_blank" rel="noopener">' +
					'<img src="' + safeUrl +
					'" alt="" loading="lazy" />' +
					'</a>';
			} else if (ft === FT_VIDEO) {
				preview = '<video src="' + safeUrl +
					'" controls playsinline preload="metadata" muted></video>';
			} else if (ft === FT_AUDIO) {
				preview = '<audio src="' + safeUrl +
					'" controls preload="metadata"></audio>';
			} else {
				preview = '<a href="' + safeUrl +
					'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">{{ __("admin.hub-file-open") }}</a>';
			}

			html += '<div class="invitation-hub-file-card">' +
				'<div class="invitation-hub-file-card__preview">' +
				preview + '</div>' +
				'<div class="invitation-hub-file-card__meta">' +
				'<span class="badge bg-dark">' +
				typeLabel + '</span><br />' +
				'<span class="badge ' + badgeClass +
				'">' + upLabel + '</span>' +
				'</div></div>';
		});
		html += '</div>';
		el.innerHTML = html;
	};
})();
</script>