<script>
(function () {
	const form = document.getElementById('invitationBuilderForm');
	const iframe = document.getElementById('ibPreviewFrame');
	const loading = document.getElementById('ibPreviewLoading');
	const deviceWrap = document.getElementById('ibPreviewDevice');
	const previewUrl = @json($previewPostUrl);
	const csrf = @json(csrf_token());
	const blockCatalog = @json($catalog['information_blocks']);
	let debounceTimer = null;
	let previewSeq = 0;

	function setLoading(show) {
		loading.classList.toggle('d-none', !show);
	}

	function refreshPreview() {
		const seq = ++previewSeq;
		setLoading(true);
		const body = new FormData(form);
		body.delete('_method');
		body.delete('publish');
		body.delete('text_color_visible');

		fetch(previewUrl, {
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
				iframe.srcdoc = html;
			})
			.catch(function () {
				if (seq !== previewSeq) return;
				iframe.srcdoc = '<body style="font-family:sans-serif;padding:24px;color:#c00;text-align:center;">' + @json(__('admin.invitation-builder-preview-error')) + '</body>';
			})
			.finally(function () {
				if (seq === previewSeq) setLoading(false);
			});
	}

	function schedulePreview() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(refreshPreview, 450);
	}

	form.querySelectorAll('.ib-preview-field').forEach(function (el) {
		el.addEventListener('input', schedulePreview);
		el.addEventListener('change', schedulePreview);
	});

	document.getElementById('ibPreviewRefresh').addEventListener('click', function () {
		clearTimeout(debounceTimer);
		refreshPreview();
	});

	document.querySelectorAll('[data-ib-device]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			document.querySelectorAll('[data-ib-device]').forEach(function (b) { b.classList.remove('active'); });
			btn.classList.add('active');
			const mode = btn.getAttribute('data-ib-device');
			deviceWrap.classList.toggle('is-mobile', mode === 'mobile');
			deviceWrap.classList.toggle('is-desktop', mode === 'desktop');
		});
	});

	/* Theme picker */
	document.querySelectorAll('.ib-theme-card').forEach(function (card) {
		card.addEventListener('click', function () {
			document.querySelectorAll('.ib-theme-card').forEach(function (c) { c.classList.remove('is-active'); });
			card.classList.add('is-active');
			document.getElementById('theme_slug').value = card.dataset.slug;
			document.getElementById('primary_color').value = card.dataset.primary;
			document.getElementById('secondary_color').value = card.dataset.secondary;
			document.getElementById('background_color').value = card.dataset.bg;
			document.getElementById('text_color').value = card.dataset.text;
			const sync = document.querySelector('.ib-color-sync[data-target="text_color"]');
			if (sync) sync.value = card.dataset.text;
			schedulePreview();
		});
	});

	const catFilter = document.getElementById('ibThemeCategoryFilter');
	if (catFilter) {
		catFilter.addEventListener('change', function () {
			const val = catFilter.value;
			document.querySelectorAll('.ib-theme-card-wrap').forEach(function (wrap) {
				wrap.style.display = !val || wrap.dataset.category === val ? '' : 'none';
			});
		});
	}

	/* Envelope swatches */
	document.querySelectorAll('.ib-envelope-swatch').forEach(function (sw) {
		sw.addEventListener('click', function () {
			document.querySelectorAll('.ib-envelope-swatch').forEach(function (s) { s.classList.remove('is-active'); });
			sw.classList.add('is-active');
			sw.querySelector('input').checked = true;
			schedulePreview();
		});
	});

	document.querySelectorAll('.ib-seal-option').forEach(function (opt) {
		opt.addEventListener('click', function () {
			document.querySelectorAll('.ib-seal-option').forEach(function (o) { o.classList.remove('border-primary'); });
			opt.classList.add('border-primary');
			opt.querySelector('input').checked = true;
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

	refreshPreview();
})();
</script>
