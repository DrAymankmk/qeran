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
	const blockSchemas = JSON.parse(document.getElementById('ibBlockSchemasJson')?.textContent || '{}');
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

	form.addEventListener('input', function (e) {
		if (e.target.classList.contains('ib-preview-field')) {
			schedulePreview();
		}
	});
	form.addEventListener('change', function (e) {
		if (e.target.classList.contains('ib-preview-field')) {
			schedulePreview();
		}
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

		const standaloneInput = document.createElement('input');
		standaloneInput.type = 'hidden';
		standaloneInput.name = 'preview_standalone';
		standaloneInput.value = '1';
		tempForm.appendChild(standaloneInput);

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

	function syncBlocksOrderInputs() {
		if (!sortable) return;
		sortable.querySelectorAll('.ib-block-item').forEach(function (item) {
			var hidden = item.querySelector('input[name="blocks[]"]');
			if (hidden) {
				hidden.value = item.getAttribute('data-block') || hidden.value;
			}
		});
	}

	function reindexRepeaterRows(repeaterEl) {
		var blockKey = repeaterEl.getAttribute('data-block');
		var repeaterKey = repeaterEl.getAttribute('data-repeater');
		repeaterEl.querySelectorAll('.ib-repeater-row').forEach(function (row, index) {
			row.querySelectorAll('[name]').forEach(function (input) {
				var name = input.getAttribute('name');
				if (!name) return;
				input.setAttribute('name', name.replace(
					/block_data\[[^\]]+\]\[[^\]]+\]\[\d+\]/,
					'block_data[' + blockKey + '][' + repeaterKey + '][' + index + ']'
				));
			});
			var label = row.querySelector('.small.text-muted');
			if (label) label.textContent = '#' + (index + 1);
		});
	}

	function ibBlockFieldColumnClass(fieldType) {
		if (fieldType === 'textarea') return 'col-12';
		if (fieldType === 'checkbox') return 'col-md-6';
		if (fieldType === 'color' || fieldType === 'optional_color') return 'col-md-4';
		if (fieldType === 'font' || fieldType === 'font_weight') return 'col-md-6';
		if (fieldType === 'font_size') return 'col-md-4';
		if (fieldType === 'date' || fieldType === 'time' || fieldType === 'datetime-local') return 'col-md-4';
		return 'col-md-6';
	}

	function bindOptionalColorControls(root) {
		root = root || document;
		root.querySelectorAll('.ib-optional-color-picker').forEach(function (picker) {
			if (picker.dataset.bound === '1') return;
			picker.dataset.bound = '1';
			picker.addEventListener('input', function () {
				var target = document.getElementById(picker.getAttribute('data-target'));
				if (target) {
					target.value = picker.value;
					target.dispatchEvent(new Event('input', { bubbles: true }));
				}
			});
		});
		root.querySelectorAll('.ib-optional-color-text.ib-preview-field').forEach(function (input) {
			if (input.dataset.bound === '1') return;
			input.dataset.bound = '1';
			input.addEventListener('input', function () {
				var picker = input.parentElement && input.parentElement.querySelector('.ib-optional-color-picker');
				if (picker && /^#[0-9A-Fa-f]{6}$/.test(input.value)) {
					picker.value = input.value;
				}
			});
		});
		root.querySelectorAll('.ib-optional-color-clear').forEach(function (btn) {
			if (btn.dataset.bound === '1') return;
			btn.dataset.bound = '1';
			btn.addEventListener('click', function () {
				var target = document.getElementById(btn.getAttribute('data-target'));
				if (target) {
					target.value = '';
					target.dispatchEvent(new Event('input', { bubbles: true }));
				}
				var picker = btn.parentElement && btn.parentElement.querySelector('.ib-optional-color-picker');
				if (picker) picker.value = '#faf7f2';
			});
		});
	}

	function ibBlockFieldInputHtml(fieldType, name, label, fieldDef) {
		fieldDef = fieldDef || {};
		var inputId = 'ib_bf_dyn_' + Math.random().toString(36).slice(2, 9);
		var maxlength = fieldDef.max || 500;
		var placeholder = fieldDef.placeholder || '';

		if (fieldType === 'checkbox') {
			return '<div class="form-check mt-1"><input type="hidden" name="' + name + '" value="0">'
				+ '<input type="checkbox" class="form-check-input ib-preview-field" name="' + name + '" value="1" id="' + inputId + '">'
				+ '<label class="form-check-label small" for="' + inputId + '">' + label + '</label></div>';
		}

		var html = '<label class="form-label small mb-1" for="' + inputId + '">' + label + '</label>';
		if (fieldType === 'textarea') {
			return html + '<textarea name="' + name + '" id="' + inputId + '" rows="' + (fieldDef.rows || 2)
				+ '" class="form-control form-control-sm ib-preview-field" maxlength="' + maxlength
				+ '" placeholder="' + placeholder + '"></textarea>';
		}
		if (fieldType === 'color') {
			return html + '<input type="color" name="' + name + '" id="' + inputId
				+ '" class="form-control form-control-color w-100 ib-preview-field" value="#c9a962">';
		}
		if (fieldType === 'number') {
			return html + '<input type="number" name="' + name + '" id="' + inputId
				+ '" class="form-control form-control-sm ib-preview-field" placeholder="' + placeholder + '">';
		}
		if (fieldType === 'date' || fieldType === 'time' || fieldType === 'datetime-local') {
			return html + '<input type="' + fieldType + '" name="' + name + '" id="' + inputId
				+ '" class="form-control form-control-sm ib-preview-field" placeholder="' + placeholder + '">';
		}
		var htmlType = ({ url: 'url', email: 'email', tel: 'tel' })[fieldType] || 'text';
		return html + '<input type="' + htmlType + '" name="' + name + '" id="' + inputId
			+ '" class="form-control form-control-sm ib-preview-field" maxlength="' + maxlength
			+ '" placeholder="' + placeholder + '">';
	}

	function buildRepeaterRowHtml(blockKey, repeaterKey, rowIndex, fields) {
		var html = '<div class="ib-repeater-row card card-body p-2 mb-2 bg-light">';
		html += '<div class="d-flex justify-content-between align-items-center mb-2">';
		html += '<span class="small text-muted">#' + (rowIndex + 1) + '</span>';
		html += '<button type="button" class="btn btn-sm btn-outline-danger py-0 ib-repeater-remove">×</button></div><div class="row g-2">';
		Object.keys(fields).forEach(function (rfKey) {
			var rf = fields[rfKey];
			var rfType = rf.type || 'text';
			var rfName = 'block_data[' + blockKey + '][' + repeaterKey + '][' + rowIndex + '][' + rfKey + ']';
			var col = ibBlockFieldColumnClass(rfType);
			html += '<div class="' + col + ' ib-block-field ib-block-field-' + rfType + '">';
			html += ibBlockFieldInputHtml(rfType, rfName, rf.label_ar || rfKey, rf);
			html += '</div>';
		});
		html += '</div></div>';
		return html;
	}

	function bindRepeater(container) {
		if (!container || container.dataset.ibRepeaterBound === '1') return;
		container.dataset.ibRepeaterBound = '1';
		container.querySelectorAll('.ib-repeater-add').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var wrap = btn.closest('.ib-block-repeater');
				if (!wrap) return;
				var max = parseInt(wrap.getAttribute('data-max') || '8', 10);
				var rows = wrap.querySelector('.ib-repeater-rows');
				if (!rows || rows.children.length >= max) return;
				var blockKey = wrap.getAttribute('data-block');
				var repeaterKey = wrap.getAttribute('data-repeater');
				var fields = (blockSchemas[blockKey] && blockSchemas[blockKey].repeaters && blockSchemas[blockKey].repeaters[repeaterKey])
					? blockSchemas[blockKey].repeaters[repeaterKey].fields : {};
				rows.insertAdjacentHTML('beforeend', buildRepeaterRowHtml(blockKey, repeaterKey, rows.children.length, fields));
				bindRepeaterRow(rows.lastElementChild);
				schedulePreview();
			});
		});
		container.querySelectorAll('.ib-repeater-row').forEach(bindRepeaterRow);
	}

	function bindRepeaterRow(row) {
		if (!row || row.dataset.ibRowBound === '1') return;
		row.dataset.ibRowBound = '1';
		var removeBtn = row.querySelector('.ib-repeater-remove');
		if (removeBtn) {
			removeBtn.addEventListener('click', function () {
				var repeater = row.closest('.ib-block-repeater');
				row.remove();
				if (repeater) {
					reindexRepeaterRows(repeater);
					schedulePreview();
				}
			});
		}
	}

	document.querySelectorAll('.ib-block-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.ib-block-item')?.querySelector('.ib-block-fields-wrap');
			if (wrap) wrap.classList.toggle('show');
		});
	});

	document.querySelectorAll('#ibBlocksSortable .ib-block-fields').forEach(function (wrap) {
		bindRepeater(wrap);
	});
	bindOptionalColorControls(document);

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
			syncBlocksOrderInputs();
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
			li.className = 'list-group-item ib-block-item';
			li.draggable = true;
			li.dataset.block = key;
			var hasSchema = !!blockSchemas[key];
			var rowHtml = '<div class="d-flex align-items-center gap-2">';
			rowHtml += '<span class="ib-drag-handle text-muted cursor-grab">⋮⋮</span><span class="fs-5">' + btn.dataset.icon + '</span>';
			rowHtml += '<div class="flex-grow-1"><strong class="small d-block">' + btn.dataset.label + '</strong>';
			if (btn.dataset.desc) rowHtml += '<span class="text-muted" style="font-size:11px;">' + btn.dataset.desc + '</span>';
			rowHtml += '</div><input type="hidden" name="blocks[]" value="' + key + '" class="ib-preview-field">';
			if (hasSchema) {
				rowHtml += '<button type="button" class="btn btn-sm btn-outline-secondary ib-block-toggle"><i class="mdi mdi-pencil-outline"></i></button>';
			}
			rowHtml += '<button type="button" class="btn btn-sm btn-outline-danger ib-block-remove">×</button></div>';
			li.innerHTML = rowHtml;
			if (hasSchema) {
				var tpl = document.getElementById('ibBlockFieldsTpl_' + key);
				if (tpl && tpl.content) {
					li.appendChild(tpl.content.cloneNode(true));
				}
			}
			sortable.appendChild(li);
			btn.closest('.col-md-6').remove();
			var toggleBtn = li.querySelector('.ib-block-toggle');
			if (toggleBtn) {
				toggleBtn.addEventListener('click', function () {
					var wrap = li.querySelector('.ib-block-fields-wrap');
					if (wrap) wrap.classList.toggle('show');
				});
			}
			li.querySelector('.ib-block-remove').addEventListener('click', function () {
				const item = li;
				const blockKey = item.dataset.block;
				const meta = blockCatalog[blockKey];
				if (meta) {
					const col = document.createElement('div');
					col.className = 'col-md-6';
					col.innerHTML = '<button type="button" class="btn btn-outline-secondary w-100 text-start ib-block-add" data-block="' + blockKey + '" data-icon="' + meta.icon + '" data-label="' + meta.label_ar + '" data-desc="">' + meta.icon + ' ' + meta.label_ar + '</button>';
					document.getElementById('ibBlocksAvailable').appendChild(col);
					bindBlockAdd(col.querySelector('.ib-block-add'));
				}
				item.remove();
				schedulePreview();
			});
			var fieldsWrap = li.querySelector('.ib-block-fields-wrap');
			if (fieldsWrap) {
				fieldsWrap.classList.add('show');
				bindRepeater(fieldsWrap);
				bindOptionalColorControls(fieldsWrap);
			}
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
