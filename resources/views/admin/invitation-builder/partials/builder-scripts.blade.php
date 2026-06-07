<script>
(function () {
	const form = document.getElementById('invitationBuilderForm');
	const iframe = document.getElementById('ibPreviewFrame');
	const loading = document.getElementById('ibPreviewLoading');
	const deviceWrap = document.getElementById('ibPreviewDevice');
	const previewPostUrl = @json($previewPostUrl);
	const themeUploadUrl = @json(route('admin.invitation-builder.themes.store'));
	const themeDeleteUrlTemplate = @json(route('admin.invitation-builder.themes.destroy', ['theme' => '__SLUG__']));
	const csrf = @json(csrf_token());
	const blockCatalog = @json($catalog['information_blocks']);
	let debounceTimer = null;
	let previewSeq = 0;
	let previewReady = false;

	function setLoading(show) {
		loading.classList.toggle('d-none', !show);
	}

	function getPreviewDoc() {
		try {
			return iframe.contentDocument || iframe.contentWindow.document;
		} catch (e) {
			return null;
		}
	}

	function getPreviewWin() {
		try {
			return iframe.contentWindow;
		} catch (e) {
			return null;
		}
	}

	function isStyleRelevant(text) {
		return /(--ib-|--wi-|\.wi-|\.ib-builder|invitation-builder-active|wi-env-seal|wi-envelope)/.test(text || '');
	}

	function applyPreviewStyles(targetDoc, parsedDoc) {
		targetDoc.querySelectorAll('[data-ib-preview-style]').forEach(function (node) {
			node.remove();
		});
		parsedDoc.querySelectorAll('style').forEach(function (style) {
			if (!isStyleRelevant(style.textContent)) return;
			var el = targetDoc.createElement('style');
			el.setAttribute('data-ib-preview-style', '1');
			el.textContent = style.textContent;
			targetDoc.head.appendChild(el);
		});
		if (parsedDoc.body) {
			targetDoc.body.className = parsedDoc.body.className;
		}
	}

	function replaceNodeFromParsed(targetDoc, selector, parsedDoc) {
		var oldNode = targetDoc.querySelector(selector);
		var newNode = parsedDoc.querySelector(selector);
		if (oldNode && newNode) {
			oldNode.replaceWith(targetDoc.importNode(newNode, true));
			return true;
		}
		if (!oldNode && newNode) {
			var shell = targetDoc.querySelector('.ib-preview-shell');
			var main = targetDoc.getElementById('wiMainContent');
			var imported = targetDoc.importNode(newNode, true);
			if (selector === '#wiEnvelopeGate' && main) {
				main.parentNode.insertBefore(imported, main);
			} else if (shell) {
				shell.appendChild(imported);
			} else {
				targetDoc.body.appendChild(imported);
			}
			return true;
		}
		if (oldNode && !newNode) {
			oldNode.remove();
		}
		return false;
	}

	function softPatchPreview(html) {
		var doc = getPreviewDoc();
		var win = getPreviewWin();
		if (!doc || !doc.getElementById('wiMainContent') || !win) {
			return false;
		}

		deviceWrap.classList.add('is-live-updating');

		var state = win.ibCapturePreviewState ? win.ibCapturePreviewState() : null;
		var parsed = new DOMParser().parseFromString(html, 'text/html');

		applyPreviewStyles(doc, parsed);
		replaceNodeFromParsed(doc, '#wiEnvelopeGate', parsed);
		replaceNodeFromParsed(doc, '#wiMainContent', parsed);
		replaceNodeFromParsed(doc, '#wiStatusAccepted', parsed);
		replaceNodeFromParsed(doc, '#wiStatusDeclined', parsed);

		var newAudio = parsed.getElementById('inviteOpeningAudio');
		var oldAudio = doc.getElementById('inviteOpeningAudio');
		if (oldAudio && !newAudio) {
			oldAudio.remove();
		} else if (newAudio) {
			if (oldAudio) oldAudio.replaceWith(doc.importNode(newAudio, true));
			else doc.body.appendChild(doc.importNode(newAudio, true));
		}

		if (win.ibRestorePreviewState) win.ibRestorePreviewState(state);
		if (win.ibAfterPreviewPatch) win.ibAfterPreviewPatch();

		requestAnimationFrame(function () {
			deviceWrap.classList.remove('is-live-updating');
		});

		return true;
	}

	function fullLoadPreview(html, seq) {
		previewReady = false;
		iframe.srcdoc = html;
		iframe.onload = function () {
			if (seq !== previewSeq) return;
			previewReady = true;
			var win = getPreviewWin();
			if (win && win.ibAfterPreviewPatch) win.ibAfterPreviewPatch();
			setLoading(false);
		};
	}

	function refreshPreview(forceFull) {
		const seq = ++previewSeq;
		const soft = previewReady && !forceFull;
		if (!soft) setLoading(true);

		const body = buildPreviewFormData();

		fetch(previewPostUrl, {
			method: 'POST',
			body: body,
			headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'text/html' },
			credentials: 'same-origin',
		})
			.then(function (res) {
				if (!res.ok) throw new Error('Preview failed');
				return res.text();
			})
			.then(function (html) {
				if (seq !== previewSeq) return;
				if (!soft || !softPatchPreview(html)) {
					fullLoadPreview(html, seq);
					return;
				}
				setLoading(false);
			})
			.catch(function () {
				if (seq !== previewSeq) return;
				previewReady = false;
				iframe.srcdoc = '<body style="font-family:sans-serif;padding:24px;color:#c00;text-align:center;">' + @json(__('admin.invitation-builder-preview-error')) + '</body>';
				setLoading(false);
			});
	}

	function schedulePreview() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function () { refreshPreview(false); }, 280);
	}

	form.querySelectorAll('.ib-preview-field').forEach(function (el) {
		el.addEventListener('input', schedulePreview);
		el.addEventListener('change', schedulePreview);
	});

	document.getElementById('ibPreviewRefresh').addEventListener('click', function () {
		clearTimeout(debounceTimer);
		refreshPreview(true);
	});

	function buildPreviewFormData() {
		const body = new FormData(form);
		body.delete('_method');
		body.delete('publish');
		body.delete('text_color_visible');
		return body;
	}

	function openPreviewInNewTab() {
		const body = buildPreviewFormData();
		const tempForm = document.createElement('form');
		tempForm.method = 'POST';
		tempForm.action = previewPostUrl;
		tempForm.target = '_blank';
		tempForm.rel = 'noopener';
		tempForm.style.display = 'none';

		const tokenInput = document.createElement('input');
		tokenInput.type = 'hidden';
		tokenInput.name = '_token';
		tokenInput.value = csrf;
		tempForm.appendChild(tokenInput);

		body.forEach(function (value, key) {
			const input = document.createElement('input');
			input.type = 'hidden';
			input.name = key;
			input.value = typeof value === 'string' ? value : '';
			tempForm.appendChild(input);
		});

		document.body.appendChild(tempForm);
		tempForm.submit();
		tempForm.remove();
	}

	var openTabBtn = document.getElementById('ibOpenPreviewTab');
	if (openTabBtn) {
		openTabBtn.addEventListener('click', openPreviewInNewTab);
	}

	document.querySelectorAll('[data-ib-device]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			document.querySelectorAll('[data-ib-device]').forEach(function (b) { b.classList.remove('active'); });
			btn.classList.add('active');
			const mode = btn.getAttribute('data-ib-device');
			deviceWrap.classList.toggle('is-mobile', mode === 'mobile');
			deviceWrap.classList.toggle('is-desktop', mode === 'desktop');
		});
	});

	function syncThemeMedia(url, mediaType) {
		var hiddenUrl = document.getElementById('background_media_url');
		var hiddenVideo = document.getElementById('video_background_hidden');
		var customUrl = document.getElementById('background_media_url_custom');
		var videoCb = document.getElementById('video_background');
		var isVideo = mediaType === 'video';
		if (hiddenUrl) hiddenUrl.value = url || '';
		if (hiddenVideo) hiddenVideo.value = isVideo ? '1' : '0';
		if (customUrl) customUrl.value = url || '';
		if (videoCb) videoCb.checked = isVideo;
	}

	function updateThemePreviewVideos() {
		document.querySelectorAll('.ib-theme-preview-vid').forEach(function (vid) {
			var card = vid.closest('.ib-theme-card');
			if (card && card.classList.contains('is-active')) {
				vid.play().catch(function () {});
			} else {
				vid.pause();
				try { vid.currentTime = 0; } catch (e) {}
			}
		});
	}

	function activateThemeCard(card) {
		document.querySelectorAll('.ib-theme-card').forEach(function (c) { c.classList.remove('is-active'); });
		card.classList.add('is-active');
		document.getElementById('theme_slug').value = card.dataset.slug;
		document.getElementById('primary_color').value = card.dataset.primary;
		document.getElementById('secondary_color').value = card.dataset.secondary;
		document.getElementById('background_color').value = card.dataset.bg;
		document.getElementById('text_color').value = card.dataset.text;
		var sync = document.querySelector('.ib-color-sync[data-target="text_color"]');
		if (sync) sync.value = card.dataset.text;
		var mediaUrl = card.dataset.mediaUrl || card.dataset.video || '';
		var mediaType = card.dataset.mediaType || (card.dataset.video ? 'video' : 'image');
		if (mediaUrl) {
			syncThemeMedia(mediaUrl, mediaType);
		}
		updateThemePreviewVideos();
		schedulePreview();
	}

	function bindThemeCards() {
		document.querySelectorAll('.ib-theme-card').forEach(function (card) {
			if (card.dataset.bound === '1') {
				return;
			}
			card.dataset.bound = '1';
			card.addEventListener('click', function () {
				activateThemeCard(card);
			});
		});
	}

	bindThemeCards();
	updateThemePreviewVideos();

	var themeUploadBtn = document.getElementById('ibThemeUploadBtn');
	if (themeUploadBtn) {
		themeUploadBtn.addEventListener('click', function () {
			var nameInput = document.getElementById('ibThemeNameAr');
			var typeInput = document.getElementById('ibThemeMediaType');
			var fileInput = document.getElementById('ibThemeMediaInput');
			var statusEl = document.getElementById('ibThemeUploadStatus');
			var file = fileInput && fileInput.files ? fileInput.files[0] : null;

			if (!nameInput || !nameInput.value.trim()) {
				if (statusEl) statusEl.textContent = @json(__('admin.ib-theme-upload-name-required'));
				return;
			}
			if (!file) {
				if (statusEl) statusEl.textContent = @json(__('admin.ib-theme-upload-file-required'));
				return;
			}

			var formData = new FormData();
			formData.append('name_ar', nameInput.value.trim());
			formData.append('media_type', typeInput ? typeInput.value : 'video');
			formData.append('media', file);
			formData.append('_token', csrf);

			themeUploadBtn.disabled = true;
			if (statusEl) statusEl.textContent = @json(__('admin.ib-theme-uploading'));

			fetch(themeUploadUrl, {
				method: 'POST',
				body: formData,
				headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
			})
				.then(function (response) {
					return response.json().then(function (data) {
						if (!response.ok) {
							throw data;
						}
						return data;
					});
				})
				.then(function () {
					window.location.hash = 'ibTabThemes';
					window.location.reload();
				})
				.catch(function (error) {
					var message = @json(__('admin.ib-theme-upload-failed'));
					if (error && error.errors) {
						var firstKey = Object.keys(error.errors)[0];
						if (firstKey && error.errors[firstKey][0]) {
							message = error.errors[firstKey][0];
						}
					} else if (error && error.message) {
						message = error.message;
					}
					if (statusEl) statusEl.textContent = message;
				})
				.finally(function () {
					themeUploadBtn.disabled = false;
				});
		});
	}

	document.querySelectorAll('.ib-theme-delete-btn').forEach(function (btn) {
		btn.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			var themeSlug = btn.dataset.themeSlug;
			if (!themeSlug || !window.confirm(@json(__('admin.ib-theme-delete-confirm')))) {
				return;
			}

			var deleteUrl = themeDeleteUrlTemplate.replace('__SLUG__', encodeURIComponent(themeSlug));
			btn.disabled = true;

			fetch(deleteUrl, {
				method: 'DELETE',
				headers: {
					'Accept': 'application/json',
					'X-CSRF-TOKEN': csrf,
					'X-Requested-With': 'XMLHttpRequest',
				},
			})
				.then(function (response) {
					return response.json().then(function (data) {
						if (!response.ok) {
							throw data;
						}
						return data;
					});
				})
				.then(function () {
					window.location.hash = 'ibTabThemes';
					window.location.reload();
				})
				.catch(function () {
					btn.disabled = false;
					window.alert(@json(__('admin.ib-theme-delete-failed')));
				});
		});
	});

	var customBg = document.getElementById('background_media_url_custom');
	var videoBgCb = document.getElementById('video_background');
	if (customBg) {
		customBg.addEventListener('input', function () {
			syncThemeMedia(customBg.value, videoBgCb && videoBgCb.checked ? 'video' : 'image');
			schedulePreview();
		});
	}
	if (videoBgCb) {
		videoBgCb.addEventListener('change', function () {
			syncThemeMedia(customBg ? customBg.value : '', videoBgCb.checked ? 'video' : 'image');
			schedulePreview();
		});
	}

	document.querySelectorAll('.ib-envelope-shape-card').forEach(function (card) {
		card.addEventListener('click', function () {
			document.querySelectorAll('.ib-envelope-shape-card').forEach(function (c) { c.classList.remove('is-active'); });
			card.classList.add('is-active');
			var radio = card.querySelector('input[type="radio"]');
			if (radio) radio.checked = true;
			schedulePreview();
		});
	});

	var envRefInput = document.getElementById('envelope_image_ref');
	document.querySelectorAll('.ib-env-image-card').forEach(function (card) {
		card.addEventListener('click', function () {
			document.querySelectorAll('.ib-env-image-card').forEach(function (c) { c.classList.remove('is-active'); });
			card.classList.add('is-active');
			if (envRefInput) {
				envRefInput.value = card.getAttribute('data-envelope-ref') || '';
			}
			schedulePreview();
		});
	});

	/* Envelope swatches */
	document.querySelectorAll('.ib-envelope-swatch').forEach(function (sw) {
		sw.addEventListener('click', function () {
			document.querySelectorAll('.ib-envelope-swatch').forEach(function (s) { s.classList.remove('is-active'); });
			sw.classList.add('is-active');
			sw.querySelector('input').checked = true;
			schedulePreview();
		});
	});

	function syncSealColor(hex) {
		var hidden = document.getElementById('seal_color');
		var picker = document.getElementById('seal_color_picker');
		if (!hex) return;
		if (hidden) hidden.value = hex;
		if (picker) picker.value = hex;
		document.querySelectorAll('.ib-seal-color-swatch').forEach(function (sw) {
			sw.classList.toggle('is-active', (sw.getAttribute('data-seal-color') || '').toLowerCase() === hex.toLowerCase());
		});
	}

	function applySealColorToMiniPreviews(hex) {
		if (!hex) return;
		var style = '--s-mid: ' + hex + '; --s-lo: color-mix(in srgb, ' + hex + ' 58%, #000); --s-hi: color-mix(in srgb, ' + hex + ' 38%, #fff); --s-drip: color-mix(in srgb, ' + hex + ' 72%, #000); --s-ink: color-mix(in srgb, ' + hex + ' 18%, #fff);';
		var active = document.querySelector('.ib-seal-option.border-primary .wi-env-seal.ib-seal-mini');
		if (active) active.setAttribute('style', style);
	}

	document.querySelectorAll('.ib-seal-option').forEach(function (opt) {
		opt.addEventListener('click', function () {
			document.querySelectorAll('.ib-seal-option').forEach(function (o) { o.classList.remove('border-primary'); });
			opt.classList.add('border-primary');
			opt.querySelector('input').checked = true;
			var defaultColor = opt.getAttribute('data-seal-color');
			if (defaultColor) {
				syncSealColor(defaultColor);
			}
			schedulePreview();
		});
	});

	var sealColorPicker = document.getElementById('seal_color_picker');
	if (sealColorPicker) {
		sealColorPicker.addEventListener('input', function () {
			syncSealColor(sealColorPicker.value);
			applySealColorToMiniPreviews(sealColorPicker.value);
			schedulePreview();
		});
	}

	document.querySelectorAll('.ib-seal-color-swatch').forEach(function (sw) {
		sw.addEventListener('click', function () {
			var hex = sw.getAttribute('data-seal-color');
			syncSealColor(hex);
			applySealColorToMiniPreviews(hex);
			schedulePreview();
		});
	});

	/* Text color sync */
	document.querySelectorAll('.ib-color-sync').forEach(function (picker) {
		picker.addEventListener('input', function () {
			const target = document.getElementById(picker.dataset.target);
			if (target) target.value = picker.value;
			schedulePreview();
		});
	});

	/* Blocks drag & drop */
	const sortable = document.getElementById('ibBlocksSortable');
	let dragged = null;

	if (sortable) {
		sortable.addEventListener('dragstart', function (e) {
			const item = e.target.closest('.ib-block-item');
			if (!item) return;
			dragged = item;
			item.classList.add('dragging');
		});
		sortable.addEventListener('dragend', function () {
			if (dragged) dragged.classList.remove('dragging');
			dragged = null;
			schedulePreview();
		});
		sortable.addEventListener('dragover', function (e) {
			e.preventDefault();
			const after = getDragAfterElement(sortable, e.clientY);
			if (!dragged) return;
			if (after == null) {
				sortable.appendChild(dragged);
			} else {
				sortable.insertBefore(dragged, after);
			}
		});
	}

	function getDragAfterElement(container, y) {
		const elements = [...container.querySelectorAll('.ib-block-item:not(.dragging)')];
		return elements.reduce(function (closest, child) {
			const box = child.getBoundingClientRect();
			const offset = y - box.top - box.height / 2;
			if (offset < 0 && offset > closest.offset) {
				return { offset: offset, element: child };
			}
			return closest;
		}, { offset: Number.NEGATIVE_INFINITY }).element;
	}

	document.querySelectorAll('.ib-block-remove').forEach(function (btn) {
		btn.addEventListener('click', function () {
			const item = btn.closest('.ib-block-item');
			const key = item.dataset.block;
			const meta = blockCatalog[key];
			if (meta) {
				const col = document.createElement('div');
				col.className = 'col-md-6';
				col.innerHTML = '<button type="button" class="btn btn-outline-secondary w-100 text-start ib-block-add" data-block="' + key + '" data-icon="' + meta.icon + '" data-label="' + meta.label_ar + '" data-desc="">' + meta.icon + ' ' + meta.label_ar + '</button>';
				document.getElementById('ibBlocksAvailable').appendChild(col);
				bindBlockAdd(col.querySelector('.ib-block-add'));
			}
			item.remove();
			schedulePreview();
		});
	});

	function bindBlockAdd(btn) {
		if (!btn) return;
		btn.addEventListener('click', function () {
			const key = btn.dataset.block;
			const li = document.createElement('li');
			li.className = 'list-group-item ib-block-item d-flex align-items-center gap-2';
			li.draggable = true;
			li.dataset.block = key;
			li.innerHTML = '<span class="ib-drag-handle text-muted cursor-grab">⋮⋮</span><span class="fs-5">' + btn.dataset.icon + '</span><div class="flex-grow-1"><strong class="small d-block">' + btn.dataset.label + '</strong></div><input type="hidden" name="blocks[]" value="' + key + '" class="ib-preview-field"><button type="button" class="btn btn-sm btn-outline-danger ib-block-remove">×</button>';
			sortable.appendChild(li);
			btn.closest('.col-md-6').remove();
			li.querySelector('.ib-block-remove').addEventListener('click', function () {
				li.remove();
				schedulePreview();
			});
			schedulePreview();
		});
	}

	document.querySelectorAll('.ib-block-add').forEach(bindBlockAdd);

	if (window.location.hash === '#ibTabThemes') {
		var themesTab = document.querySelector('[data-bs-target="#ibTabThemes"]');
		if (themesTab && window.bootstrap && bootstrap.Tab) {
			bootstrap.Tab.getOrCreateInstance(themesTab).show();
		}
	}

	refreshPreview(true);
})();
</script>
