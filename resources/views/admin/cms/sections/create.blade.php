@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">Create Section - {{ $page->name }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">Dashboard</a>
					</li>
					<li class="breadcrumb-item"><a href="{{route('cms.pages.index')}}">CMS
							Pages</a></li>
					<li class="breadcrumb-item"><a
							href="{{route('cms.sections.index', $page)}}">Sections</a>
					</li>
					<li class="breadcrumb-item active">Create</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="{{route('cms.sections.store', $page)}}" method="POST"
					enctype="multipart/form-data">
					@csrf

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Internal Name
									<span
										class="text-danger">*</span></label>
								<input type="text" name="name"
									class="form-control @error('name') is-invalid @enderror"
									value="{{old('name')}}"
									required>
								@error('name')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
								<small class="form-text text-muted">Visible
									in admin only</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Type <span
										class="text-danger">*</span></label>
								<input type="text" name="type"
									class="form-control @error('type') is-invalid @enderror"
									value="{{old('type', 'default')}}"
									required>
								@error('type')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
								<small class="form-text text-muted">e.g.,
									default, hero, gallery,
									testimonial</small>
							</div>
						</div>
						<!-- <div class="col-md-6">
							<div class="mb-3">
								<label
									class="form-label">Template</label>
								<input type="text" name="template"
									class="form-control"
									value="{{old('template')}}">
								<small class="form-text text-muted">Optional
									Blade template name</small>
							</div>
						</div> -->
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Order</label>
								<input type="number" name="order"
									class="form-control"
									value="{{old('order', 0)}}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Status</label>
								<select name="is_active"
									class="form-select">
									<option value="1"
										{{old('is_active', true) ? 'selected' : ''}}>
										Active</option>
									<option value="0"
										{{!old('is_active', true) ? 'selected' : ''}}>
										Inactive</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Images
									(optional)</label>
								<input type="file" name="image_files[]"
									class="form-control"
									accept="image/*" multiple>
								<small class="form-text text-muted">Upload
									multiple images (max 5MB
									each)</small>

								<div class="mt-2"
									id="image-preview-wrapper">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Video
									(optional)</label>
								<input type="file" name="video_file"
									class="form-control"
									accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska">
								<small class="form-text text-muted">Upload
									a video (max 50MB). You can
									also provide a URL
									below.</small>
								<input type="text" name="video"
									class="form-control mt-2"
									value="{{old('video')}}"
									placeholder="Or paste video URL (YouTube/Vimeo)">
								<div class="mt-2"
									id="video-preview-wrapper"
									style="display:none;">
									<video id="video-preview"
										src=""
										class="w-100 rounded"
										style="max-height:180px;"
										controls></video>
								</div>
							</div>
						</div>
					</div>

					<hr>
					<h5 class="mb-3">Content</h5>

					<!-- Language Tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="en-tab"
								data-bs-toggle="tab"
								data-bs-target="#en-content"
								type="button" role="tab"
								aria-controls="en-content"
								aria-selected="true">
								English
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="ar-tab"
								data-bs-toggle="tab"
								data-bs-target="#ar-content"
								type="button" role="tab"
								aria-controls="ar-content"
								aria-selected="false">
								Arabic
							</button>
						</li>
					</ul>

					<div class="tab-content mt-3" id="languageTabContent">
						<!-- English Tab -->
						<div class="tab-pane fade show active" id="en-content"
							role="tabpanel" aria-labelledby="en-tab">
							<div class="mb-3">
								<label class="form-label">Title
									(EN)</label>
								<input type="text" name="en[title]"
									class="form-control"
									value="{{old('en.title')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Subtitle
									(EN)</label>
								<input type="text" name="en[subtitle]"
									class="form-control"
									value="{{old('en.subtitle')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Description
									(EN)</label>
								<textarea name="en[description]"
									class="form-control summernote"
									rows="4">{{old('en.description')}}</textarea>
							</div>
						</div>

						<!-- Arabic Tab -->
						<div class="tab-pane fade" id="ar-content" role="tabpanel"
							aria-labelledby="ar-tab">
							<div class="mb-3">
								<label class="form-label">Title
									(AR)</label>
								<input type="text" name="ar[title]"
									class="form-control"
									value="{{old('ar.title')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Subtitle
									(AR)</label>
								<input type="text" name="ar[subtitle]"
									class="form-control"
									value="{{old('ar.subtitle')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Description
									(AR)</label>
								<textarea name="ar[description]"
									class="form-control summernote"
									rows="4">{{old('ar.description')}}</textarea>
							</div>
						</div>
					</div>

					<hr>
					<h5 class="mb-3">Items (Optional)</h5>
					<p class="text-muted">Add items to this section. Each item can have
						title, subtitle, and description.</p>

					<div id="items-container">
						<div class="item-row mb-3 p-3 border rounded">
							<div
								class="d-flex justify-content-between align-items-center mb-2">
								<h6 class="mb-0">Item #1</h6>
								<button type="button"
									class="btn btn-sm btn-danger remove-item">Remove
									Item</button>
							</div>

							<div class="row mb-2">
								<div class="col-md-4">
									<label
										class="form-label">Type</label>
									<input type="text"
										name="items[0][type]"
										class="form-control"
										value="default"
										placeholder="default">
								</div>
								<div class="col-md-4">
									<label
										class="form-label">Order</label>
									<input type="number"
										name="items[0][order]"
										class="form-control"
										value="0">
								</div>
								<div class="col-md-4">
									<label
										class="form-label">Status</label>
									<select name="items[0][is_active]"
										class="form-select">
										<option value="1"
											selected>
											Active
										</option>
										<option value="0">
											Inactive
										</option>
									</select>
								</div>
							</div>

							<!-- Item Language Tabs -->
							<ul class="nav nav-tabs mt-2" role="tablist">
								<li class="nav-item"
									role="presentation">
									<button class="nav-link active"
										id="item-0-en-tab"
										data-bs-toggle="tab"
										data-bs-target="#item-0-en-content"
										type="button"
										role="tab"
										aria-controls="item-0-en-content"
										aria-selected="true">
										English
									</button>
								</li>
								<li class="nav-item"
									role="presentation">
									<button class="nav-link"
										id="item-0-ar-tab"
										data-bs-toggle="tab"
										data-bs-target="#item-0-ar-content"
										type="button"
										role="tab"
										aria-controls="item-0-ar-content"
										aria-selected="false">
										Arabic
									</button>
								</li>
							</ul>

							<div class="tab-content mt-2">
								<!-- English Tab -->
								<div class="tab-pane fade show active"
									id="item-0-en-content"
									role="tabpanel"
									aria-labelledby="item-0-en-tab">
									<div class="mb-2">
										<input type="text"
											name="items[0][en][title]"
											class="form-control mb-2"
											placeholder="Title (EN)">
									</div>
									<div class="mb-2">
										<input type="text"
											name="items[0][en][sub_title]"
											class="form-control mb-2"
											placeholder="Sub Title (EN)">
									</div>
									<div class="mb-2">
										<textarea name="items[0][en][content]"
											class="form-control summernote"
											rows="2"
											placeholder="Content (EN)"></textarea>
									</div>
									<div class="mb-2">
										<label
											class="form-label">Icon
											(EN)</label>
										<div
											class="input-group">
											<input type="text"
												name="items[0][en][icon]"
												class="form-control icon-input"
												placeholder="Select an icon"
												readonly>
											<button type="button"
												class="btn btn-outline-secondary icon-picker-btn"
												data-target="items[0][en][icon]">
												<i
													class="icon icon-settings"></i>
												Choose
												Icon
											</button>
										</div>
										<div class="icon-preview mt-2"
											id="icon-preview-items[0][en][icon]"
											data-name="items[0][en][icon]">
										</div>
									</div>
								</div>

								<!-- Arabic Tab -->
								<div class="tab-pane fade"
									id="item-0-ar-content"
									role="tabpanel"
									aria-labelledby="item-0-ar-tab">
									<div class="mb-2">
										<input type="text"
											name="items[0][ar][title]"
											class="form-control mb-2"
											placeholder="Title (AR)">
									</div>
									<div class="mb-2">
										<input type="text"
											name="items[0][ar][sub_title]"
											class="form-control mb-2"
											placeholder="Sub Title (AR)">
									</div>
									<div class="mb-2">
										<textarea name="items[0][ar][content]"
											class="form-control summernote"
											rows="2"
											placeholder="Content (AR)"></textarea>
									</div>
									<div class="mb-2">
										<label
											class="form-label">Icon
											(AR)</label>
										<div
											class="input-group">
											<input type="text"
												name="items[0][ar][icon]"
												class="form-control icon-input"
												placeholder="Select an icon"
												readonly>
											<button type="button"
												class="btn btn-outline-secondary icon-picker-btn"
												data-target="items[0][ar][icon]">
												<i
													class="icon icon-settings"></i>
												Choose
												Icon
											</button>
										</div>
										<div class="icon-preview mt-2"
											id="icon-preview-items[0][ar][icon]"
											data-name="items[0][ar][icon]">
										</div>
									</div>
								</div>
							</div>

							<!-- Item Image Upload -->
							<div class="mb-2 mt-3">
								<label class="form-label">Item Images
									(optional)</label>
								<input type="file"
									name="items[0][images][]"
									class="form-control"
									accept="image/*" multiple>
								<small class="form-text text-muted">You
									can select multiple images
									(JPEG, PNG, GIF, WebP, max 5MB
									each)</small>
								<div class="mt-2 item-image-preview-0"
									id="item-image-preview-0">
								</div>
							</div>
						</div>
					</div>

					<button type="button" class="btn btn-sm btn-secondary"
						id="add-item">Add Item</button>

					<div class="mb-3 mt-3">
						<button type="submit" class="btn btn-primary">Create
							Section</button>
						<a href="{{route('cms.sections.index', $page)}}"
							class="btn btn-secondary">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection


