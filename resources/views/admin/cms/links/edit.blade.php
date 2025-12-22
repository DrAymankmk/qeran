@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">Edit Link - {{ ucfirst($type) }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">Dashboard</a>
					</li>
					<li class="breadcrumb-item"><a href="{{route('cms.pages.index')}}">CMS
							Pages</a></li>
					<li class="breadcrumb-item active">Edit Link</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="{{route('cms.links.update', [$type, $id, $link])}}" method="POST">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Name <span
										class="text-danger">*</span></label>
								<input type="text" name="name"
									class="form-control @error('name') is-invalid @enderror"
									value="{{old('name', $link->name)}}"
									required>
								@error('name')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Link Type
									<span
										class="text-danger">*</span></label>
								<select name="link_type" id="link_type"
									class="form-select @error('link_type') is-invalid @enderror"
									required>
									<option value="route"
										{{old('link_type', $link->route_name ? 'route' : 'custom') == 'route' ? 'selected' : ''}}>
										Route Name</option>
									<option value="custom"
										{{old('link_type', $link->route_name ? 'route' : 'custom') == 'custom' ? 'selected' : ''}}>
										Custom URL</option>
								</select>
								@error('link_type')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6" id="route_name_field"
							style="display: {{old('link_type', $link->route_name ? 'route' : 'custom') == 'route' ? 'block' : 'none'}};">
							<div class="mb-3">
								<label class="form-label">Route Name
									<span
										class="text-danger">*</span></label>
								<select name="route_name"
									id="route_name"
									class="form-select @error('route_name') is-invalid @enderror">
									<option value="">Select a
										route...</option>
									@foreach($routes as $routeName
									=> $routeValue)
									<option value="{{ $routeName }}"
										{{old('route_name', $link->route_name) == $routeName ? 'selected' : ''}}>
										{{ $routeName }}
									</option>
									@endforeach
								</select>
								<small class="form-text text-muted">Choose
									a route name from the
									list</small>
								@error('route_name')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-md-6" id="link_field"
							style="display: {{old('link_type', $link->route_name ? 'route' : 'custom') == 'custom' ? 'block' : 'none'}};">
							<div class="mb-3">
								<label class="form-label">Link/URL <span
										class="text-danger">*</span></label>
								<input type="url" name="link" id="link"
									class="form-control @error('link') is-invalid @enderror"
									value="{{old('link', $link->link)}}">
								<small class="form-text text-muted">Enter
									a custom URL (e.g.,
									https://example.com)</small>
								@error('link')
								<div class="invalid-feedback">
									{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="mb-3">
								<label class="form-label">Icon</label>
								<div class="input-group">
									<input type="text" name="icon"
										id="icon"
										class="form-control icon-input"
										value="{{old('icon', $link->icon)}}"
										placeholder="Select an icon"
										readonly>
									<button type="button"
										class="btn btn-outline-secondary icon-picker-btn"
										data-target="icon">
										<i
											class="mdi mdi-palette"></i>
										Choose Icon
									</button>
								</div>
								<div class="icon-preview mt-2"
									id="icon-preview-icon"
									data-name="icon"></div>
								<small class="form-text text-muted">Click
									the button to choose an
									icon</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="mb-3">
								<label class="form-label">Type</label>
								<input type="text" name="type"
									class="form-control"
									value="{{old('type', $link->type)}}"
									placeholder="e.g., social, contact, quick">
							</div>
						</div>
						<div class="col-md-4">
							<div class="mb-3">
								<label class="form-label">Target</label>
								<select name="target"
									class="form-select">
									<option value="_self"
										{{old('target', $link->target) == '_self' ? 'selected' : ''}}>
										Same Window</option>
									<option value="_blank"
										{{old('target', $link->target) == '_blank' ? 'selected' : ''}}>
										New Window</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Order</label>
								<input type="number" name="order"
									class="form-control"
									value="{{old('order', $link->order)}}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Status</label>
								<select name="is_active"
									class="form-select">
									<option value="1"
										{{old('is_active', $link->is_active) ? 'selected' : ''}}>
										Active</option>
									<option value="0"
										{{!old('is_active', $link->is_active) ? 'selected' : ''}}>
										Inactive</option>
								</select>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<button type="submit" class="btn btn-primary">Update
							Link</button>
						<a href="{{route('cms.links.index', [$type, $id])}}"
							class="btn btn-secondary">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('extra-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
	integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
	crossorigin="anonymous" referrerpolicy="no-referrer" />
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

.icon-picker-modal .icon-item i[class*="fa-"] {
	font-family: 'Font Awesome 6 Free' !important;
	font-weight: 900;
}

.icon-preview {
	padding: 8px;
	background: #f8f9fa;
	border-radius: 4px;
	min-height: 30px;
}

.icon-preview i {
	font-size: 20px;
	margin-right: 8px;
}
[data-icon]:before {
	display:none !important;
}
</style>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.getElementById('link_type').addEventListener('change', function() {
	const linkType = this.value;
	const routeField = document.getElementById('route_name_field');
	const linkField = document.getElementById('link_field');
	const routeSelect = document.getElementById('route_name');
	const linkInput = document.getElementById('link');

	if (linkType === 'route') {
		routeField.style.display = 'block';
		linkField.style.display = 'none';
		routeSelect.setAttribute('required', 'required');
		linkInput.removeAttribute('required');
		linkInput.value = '';
	} else {
		routeField.style.display = 'none';
		linkField.style.display = 'block';
		routeSelect.removeAttribute('required');
		linkInput.setAttribute('required', 'required');
		routeSelect.value = '';
	}
});

// Icon Picker
const fontAwesomeIcons = [
	'fas fa-home', 'fas fa-user', 'fas fa-users', 'fas fa-envelope', 'fas fa-phone',
	'fas fa-globe', 'fas fa-link', 'fas fa-share', 'fas fa-share-alt',
	'fab fa-facebook', 'fab fa-facebook-f', 'fab fa-twitter', 'fab fa-instagram',
	'fab fa-linkedin', 'fab fa-youtube', 'fab fa-whatsapp', 'fab fa-telegram',
	'fab fa-skype', 'fab fa-github', 'fab fa-google', 'fab fa-google-plus',
	'fas fa-map-marker-alt', 'fas fa-calendar', 'fas fa-clock', 'fas fa-bell',
	'fas fa-heart', 'fas fa-star', 'fas fa-thumbs-up', 'fas fa-check-circle',
	'fas fa-info-circle', 'fas fa-question-circle', 'fas fa-exclamation-circle',
	'fas fa-arrow-right', 'fas fa-arrow-left', 'fas fa-arrow-up', 'fas fa-arrow-down',
	'fas fa-chevron-right', 'fas fa-chevron-left', 'fas fa-chevron-up', 'fas fa-chevron-down',
	'fas fa-plus', 'fas fa-minus', 'fas fa-edit', 'fas fa-trash', 'fas fa-save',
	'fas fa-download', 'fas fa-upload', 'fas fa-search', 'fas fa-filter',
	'fas fa-cog', 'fas fa-cogs', 'fas fa-wrench', 'fas fa-tools',
	'fas fa-briefcase', 'fas fa-building', 'fas fa-chart-line', 'fas fa-chart-bar',
	'fas fa-shopping-cart', 'fas fa-credit-card', 'fas fa-money-bill',
	'fas fa-graduation-cap', 'fas fa-book', 'fas fa-certificate',
	'fas fa-heartbeat', 'fas fa-hospital', 'fas fa-user-md',
	'fas fa-plane', 'fas fa-car', 'fas fa-train', 'fas fa-ship',
	'fas fa-music', 'fas fa-video', 'fas fa-camera', 'fas fa-image',
	'fas fa-file', 'fas fa-folder', 'fas fa-archive',
	'fas fa-lock', 'fas fa-unlock', 'fas fa-shield-alt', 'fas fa-key',
	'fas fa-wifi', 'fas fa-mobile-alt', 'fas fa-laptop', 'fas fa-desktop'
];

function createIconPickerModal() {
	if ($('#iconPickerModal').length) return;

	let fontAwesomeGridHtml = '<div class="icon-grid" id="fontAwesomeIconsGrid">';
	fontAwesomeIcons.forEach(icon => {
		const iconName = icon.replace(/^(fas|far|fab) fa-/, '').replace(/-/g, ' ');
		fontAwesomeGridHtml += `<div class="icon-item" data-icon="${icon}" title="${iconName}">
			<i class="${icon}"></i>
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
						<div class="tab-content">
							<div class="tab-pane fade show active">
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
	selectedIcon = $('input[name="' + currentIconTarget + '"]').val();

	$('#iconPickerModal .icon-item').removeClass('selected');
	if (selectedIcon) {
		$('#iconPickerModal .icon-item[data-icon="' + selectedIcon + '"]').addClass(
			'selected');
	}

	const modal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
	modal.show();
	$('#iconSearchInput').val('').trigger('input');
});

$(document).on('click', '#iconPickerModal .icon-item', function() {
	$('#iconPickerModal .icon-item').removeClass('selected');
	$(this).addClass('selected');
	selectedIcon = $(this).data('icon');
});

$(document).on('input', '#iconSearchInput', function() {
	const searchTerm = $(this).val().toLowerCase();
	$('#iconPickerModal .icon-item').each(function() {
		const iconName = $(this).data('icon').toLowerCase();
		const iconTitle = $(this).attr('title').toLowerCase();
		if (iconName.includes(searchTerm) || iconTitle.includes(
				searchTerm)) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
});

$(document).on('click', '#confirmIconSelection', function() {
	if (currentIconTarget && selectedIcon) {
		$('input[name="' + currentIconTarget + '"]').val(selectedIcon);
		const previewContainer = $('.icon-preview[data-name="' + currentIconTarget +
			'"]');
		if (previewContainer.length) {
			previewContainer.html('<i class="' + selectedIcon + '"></i> <span>' +
				selectedIcon + '</span>');
		}
	}
	$('#iconPickerModal').modal('hide');
});

// Update preview for existing value
$('.icon-input').each(function() {
	const input = $(this);
	const iconClass = input.val();
	const inputName = input.attr('name');
	if (iconClass && inputName) {
		const previewContainer = $('.icon-preview[data-name="' + inputName + '"]');
		if (previewContainer.length) {
			previewContainer.html('<i class="' + iconClass + '"></i> <span>' +
				iconClass + '</span>');
		}
	}
});
</script>
@endsection