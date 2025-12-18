@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">Edit Item</h4>
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
					<li class="breadcrumb-item"><a
							href="{{route('cms.items.index', [$page, $section])}}">Items</a>
					</li>
					<li class="breadcrumb-item active">Edit</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="{{route('cms.items.update', [$page, $section, $item])}}"
					method="POST" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Type <span
										class="text-danger">*</span></label>
								<input type="text" name="type"
									class="form-control @error('type') is-invalid @enderror"
									value="{{old('type', $item->type)}}"
									required>
								@error('type')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="mb-3">
								<label class="form-label">Order</label>
								<input type="number" name="order"
									class="form-control"
									value="{{old('order', $item->order)}}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="mb-3">
								<label class="form-label">Status</label>
								<select name="is_active"
									class="form-select">
									<option value="1"
										{{old('is_active', $item->is_active) ? 'selected' : ''}}>
										Active</option>
									<option value="0"
										{{!old('is_active', $item->is_active) ? 'selected' : ''}}>
										Inactive</option>
								</select>
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
								<label class="form-label">Title (EN)
									<span
										class="text-danger">*</span></label>
								<input type="text" name="en[title]"
									class="form-control @error('en.title') is-invalid @enderror"
									value="{{old('en.title', $item->translate('en')->title ?? '')}}"
									required>
								@error('en.title')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
							<div class="mb-3">
								<label class="form-label">Sub Title
									(EN)</label>
								<input type="text" name="en[sub_title]"
									class="form-control"
									value="{{old('en.sub_title', $item->translate('en')->sub_title ?? '')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Content
									(EN)</label>
								<textarea name="en[content]"
									class="form-control summernote"
									rows="4">{{old('en.content', $item->translate('en')->content ?? '')}}</textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">Icon
									(EN)</label>
								<input type="text" name="en[icon]"
									class="form-control"
									value="{{old('en.icon', $item->translate('en')->icon ?? '')}}"
									placeholder="e.g., fa fa-home or icon path">
								<small class="form-text text-muted">Icon class (FontAwesome, Material Icons) or icon image path</small>
							</div>
						</div>

						<!-- Arabic Tab -->
						<div class="tab-pane fade" id="ar-content" role="tabpanel"
							aria-labelledby="ar-tab">
							<div class="mb-3">
								<label class="form-label">Title (AR)
									<span
										class="text-danger">*</span></label>
								<input type="text" name="ar[title]"
									class="form-control @error('ar.title') is-invalid @enderror"
									value="{{old('ar.title', $item->translate('ar')->title ?? '')}}"
									required>
								@error('ar.title')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
							<div class="mb-3">
								<label class="form-label">Sub Title
									(AR)</label>
								<input type="text" name="ar[sub_title]"
									class="form-control"
									value="{{old('ar.sub_title', $item->translate('ar')->sub_title ?? '')}}">
							</div>
							<div class="mb-3">
								<label class="form-label">Content
									(AR)</label>
								<textarea name="ar[content]"
									class="form-control summernote"
									rows="4">{{old('ar.content', $item->translate('ar')->content ?? '')}}</textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">Icon
									(AR)</label>
								<input type="text" name="ar[icon]"
									class="form-control"
									value="{{old('ar.icon', $item->translate('ar')->icon ?? '')}}"
									placeholder="e.g., fa fa-home or icon path">
								<small class="form-text text-muted">Icon class (FontAwesome, Material Icons) or icon image path</small>
							</div>
						</div>
					</div>

					<hr>
					<h5>Existing Images</h5>
					<div class="mb-3" id="existing-images">
						@foreach($item->getMedia('images') as $media)
						<div class="image-item mb-2 p-2 border rounded">
							<input type="hidden" name="existing_images[]"
								value="{{ $media->id }}">
							<div class="row align-items-center">
								<div class="col-md-2">
									<img src="{{ $media->getUrl('thumb') }}"
										alt="Preview"
										class="img-thumbnail"
										style="max-width: 100px;">
								</div>
								<div class="col-md-4">
									<input type="text"
										name="existing_image_alt_{{ $media->id }}"
										class="form-control form-control-sm"
										value="{{ $media->getCustomProperty('alt_text', '') }}"
										placeholder="Alt Text">
								</div>
								<div class="col-md-4">
									<small
										class="text-muted">{{ $media->name }}</small>
								</div>
								<div class="col-md-2">
									<button type="button"
										class="btn btn-sm btn-danger remove-image"
										data-media-id="{{ $media->id }}">Remove</button>
								</div>
							</div>
						</div>
						@endforeach
					</div>

					<hr>
					<h5>Add New Images</h5>
					<div class="mb-3">
						<label class="form-label">Upload Images</label>
						<input type="file" name="images[]" class="form-control"
							multiple accept="image/*">
						<small class="form-text text-muted">You can select multiple
							images (JPEG, PNG, GIF, WebP, max 5MB
							each)</small>
					</div>

					<div class="mb-3 mt-3">
						<button type="submit" class="btn btn-primary">Update
							Item</button>
						<a href="{{route('cms.items.index', [$page, $section])}}"
							class="btn btn-secondary">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@section('extra-css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

// Initialize Summernote editors
$(document).ready(function() {
$('.summernote').summernote({
height: 200,
toolbar: [
['style', ['style']],
['font', ['bold', 'italic', 'underline', 'clear']],
['fontname', ['fontname']],
['color', ['color']],
['para', ['ul', 'ol', 'paragraph']],
['table', ['table']],
['insert', ['link']],
['view', ['fullscreen', 'codeview', 'help']]
]
});

// Reinitialize editor when tab is shown (for hidden tabs)
$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
$('.summernote').summernote('destroy');
$('.summernote').summernote({
height: 200,
toolbar: [
['style', ['style']],
['font', ['bold', 'italic', 'underline', 'clear']],
['fontname', ['fontname']],
['color', ['color']],
['para', ['ul', 'ol', 'paragraph']],
['table', ['table']],
['insert', ['link']],
['view', ['fullscreen', 'codeview', 'help']]
]
});
});
});

document.addEventListener('click', function(e) {
if (e.target.classList.contains('remove-image')) {
e.target.closest('.image-item').remove();
}
});
</script>
@endsection
@endsection