@section('extra-css')
<link href="{{ asset('frontend/assets/fonts/simple/simple-line-icons.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
	integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
	crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
.icon-picker-modal .icon-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
	gap: 10px;
	max-height: 400px;
	overflow-y: auto;
	padding: 15px;
}

.icon-picker-modal .icon-item {
	text-align: center;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 4px;
	cursor: pointer;
	transition: all 0.2s;
}

.icon-picker-modal .icon-item:hover {
	background-color: #f0f0f0;
	border-color: #007bff;
}

.icon-picker-modal .icon-item.selected {
	background-color: #007bff;
	color: white;
	border-color: #007bff;
}

.icon-picker-modal .icon-item i {
	font-size: 24px;
	display: block;
	margin-bottom: 5px;
	line-height: 1;
}

.icon-picker-modal .icon-item i[class*="icon-"] {
	font-family: 'Simple-Line-Icons' !important;
	speak: none;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

.icon-picker-modal .icon-item i[class*="fa-"] {
	font-family: 'Font Awesome 6 Free' !important;
	font-weight: 900;
}

.icon-picker-modal .icon-item i[class*="fas"],
.icon-picker-modal .icon-item i[class*="far"],
.icon-picker-modal .icon-item i[class*="fab"] {
	font-family: 'Font Awesome 6 Free' !important;
}

.icon-picker-modal .icon-item i[class*="fas"] {
	font-weight: 900;
}

.icon-picker-modal .icon-item i[class*="far"] {
	font-weight: 400;
}

.icon-picker-modal .icon-item i[class*="fab"] {
	font-weight: 400;
	font-family: 'Font Awesome 6 Brands' !important;
}

[data-icon]:before {
	display: none;
}

.icon-picker-modal .icon-item small {
	display: block;
	font-size: 10px;
	margin-top: 5px;
	word-break: break-word;
	line-height: 1.2;
}

.icon-preview {
	min-height: 30px;
}

.icon-preview i {
	font-size: 24px;
	color: #007bff;
}

.icon-preview i[class*="icon-"] {
	font-family: 'Simple-Line-Icons' !important;
	speak: none;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

.icon-preview i[class*="fas"],
.icon-preview i[class*="far"],
.icon-preview i[class*="fab"] {
	font-family: 'Font Awesome 6 Free' !important;
}

.icon-preview i[class*="fas"] {
	font-weight: 900;
}

.icon-preview i[class*="far"] {
	font-weight: 400;
}

.icon-preview i[class*="fab"] {
	font-weight: 400;
	font-family: 'Font Awesome 6 Brands' !important;
}
</style>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
// Initialize Summernote editors
$(document).ready(function() {
	function initEditors() {
		$('.summernote').summernote({
			height: 200,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic',
					'underline',
					'clear'
				]],
				['color', ['color']],
				['para', ['ul', 'ol',
					'paragraph'
				]],
				['insert', ['link']],
				['view', ['fullscreen',
					'codeview',
					'help'
				]]
			]
		});
	}

	function reinitEditors() {
		$('.summernote').summernote('destroy');
		initEditors();
	}

	initEditors();

	// Re-init when switching tabs to handle hidden editors
	$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
		reinitEditors();
	});

	// Media preview helpers
	const imageFileInput = $('input[name="image_files[]"]');
	const imagePreviewWrapper = $('#image-preview-wrapper');

	const videoFileInput = $('input[name="video_file"]');
	const videoPreviewWrapper = $('#video-preview-wrapper');
	const videoPreview = $('#video-preview');

	function updateImagePreviews() {
		imagePreviewWrapper.empty();

		// Show previews for uploaded files
		if (imageFileInput[0] && imageFileInput[0].files) {
			Array.from(imageFileInput[0].files).forEach((file) => {
				const url = URL.createObjectURL(file);
				const preview = $('<div class="d-inline-block me-2 mb-2"><img src="' +
					url +
					'" alt="Preview" class="img-thumbnail" style="max-height:100px;"><button type="button" class="btn btn-sm btn-danger d-block w-100 mt-1 remove-file-preview">Remove</button></div>'
				);
				preview.find('.remove-file-preview').on(
					'click',
					function() {
						const dt =
							new DataTransfer();
						Array.from(imageFileInput[
									0
								]
								.files
							)
							.forEach((f,
								idx
							) => {
								if (f !==
									file
								)
									dt
									.items
									.add(
										f
									);
							});
						imageFileInput
							[
								0
							]
							.files =
							dt
							.files;
						updateImagePreviews
							();
					});
				imagePreviewWrapper.append(preview);
			});
		}

	}

	function showVideoPreview(src) {
		if (src) {
			videoPreview.attr('src', src);
			videoPreviewWrapper.show();
		} else {
			if (videoPreview.length && videoPreview[0]) {
				videoPreview[0].pause();
			}
			videoPreviewWrapper.hide();
		}
	}

	imageFileInput.on('change', function() {
		updateImagePreviews();
	});

	videoFileInput.on('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const url = URL.createObjectURL(file);
			showVideoPreview(url);
		} else {
			showVideoPreview('');
		}
	});

	// Items management
	let itemIndex = 1;
	$('#add-item').on('click', function() {
		const itemHtml = `
			<div class="item-row mb-3 p-3 border rounded">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<h6 class="mb-0">Item #${itemIndex + 1}</h6>
					<button type="button" class="btn btn-sm btn-danger remove-item">Remove Item</button>
				</div>

				<div class="row mb-2">
					<div class="col-md-4">
						<label class="form-label">Type</label>
						<input type="text" name="items[${itemIndex}][type]" class="form-control" value="default" placeholder="default">
					</div>
					<div class="col-md-4">
						<label class="form-label">Order</label>
						<input type="number" name="items[${itemIndex}][order]" class="form-control" value="${itemIndex}">
					</div>
					<div class="col-md-4">
						<label class="form-label">Status</label>
						<select name="items[${itemIndex}][is_active]" class="form-select">
							<option value="1" selected>Active</option>
							<option value="0">Inactive</option>
						</select>
					</div>
				</div>

				<!-- Item Language Tabs -->
				<ul class="nav nav-tabs mt-2" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="item-${itemIndex}-en-tab" data-bs-toggle="tab" data-bs-target="#item-${itemIndex}-en-content" type="button" role="tab" aria-controls="item-${itemIndex}-en-content" aria-selected="true">
							English
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="item-${itemIndex}-ar-tab" data-bs-toggle="tab" data-bs-target="#item-${itemIndex}-ar-content" type="button" role="tab" aria-controls="item-${itemIndex}-ar-content" aria-selected="false">
							Arabic
						</button>
					</li>
				</ul>

				<div class="tab-content mt-2">
					<!-- English Tab -->
					<div class="tab-pane fade show active" id="item-${itemIndex}-en-content" role="tabpanel" aria-labelledby="item-${itemIndex}-en-tab">
						<div class="mb-2">
							<input type="text" name="items[${itemIndex}][en][title]" class="form-control mb-2" placeholder="Title (EN)">
						</div>
						<div class="mb-2">
							<input type="text" name="items[${itemIndex}][en][sub_title]" class="form-control mb-2" placeholder="Sub Title (EN)">
						</div>
                            <div class="mb-2">
                                <textarea name="items[${itemIndex}][en][content]" class="form-control summernote" rows="2" placeholder="Content (EN)"></textarea>
                            </div>
						<div class="mb-2">
							<label class="form-label">Icon (EN)</label>
							<div class="input-group">
								<input type="text" name="items[${itemIndex}][en][icon]" class="form-control icon-input" placeholder="Select an icon" readonly>
								<button type="button" class="btn btn-outline-secondary icon-picker-btn" data-target="items[${itemIndex}][en][icon]">
									<i class="icon icon-settings"></i> Choose Icon
								</button>
							</div>
							<div class="icon-preview mt-2" id="icon-preview-items[${itemIndex}][en][icon]" data-name="items[${itemIndex}][en][icon]"></div>
						</div>
					</div>

					<!-- Arabic Tab -->
					<div class="tab-pane fade" id="item-${itemIndex}-ar-content" role="tabpanel" aria-labelledby="item-${itemIndex}-ar-tab">
						<div class="mb-2">
							<input type="text" name="items[${itemIndex}][ar][title]" class="form-control mb-2" placeholder="Title (AR)">
						</div>
						<div class="mb-2">
							<input type="text" name="items[${itemIndex}][ar][sub_title]" class="form-control mb-2" placeholder="Sub Title (AR)">
						</div>
                        <div class="mb-2">
                            <textarea name="items[${itemIndex}][ar][content]" class="form-control summernote" rows="2" placeholder="Content (AR)"></textarea>
                        </div>
						<div class="mb-2">
							<label class="form-label">Icon (AR)</label>
							<div class="input-group">
								<input type="text" name="items[${itemIndex}][ar][icon]" class="form-control icon-input" placeholder="Select an icon" readonly>
								<button type="button" class="btn btn-outline-secondary icon-picker-btn" data-target="items[${itemIndex}][ar][icon]">
									<i class="icon icon-settings"></i> Choose Icon
								</button>
							</div>
							<div class="icon-preview mt-2" id="icon-preview-items[${itemIndex}][ar][icon]" data-name="items[${itemIndex}][ar][icon]"></div>
						</div>
					</div>
				</div>

				<!-- Item Image Upload -->
				<div class="mb-2 mt-3">
					<label class="form-label">Item Images (optional)</label>
					<input type="file" name="items[${itemIndex}][images][]" class="form-control item-image-input" accept="image/*" multiple data-item-index="${itemIndex}">
					<small class="form-text text-muted">You can select multiple images (JPEG, PNG, GIF, WebP, max 5MB each)</small>
					<div class="mt-2 item-image-preview" id="item-image-preview-${itemIndex}"></div>
				</div>
			</div>
		`;
		$('#items-container').append(itemHtml);
		reinitEditors();
		itemIndex++;
	});

	$(document).on('click', '.remove-item', function() {
		$(this).closest('.item-row').remove();
		// Renumber items
		$('#items-container .item-row').each(function(index) {
			$(this).find('h6').text(
				'Item #' +
				(index +
					1
				)
			);
		});
	});

	// Handle item image previews
	$(document).on('change', '.item-image-input', function() {
		const itemIndex = $(this).data('item-index');
		const previewContainer = $('#item-image-preview-' +
			itemIndex);
		previewContainer.empty();

		if (this.files && this.files.length > 0) {
			Array.from(this.files).forEach((file) => {
				const url = URL
					.createObjectURL(
						file
					);
				const preview = $('<div class="d-inline-block me-2 mb-2"><img src="' +
					url +
					'" alt="Preview" class="img-thumbnail" style="max-height:80px;"></div>'
				);
				previewContainer
					.append(
						preview
					);
			});
		}
	});

	// Handle initial item image preview
	$('input[name="items[0][images][]"]').on('change', function() {
		const previewContainer = $('#item-image-preview-0');
		previewContainer.empty();

		if (this.files && this.files.length > 0) {
			Array.from(this.files).forEach((file) => {
				const url = URL
					.createObjectURL(
						file
					);
				const preview = $('<div class="d-inline-block me-2 mb-2"><img src="' +
					url +
					'" alt="Preview" class="img-thumbnail" style="max-height:80px;"></div>'
				);
				previewContainer
					.append(
						preview
					);
			});
		}
	});

	// Icon Picker
	// Simple Line Icons (all 189 icons)
	const simpleLineIcons = ['icon-user-female', 'icon-user-follow', 'icon-user-following',
		'icon-user-unfollow', 'icon-trophy', 'icon-screen-smartphone',
		'icon-screen-desktop', 'icon-plane', 'icon-notebook',
		'icon-moustache', 'icon-mouse', 'icon-magnet', 'icon-energy',
		'icon-emoticon-smile', 'icon-disc', 'icon-cursor-move', 'icon-crop',
		'icon-credit-card', 'icon-chemistry', 'icon-user', 'icon-speedometer',
		'icon-social-youtube', 'icon-social-twitter', 'icon-social-tumblr',
		'icon-social-facebook', 'icon-social-dropbox', 'icon-social-dribbble',
		'icon-shield', 'icon-screen-tablet', 'icon-magic-wand',
		'icon-hourglass', 'icon-graduation', 'icon-ghost',
		'icon-game-controller', 'icon-fire', 'icon-eyeglasses',
		'icon-envelope-open', 'icon-envelope-letter', 'icon-bell',
		'icon-badge', 'icon-anchor', 'icon-wallet', 'icon-vector',
		'icon-speech', 'icon-puzzle', 'icon-printer', 'icon-present',
		'icon-playlist', 'icon-pin', 'icon-picture', 'icon-map',
		'icon-layers', 'icon-handbag', 'icon-globe-alt', 'icon-globe',
		'icon-frame', 'icon-folder-alt', 'icon-film', 'icon-feed',
		'icon-earphones-alt', 'icon-earphones', 'icon-drop', 'icon-drawer',
		'icon-docs', 'icon-directions', 'icon-direction', 'icon-diamond',
		'icon-cup', 'icon-compass', 'icon-call-out', 'icon-call-in',
		'icon-call-end', 'icon-calculator', 'icon-bubbles', 'icon-briefcase',
		'icon-book-open', 'icon-basket-loaded', 'icon-basket', 'icon-bag',
		'icon-action-undo', 'icon-action-redo', 'icon-wrench',
		'icon-umbrella', 'icon-trash', 'icon-tag', 'icon-support',
		'icon-size-fullscreen', 'icon-size-actual', 'icon-shuffle',
		'icon-share-alt', 'icon-share', 'icon-rocket', 'icon-question',
		'icon-pie-chart', 'icon-pencil', 'icon-note', 'icon-music-tone-alt',
		'icon-music-tone', 'icon-microphone', 'icon-loop', 'icon-logout',
		'icon-login', 'icon-list', 'icon-like', 'icon-home', 'icon-grid',
		'icon-graph', 'icon-equalizer', 'icon-dislike', 'icon-cursor',
		'icon-control-start', 'icon-control-rewind', 'icon-control-play',
		'icon-control-pause', 'icon-control-forward', 'icon-control-end',
		'icon-calendar', 'icon-bulb', 'icon-bar-chart', 'icon-arrow-up',
		'icon-arrow-right', 'icon-arrow-left', 'icon-arrow-down', 'icon-ban',
		'icon-bubble', 'icon-camcorder', 'icon-camera', 'icon-check',
		'icon-clock', 'icon-close', 'icon-cloud-download',
		'icon-cloud-upload', 'icon-doc', 'icon-envelope', 'icon-eye',
		'icon-flag', 'icon-folder', 'icon-heart', 'icon-info', 'icon-key',
		'icon-link', 'icon-lock', 'icon-lock-open', 'icon-magnifier',
		'icon-magnifier-add', 'icon-magnifier-remove', 'icon-paper-clip',
		'icon-paper-plane', 'icon-plus', 'icon-pointer', 'icon-power',
		'icon-refresh', 'icon-reload', 'icon-settings', 'icon-star',
		'icon-symbol-female', 'icon-symbol-male', 'icon-target',
		'icon-volume-1', 'icon-volume-2', 'icon-volume-off', 'icon-users'
	];

	// Font Awesome Icons - Comprehensive set covering all concepts
	// Using 'fas' (solid) prefix for Font Awesome 6
	const fontAwesomeIcons = [
		// User & People
		'fas fa-user', 'fas fa-users', 'fas fa-user-group',
		'fas fa-user-circle', 'fas fa-user-doctor', 'fas fa-user-nurse',
		'fas fa-user-graduate', 'fas fa-user-tie', 'fas fa-user-astronaut',
		'fas fa-user-secret', 'fas fa-user-shield',
		'fas fa-user-gear', 'fas fa-user-pen', 'fas fa-user-plus',
		'fas fa-user-minus', 'fas fa-user-xmark',
		'fas fa-user-friends', 'fas fa-user-clock', 'fas fa-user-check',
		'fas fa-user-slash', 'fas fa-user-lock',
		// Business & Finance
		'fas fa-briefcase', 'fas fa-building', 'fas fa-chart-line',
		'fas fa-chart-column', 'fas fa-chart-pie',
		'fas fa-chart-area', 'fas fa-dollar-sign', 'fas fa-euro-sign',
		'fas fa-sterling-sign', 'fas fa-yen-sign',
		'fas fa-credit-card', 'fas fa-wallet', 'fas fa-money-bill',
		'fas fa-money-bill-wave', 'fas fa-coins',
		'fas fa-hand-holding-dollar', 'fas fa-piggy-bank',
		'fas fa-cash-register', 'fas fa-receipt',
		'fas fa-file-invoice-dollar',
		// Technology & Devices
		'fas fa-laptop', 'fas fa-desktop', 'fas fa-tablet',
		'fas fa-mobile-screen-button', 'fas fa-phone', 'fas fa-phone-flip',
		'fas fa-server', 'fas fa-database', 'fas fa-microchip',
		'fas fa-memory', 'fas fa-hard-drive', 'fas fa-usb',
		'fas fa-wifi', 'fas fa-bluetooth', 'fas fa-satellite',
		'fas fa-satellite-dish', 'fas fa-router',
		// Communication
		'fas fa-envelope', 'fas fa-envelope-open', 'fas fa-mail-bulk',
		'fas fa-inbox', 'fas fa-paper-plane',
		'fas fa-comments', 'fas fa-comment', 'fas fa-comment-dots',
		'fas fa-comment', 'fas fa-comment-slash',
		'fas fa-bullhorn', 'fas fa-megaphone', 'fas fa-broadcast-tower',
		'fas fa-rss', 'fas fa-bell', 'fas fa-bell-slash',
		// Social Media (using fab for brands)
		'fab fa-facebook', 'fab fa-facebook-f', 'fab fa-twitter',
		'fab fa-instagram', 'fab fa-linkedin', 'fab fa-youtube',
		'fab fa-whatsapp', 'fab fa-telegram', 'fab fa-skype',
		'fab fa-discord', 'fab fa-reddit', 'fab fa-pinterest',
		'fab fa-snapchat', 'fab fa-tiktok', 'fab fa-vimeo', 'fab fa-twitch',
		'fab fa-github', 'fab fa-gitlab',
		// Shopping & E-commerce
		'fas fa-shopping-cart', 'fa fa-shopping-bag', 'fa fa-shopping-basket',
		'fa fa-store', 'fa fa-store-alt',
		'fas fa-tags', 'fa fa-tag', 'fa fa-gift', 'fa fa-gift-card',
		'fa fa-box', 'fa fa-boxes',
		'fas fa-shipping-fast', 'fa fa-truck', 'fa fa-truck-loading',
		'fa fa-dolly', 'fa fa-pallet',
		// Education & Learning
		'fas fa-graduation-cap', 'fa fa-school', 'fa fa-book',
		'fa fa-book-open', 'fa fa-book-reader',
		'fas fa-chalkboard', 'fa fa-chalkboard-teacher',
		'fa fa-user-graduate', 'fa fa-certificate', 'fa fa-award',
		'fas fa-medal', 'fa fa-trophy', 'fa fa-ribbon', 'fa fa-star',
		'fa fa-star-half-alt',
		// Health & Medical
		'fas fa-heart', 'fa fa-heartbeat', 'fa fa-hospital',
		'fa fa-hospital-alt', 'fa fa-clinic-medical',
		'fas fa-user-md', 'fa fa-user-nurse', 'fa fa-stethoscope',
		'fa fa-pills', 'fa fa-syringe',
		'fas fa-ambulance', 'fa fa-band-aid', 'fa fa-first-aid',
		'fa fa-prescription', 'fa fa-x-ray',
		// Food & Restaurant
		'fas fa-utensils', 'fa fa-utensil-spoon', 'fa fa-coffee',
		'fa fa-wine-glass', 'fa fa-wine-glass-alt',
		'fas fa-cocktail', 'fa fa-beer', 'fa fa-pizza-slice',
		'fa fa-hamburger', 'fa fa-ice-cream',
		'fas fa-birthday-cake', 'fa fa-cookie', 'fa fa-cookie-bite',
		'fa fa-fish', 'fa fa-drumstick-bite',
		// Travel & Transportation
		'fas fa-plane', 'fa fa-plane-departure', 'fa fa-plane-arrival',
		'fa fa-car', 'fa fa-car-side',
		'fas fa-taxi', 'fa fa-bus', 'fa fa-train', 'fa fa-subway',
		'fa fa-ship', 'fa fa-anchor',
		'fas fa-bicycle', 'fa fa-motorcycle', 'fa fa-walking', 'fa fa-hiking',
		'fa fa-map', 'fa fa-map-marked-alt',
		'fas fa-map-marker-alt', 'fa fa-compass', 'fa fa-route',
		'fa fa-suitcase', 'fa fa-suitcase-rolling',
		'fas fa-hotel', 'fa fa-bed', 'fa fa-umbrella-beach', 'fa fa-passport',
		'fa fa-globe', 'fa fa-globe-americas',
		// Sports & Fitness
		'fas fa-futbol', 'fa fa-basketball-ball', 'fa fa-volleyball-ball',
		'fa fa-football-ball', 'fa fa-baseball-ball',
		'fas fa-table-tennis', 'fa fa-golf-ball', 'fa fa-swimming-pool',
		'fa fa-dumbbell', 'fa fa-running',
		'fas fa-biking', 'fa fa-skiing', 'fa fa-skiing-nordic',
		'fa fa-snowboarding', 'fa fa-skating',
		// Music & Entertainment
		'fas fa-music', 'fa fa-headphones', 'fa fa-headphones-alt',
		'fa fa-microphone', 'fa fa-microphone-alt',
		'fas fa-microphone-alt-slash', 'fa fa-guitar', 'fa fa-drum',
		'fa fa-video', 'fa fa-film', 'fa fa-tv',
		'fas fa-theater-masks', 'fa fa-magic', 'fa fa-gamepad', 'fa fa-chess',
		'fa fa-puzzle-piece',
		// Weather & Nature
		'fas fa-sun', 'fa fa-moon', 'fa fa-cloud', 'fa fa-cloud-sun',
		'fa fa-cloud-moon', 'fa fa-cloud-rain',
		'fas fa-cloud-showers-heavy', 'fa fa-snowflake', 'fa fa-wind',
		'fa fa-umbrella', 'fa fa-tree',
		'fas fa-leaf', 'fa fa-seedling', 'fa fa-mountain', 'fa fa-water',
		'fa fa-fire', 'fa fa-volcano',
		// Time & Calendar
		'fas fa-clock', 'fa fa-calendar', 'fa fa-calendar-alt',
		'fa fa-calendar-check', 'fa fa-calendar-day',
		'fas fa-calendar-week', 'fa fa-calendar-month',
		'fa fa-calendar-times', 'fa fa-hourglass', 'fa fa-hourglass-half',
		'fas fa-hourglass-start', 'fa fa-hourglass-end', 'fa fa-stopwatch',
		'fa fa-history',
		// Files & Documents
		'fas fa-file', 'fa fa-file-alt', 'fa fa-file-pdf', 'fa fa-file-word',
		'fa fa-file-excel', 'fa fa-file-powerpoint',
		'fas fa-file-image', 'fa fa-file-video', 'fa fa-file-audio',
		'fa fa-file-archive', 'fa fa-file-code',
		'fas fa-folder', 'fa fa-folder-open', 'fa fa-folder-plus',
		'fa fa-folder-minus', 'fa fa-archive',
		// Security & Safety
		'fas fa-shield-alt', 'fa fa-shield-virus', 'fa fa-lock',
		'fa fa-unlock', 'fa fa-lock-open', 'fa fa-key',
		'fas fa-fingerprint', 'fa fa-user-shield', 'fa fa-user-secret',
		'fa fa-eye', 'fa fa-eye-slash',
		'fas fa-camera', 'fa fa-camera-retro', 'fa fa-video',
		'fa fa-video-slash', 'fa fa-search', 'fa fa-search-plus',
		'fas fa-search-minus', 'fa fa-bug', 'fa fa-virus',
		'fa fa-virus-slash', 'fa fa-mask',
		// Tools & Settings
		'fas fa-wrench', 'fa fa-tools', 'fa fa-screwdriver', 'fa fa-hammer',
		'fa fa-cog', 'fa fa-cogs',
		'fas fa-sliders-h', 'fa fa-toggle-on', 'fa fa-toggle-off',
		'fa fa-power-off', 'fa fa-plug',
		'fas fa-bolt', 'fa fa-lightbulb', 'fa fa-flashlight',
		'fa fa-battery-full', 'fa fa-battery-half',
		'fas fa-battery-empty', 'fa fa-plug', 'fa fa-plug-circle-bolt',
		// Arrows & Navigation
		'fas fa-arrow-up', 'fa fa-arrow-down', 'fa fa-arrow-left',
		'fa fa-arrow-right', 'fa fa-arrows-alt',
		'fas fa-arrow-circle-up', 'fa fa-arrow-circle-down',
		'fa fa-arrow-circle-left', 'fa fa-arrow-circle-right',
		'fas fa-chevron-up', 'fa fa-chevron-down', 'fa fa-chevron-left',
		'fa fa-chevron-right',
		'fas fa-angle-up', 'fa fa-angle-down', 'fa fa-angle-left',
		'fa fa-angle-right', 'fa fa-caret-up',
		'fas fa-caret-down', 'fa fa-caret-left', 'fa fa-caret-right',
		'fa fa-hand-point-up', 'fa fa-hand-point-down',
		'fas fa-hand-point-left', 'fa fa-hand-point-right',
		'fa fa-long-arrow-alt-up', 'fa fa-long-arrow-alt-down',
		'fas fa-long-arrow-alt-left', 'fa fa-long-arrow-alt-right',
		// Status & Actions
		'fas fa-check', 'fa fa-check-circle', 'fa fa-check-square',
		'fa fa-times', 'fa fa-times-circle',
		'fas fa-ban', 'fa fa-exclamation', 'fa fa-exclamation-circle',
		'fa fa-exclamation-triangle',
		'fas fa-question', 'fa fa-question-circle', 'fa fa-info',
		'fa fa-info-circle', 'fa fa-plus', 'fa fa-plus-circle',
		'fas fa-plus-square', 'fa fa-minus', 'fa fa-minus-circle',
		'fa fa-minus-square', 'fa fa-edit', 'fa fa-pencil-alt',
		'fas fa-trash', 'fa fa-trash-alt', 'fa fa-undo', 'fa fa-redo',
		'fa fa-save', 'fa fa-download', 'fa fa-upload',
		'fas fa-share', 'fa fa-share-alt', 'fa fa-share-square', 'fa fa-link',
		'fa fa-unlink', 'fa fa-copy', 'fa fa-cut',
		'fas fa-paste', 'fa fa-clone', 'fa fa-expand', 'fa fa-compress',
		'fa fa-compress-alt', 'fa fa-expand-alt',
		// Shapes & Symbols
		'fas fa-circle', 'fa fa-square', 'fa fa-square-full',
		'fa fa-dot-circle', 'fa fa-certificate',
		'fas fa-star', 'fa fa-star-half', 'fa fa-star-half-alt',
		'fa fa-heart', 'fa fa-heart-broken',
		'fas fa-thumbs-up', 'fa fa-thumbs-down', 'fa fa-hand-rock',
		'fa fa-hand-paper', 'fa fa-hand-scissors',
		'fas fa-hand-lizard', 'fa fa-hand-spock', 'fa fa-hand-peace',
		'fa fa-hand-point-up', 'fa fa-hand-point-down',
		'fas fa-hand-point-left', 'fa fa-hand-point-right', 'fa fa-flag',
		'fa fa-flag-checkered',
		// Home & Living
		'fas fa-home', 'fa fa-home-lg-alt', 'fa fa-couch', 'fa fa-chair',
		'fa fa-bed', 'fa fa-door-open',
		'fas fa-door-closed', 'fa fa-window-maximize',
		'fa fa-window-minimize', 'fa fa-window-restore',
		'fas fa-lightbulb', 'fa fa-lamp', 'fa fa-fan',
		'fa fa-thermometer-half', 'fa fa-fire', 'fa fa-fire-alt',
		'fas fa-shower', 'fa fa-bath', 'fa fa-toilet', 'fa fa-sink',
		'fa fa-utensils', 'fa fa-blender',
		'fas fa-microwave', 'fa fa-refrigerator', 'fa fa-oven',
		'fa fa-dishwasher',
		// Animals & Pets
		'fas fa-dog', 'fa fa-cat', 'fa fa-dove', 'fa fa-crow', 'fa fa-fish',
		'fa fa-horse', 'fa fa-hippo',
		'fas fa-spider', 'fa fa-bug', 'fa fa-paw', 'fa fa-feather',
		'fa fa-feather-alt',
		// Science & Research
		'fas fa-flask', 'fa fa-vial', 'fa fa-microscope', 'fa fa-atom',
		'fa fa-dna', 'fa fa-virus',
		'fas fa-virus-slash', 'fa fa-brain', 'fa fa-rocket',
		'fa fa-satellite', 'fa fa-satellite-dish',
		'fas fa-telescope', 'fa fa-magnet', 'fa fa-radiation',
		'fa fa-radiation-alt',
		// Law & Justice
		'fas fa-gavel', 'fa fa-balance-scale', 'fa fa-balance-scale-left',
		'fa fa-balance-scale-right',
		'fas fa-landmark', 'fa fa-scroll', 'fa fa-stamp',
		'fa fa-file-contract', 'fa fa-handshake',
		// Religion & Spirituality
		'fas fa-church', 'fa fa-mosque', 'fa fa-synagogue',
		'fa fa-place-of-worship', 'fa fa-star-and-crescent',
		'fas fa-om', 'fa fa-yin-yang', 'fa fa-cross', 'fa fa-dove',
		'fa fa-pray', 'fa fa-praying-hands',
		// Industry & Manufacturing
		'fas fa-industry', 'fa fa-warehouse', 'fa fa-hard-hat', 'fa fa-tools',
		'fa fa-hammer', 'fa fa-wrench',
		'fas fa-cog', 'fa fa-cogs', 'fa fa-oil-can', 'fa fa-gas-pump',
		'fa fa-fire-extinguisher',
		'fas fa-truck-pickup', 'fa fa-truck-moving', 'fa fa-forklift',
		'fa fa-pallet', 'fa fa-boxes',
		// Art & Design
		'fas fa-paint-brush', 'fa fa-paint-roller', 'fa fa-palette',
		'fa fa-brush', 'fa fa-pencil-alt',
		'fas fa-pen', 'fa fa-pen-fancy', 'fa fa-pen-nib', 'fa fa-marker',
		'fa fa-highlighter',
		'fas fa-eraser', 'fa fa-stamp', 'fa fa-image', 'fa fa-images',
		'fa fa-photo-video',
		'fas fa-camera-retro', 'fa fa-film', 'fa fa-video', 'fa fa-magic',
		'fa fa-theater-masks',
		// Miscellaneous
		'fas fa-gift', 'fa fa-birthday-cake', 'fa fa-cake-candles',
		'fa fa-candy-cane', 'fa fa-lollipop',
		'fas fa-ice-cream', 'fa fa-cookie', 'fa fa-cookie-bite',
		'fa fa-mug-hot', 'fa fa-coffee',
		'fas fa-wine-glass', 'fa fa-cocktail', 'fa fa-beer',
		'fa fa-champagne-glasses', 'fa fa-wine-bottle',
		'fas fa-gem', 'fa fa-ring', 'fa fa-crown', 'fa fa-trophy',
		'fa fa-medal', 'fa fa-award',
		'fas fa-ribbon', 'fa fa-certificate', 'fa fa-badge', 'fa fa-id-badge',
		'fa fa-id-card',
		'fas fa-credit-card', 'fa fa-wallet', 'fa fa-money-bill',
		'fa fa-coins', 'fa fa-hand-holding-usd'
	];

	function createIconPickerModal() {
		if ($('#iconPickerModal').length) return;

		// Generate Simple Line Icons grid
		let simpleLineGridHtml = '<div class="icon-grid" id="simpleLineIconsGrid">';
		simpleLineIcons.forEach(icon => {
			const iconName = icon.replace('icon-', '')
				.replace(/-/g, ' ');
			simpleLineGridHtml += `<div class="icon-item" data-icon="${icon}" title="${iconName}">
				<i class="${icon}"></i>
				<small>${iconName}</small>
			</div>`;
		});
		simpleLineGridHtml += '</div>';

		// Generate Font Awesome Icons grid
		let fontAwesomeGridHtml =
			'<div class="icon-grid" id="fontAwesomeIconsGrid" style="display:none;">';
		fontAwesomeIcons.forEach(icon => {
			// Ensure icon has proper prefix (fas, far, or fab)
			let iconClass = icon;
			if (icon.startsWith('fa fa-')) {
				iconClass = icon.replace('fa fa-',
					'fas fa-');
			} else if (!icon.match(/^(fas|far|fab) fa-/)) {
				iconClass = 'fas ' + icon;
			}
			const iconName = iconClass.replace(
					/^(fas|far|fab) fa-/, '')
				.replace(/-/g, ' ');
			fontAwesomeGridHtml += `<div class="icon-item" data-icon="${iconClass}" title="${iconName}">
				<i class="${iconClass}"></i>
				<small>${iconName}</small>
			</div>`;
		});
		fontAwesomeGridHtml += '</div>';

		const modalHtml = `
			<div class="modal fade" id="iconPickerModal" tabindex="-1">
				<div class="modal-dialog modal-xl">
					<div class="modal-content icon-picker-modal">
						<div class="modal-header">
							<h5 class="modal-title">Choose an Icon</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<input type="text" class="form-control" id="iconSearchInput" placeholder="Search icons...">
							</div>
							<ul class="nav nav-tabs mb-3" id="iconLibraryTabs" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="simpleLine-tab" data-bs-toggle="tab" data-bs-target="#simpleLine" type="button" role="tab">
										Simple Line Icons (${simpleLineIcons.length})
									</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="fontAwesome-tab" data-bs-toggle="tab" data-bs-target="#fontAwesome" type="button" role="tab">
										Font Awesome (${fontAwesomeIcons.length})
									</button>
								</li>
							</ul>
							<div class="tab-content" id="iconLibraryTabContent">
								<div class="tab-pane fade show active" id="simpleLine" role="tabpanel">
									${simpleLineGridHtml}
								</div>
								<div class="tab-pane fade" id="fontAwesome" role="tabpanel">
									${fontAwesomeGridHtml}
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary" id="confirmIconSelection">Select</button>
						</div>
					</div>
				</div>
			</div>
		`;
		$('body').append(modalHtml);
	}

	let currentIconTarget = null;
	let selectedIcon = null;

	$(document).on('click', '.icon-picker-btn', function() {
		createIconPickerModal();
		currentIconTarget = $(this).data('target');
		selectedIcon = $('input[name="' + currentIconTarget + '"]')
			.val();

		// Highlight currently selected icon
		$('#iconPickerModal .icon-item').removeClass('selected');
		if (selectedIcon) {
			$('#iconPickerModal .icon-item[data-icon="' +
				selectedIcon + '"]').addClass(
				'selected');
		}

		const modal = new bootstrap.Modal(document.getElementById(
			'iconPickerModal'));
		modal.show();
		// Clear search when modal opens
		$('#iconSearchInput').val('').trigger('input');
	});

	$(document).on('click', '#iconPickerModal .icon-item', function() {
		$('#iconPickerModal .icon-item').removeClass('selected');
		$(this).addClass('selected');
		selectedIcon = $(this).data('icon');
	});

	// Icon search functionality
	$(document).on('input', '#iconSearchInput', function() {
		const searchTerm = $(this).val().toLowerCase();
		$('#iconPickerModal .icon-item').each(function() {
			const iconName = $(this).data(
					'icon')
				.toLowerCase();
			const iconTitle = $(this)
				.attr('title')
				.toLowerCase();
			if (iconName.includes(
					searchTerm
				) ||
				iconTitle.includes(
					searchTerm
				)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});

	$(document).on('click', '#confirmIconSelection', function() {
		if (currentIconTarget && selectedIcon) {
			$('input[name="' + currentIconTarget + '"]').val(
				selectedIcon);
			// Find preview container by data-name attribute
			const previewContainer = $(
				'.icon-preview[data-name="' +
				currentIconTarget + '"]');
			if (previewContainer.length) {
				previewContainer.html('<i class="' +
					selectedIcon +
					'"></i> <span>' +
					selectedIcon +
					'</span>');
			} else {
				// Fallback to ID-based lookup
				const previewId = 'icon-preview-' +
					currentIconTarget.replace(
						/\[|\]/g, '_');
				$('#' + previewId).html('<i class="' +
					selectedIcon +
					'"></i> <span>' +
					selectedIcon +
					'</span>');
			}
		}
		$('#iconPickerModal').modal('hide');
	});

	// Update previews for existing values
	$('.icon-input').each(function() {
		const input = $(this);
		const iconClass = input.val();
		const inputName = input.attr('name');
		if (iconClass && inputName) {
			const previewContainer = $(
				'.icon-preview[data-name="' +
				inputName + '"]');
			if (previewContainer.length) {
				previewContainer.html('<i class="' +
					iconClass +
					'"></i> <span>' +
					iconClass +
					'</span>');
			}
		}
	});
});
</script>
@endsection