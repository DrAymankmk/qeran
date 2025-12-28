@extends('layouts.app')

@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
	type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet"
	type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('translations.manage-translations')}}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">
						{{__('translations.manage-translations')}}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<!-- Language Tabs -->
				<ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
					@foreach($availableLocales as $loc)
					<li class="nav-item">
						<a class="nav-link {{$locale == $loc ? 'active' : ''}}"
							href="{{route('admin.translations.index', ['locale' => $loc, 'file' => $file])}}"
							aria-selected="{{$locale == $loc ? 'true' : 'false'}}">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-home-variant"></i></span>
							<span
								class="d-none d-sm-block">{{strtoupper($loc)}}</span>
						</a>
					</li>
					@endforeach
				</ul>

				<!-- Filters and Actions -->
				<div class="row mb-3">
					<div class="col-md-12">
						<div
							class="d-flex justify-content-between align-items-center flex-wrap gap-2">
							<div class="d-flex gap-2 flex-wrap">
								<!-- File Filter -->
								<div class="mb-2"
									style="min-width: 200px;">
									<label
										class="form-label mb-1">{{__('translations.file')}}</label>
									<select id="fileFilter"
										class="form-select form-select-sm">
										<option value="">
											{{__('translations.all-files')}}
										</option>
										@foreach($availableFiles as $f)
										<option value="{{$f}}"
											{{$file == $f ? 'selected' : ''}}>
											{{$f}}
										</option>
										@endforeach
									</select>
								</div>
							</div>
							<div>
								<button type="button"
									class="btn btn-primary btn-rounded waves-effect waves-light"
									data-bs-toggle="modal"
									data-bs-target="#createTranslationModal">
									<i
										class="mdi mdi-plus me-1"></i>
									{{__('translations.add-new-key')}}
								</button>
							</div>
						</div>
					</div>
				</div>

				@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close"
						data-bs-dismiss="alert"></button>
				</div>
				@endif

				@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ session('error') }}
					<button type="button" class="btn-close"
						data-bs-dismiss="alert"></button>
				</div>
				@endif

				<!-- Translations DataTable -->
				<div class="table-responsive">
					<table id="translationsTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">
									{{__('translations.file')}}
								</th>
								<th scope="col">
									{{__('translations.key')}}
								</th>
								<th scope="col">
									{{__('translations.value')}}
								</th>
								<th scope="col">
									{{__('translations.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($allTranslations as $translation)
							<tr data-file="{{$translation['file']}}">
								<td>
									<span
										class="badge bg-info">{{$translation['file']}}.php</span>
								</td>
								<td>
									<code
										class="text-primary">{{$translation['key']}}</code>
								</td>
								<td>
									<div class="text-truncate"
										style="max-width: 400px;"
										title="{{$translation['value']}}">
										{{$translation['value']}}
									</div>
								</td>
								<td>
									<button type="button"
										class="btn btn-sm btn-primary edit-translation-btn"
										data-locale="{{$locale}}"
										data-file="{{$translation['file']}}"
										data-key="{{$translation['key']}}"
										title="{{__('translations.edit')}}">
										<i
											class="mdi mdi-pencil"></i>
									</button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('extra-js')
@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<script>
$(document).ready(function() {
	// Custom filter function for file column
	$.fn.dataTable.ext.search.push(
		function(settings, data, dataIndex) {
			var selectedFile = $('#fileFilter').val();
			if (!selectedFile || selectedFile === '') {
				return true; // Show all if no filter selected
			}

			// Get the row and check data-file attribute
			var row = $('#translationsTable').DataTable().row(dataIndex)
				.node();
			var rowFile = $(row).attr('data-file');

			return rowFile === selectedFile;
		}
	);

	// Initialize DataTable
	var table = initAdminDataTable({
		tableId: '#translationsTable',
		orderColumn: 0,
		orderDirection: 'asc',
		nonOrderableColumns: [3], // Actions column
		nonSearchableColumns: [3], // Actions column
		pageLength: 25,
		lengthMenu: [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "{{__('admin.all')}}"]
		],
		buttons: ['copy', 'excel', 'pdf', 'print']
	});

	// File filter functionality
	$('#fileFilter').on('change', function() {
		table.draw();
	});
});

// Handle Create Modal
$('#createTranslationModal').on('show.bs.modal', function() {
	// Reset form
	$('#createTranslationForm')[0].reset();
	$('#createTranslationForm .invalid-feedback').text('');
	$('#createTranslationForm .is-invalid').removeClass('is-invalid');

	// Set default locale and file
	$('#create_locale').val('{{$locale}}');
	$('#create_file').val('{{$file ?: "cms"}}');
});

// Handle Create Form Submit
$('#createTranslationForm').on('submit', function(e) {
	e.preventDefault();

	var form = $(this);
	var submitBtn = form.find('button[type="submit"]');
	var originalText = submitBtn.html();

	// Clear previous errors
	form.find('.invalid-feedback').text('');
	form.find('.is-invalid').removeClass('is-invalid');

	// Disable submit button
	submitBtn.prop('disabled', true).html(
		'<i class="mdi mdi-loading mdi-spin me-1"></i>{{__("translations.saving")}}...'
		);

	$.ajax({
		url: '{{route("admin.translations.store")}}',
		method: 'POST',
		data: form.serialize(),
		success: function(response) {
			// Show success message
			showAlert('success', response.message ||
				'{{__("translations.translation-key-added-successfully")}}'
				);

			// Close modal
			$('#createTranslationModal').modal(
				'hide');

			// Reload page after a short delay
			setTimeout(function() {
				window.location
					.reload();
			}, 1000);
		},
		error: function(xhr) {
			submitBtn.prop('disabled', false).html(
				originalText);

			if (xhr.status === 422) {
				// Validation errors
				var errors = xhr.responseJSON
					.errors || {};
				$.each(errors, function(field,
					messages) {
					var input =
						form
						.find('[name="' +
							field +
							'"]'
							);
					input.addClass(
						'is-invalid');
					input.siblings(
							'.invalid-feedback')
						.text(messages[
							0]);
				});
			} else {
				// Other errors
				var errorMsg = xhr
					.responseJSON
					?.error || xhr
					.responseJSON
					?.message ||
					'{{__("translations.an-error-occurred")}}';
				showAlert('danger', errorMsg);
			}
		}
	});
});

// Handle Edit Button Click
$(document).on('click', '.edit-translation-btn', function() {
	var locale = $(this).data('locale');
	var file = $(this).data('file');
	var key = $(this).data('key');

	// Show loading
	$('#editTranslationForm').find('textarea[name="value"]').val('Loading...');
	$('#editTranslationModal').modal('show');

	// Fetch translation data
	var editUrl =
		'{{route("admin.translations.edit", ["locale" => ":locale", "file" => ":file", "key" => ":key"])}}'
		.replace(':locale', locale)
		.replace(':file', file)
		.replace(':key', encodeURIComponent(key));

	$.ajax({
		url: editUrl,
		method: 'GET',
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'application/json'
		},
		success: function(response) {
			$('#edit_locale').val(response.locale);
			$('#edit_file').val(response.file);
			$('#edit_key').val(response.key);
			$('#edit_key_display').val(response
			.key);
			$('#edit_file_display').val(response
				.file + '.php');
			$('#edit_locale_display').val(response
				.locale
				.toUpperCase());
			$('#edit_value').val(response.value);
			var updateUrl =
				'{{route("admin.translations.update", ["locale" => ":locale", "file" => ":file", "key" => ":key"])}}'
				.replace(':locale', response
					.locale)
				.replace(':file', response
					.file)
				.replace(':key',
					encodeURIComponent(
						response
						.key));
			$('#editTranslationForm').attr('action',
				updateUrl);
		},
		error: function(xhr) {
			var errorMsg = xhr.responseJSON
				?.error ||
				'{{__("translations.failed-to-load-translation")}}';
			showAlert('danger', errorMsg);
			$('#editTranslationModal').modal(
			'hide');
		}
	});
});

// Handle Edit Form Submit
$('#editTranslationForm').on('submit', function(e) {
	e.preventDefault();

	var form = $(this);
	var submitBtn = form.find('button[type="submit"]');
	var originalText = submitBtn.html();
	var url = form.attr('action');

	// Clear previous errors
	form.find('.invalid-feedback').text('');
	form.find('.is-invalid').removeClass('is-invalid');

	// Disable submit button
	submitBtn.prop('disabled', true).html(
		'<i class="mdi mdi-loading mdi-spin me-1"></i>{{__("translations.updating")}}...'
		);

	$.ajax({
		url: url,
		method: 'PUT',
		data: form.serialize(),
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'application/json'
		},
		success: function(response) {
			// Show success message
			showAlert('success', response.message ||
				'{{__("translations.translation-updated-successfully")}}'
				);

			// Close modal
			$('#editTranslationModal').modal(
			'hide');

			// Reload page after a short delay
			setTimeout(function() {
				window.location
					.reload();
			}, 1000);
		},
		error: function(xhr) {
			submitBtn.prop('disabled', false).html(
				originalText);

			if (xhr.status === 422) {
				// Validation errors
				var errors = xhr.responseJSON
					.errors || {};
				$.each(errors, function(field,
					messages) {
					var input =
						form
						.find('[name="' +
							field +
							'"]'
							);
					input.addClass(
						'is-invalid');
					input.siblings(
							'.invalid-feedback')
						.text(messages[
							0]);
				});
			} else {
				// Other errors
				var errorMsg = xhr
					.responseJSON
					?.error || xhr
					.responseJSON
					?.message ||
					'{{__("translations.an-error-occurred")}}';
				showAlert('danger', errorMsg);
			}
		}
	});
});

// Helper function to show alerts
function showAlert(type, message) {
	var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
		message +
		'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
		'</div>';

	// Remove existing alerts
	$('.alert').remove();

	// Add new alert at the top of card body
	$('.card-body').prepend(alertHtml);

	// Auto-dismiss after 5 seconds
	setTimeout(function() {
		$('.alert').fadeOut(function() {
			$(this).remove();
		});
	}, 5000);
}
</script>
@endsection

<!-- Create Translation Modal -->
<div class="modal fade" id="createTranslationModal" tabindex="-1" aria-labelledby="createTranslationModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createTranslationModalLabel">
					{{__('translations.add-new-key')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="createTranslationForm">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">{{__('translations.locale')}}
							<span class="text-danger">*</span></label>
						<select name="locale" id="create_locale" class="form-select"
							required>
							@foreach($availableLocales as $loc)
							<option value="{{$loc}}"
								{{$locale == $loc ? 'selected' : ''}}>
								{{strtoupper($loc)}}</option>
							@endforeach
						</select>
						<div class="invalid-feedback"></div>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.file')}} <span
								class="text-danger">*</span></label>
						<select name="file" id="create_file" class="form-select"
							required>
							@foreach($availableFiles as $f)
							<option value="{{$f}}"
								{{($file ?: 'cms') == $f ? 'selected' : ''}}>
								{{$f}}.php</option>
							@endforeach
						</select>
						<div class="invalid-feedback"></div>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.key')}} <span
								class="text-danger">*</span></label>
						<input type="text" name="key" class="form-control"
							placeholder="e.g., new-key or nested.key.name"
							required>
						<small
							class="form-text text-muted">{{__('translations.key-hint')}}</small>
						<div class="invalid-feedback"></div>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.value')}} <span
								class="text-danger">*</span></label>
						<textarea name="value" class="form-control" rows="5"
							required></textarea>
						<div class="invalid-feedback"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">{{__('translations.cancel')}}</button>
					<button type="submit"
						class="btn btn-primary">{{__('translations.save')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit Translation Modal -->
<div class="modal fade" id="editTranslationModal" tabindex="-1" aria-labelledby="editTranslationModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editTranslationModalLabel">
					{{__('translations.edit-translation')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="editTranslationForm" method="POST">
				@csrf
				@method('PUT')
				<input type="hidden" id="edit_locale" name="locale">
				<input type="hidden" id="edit_file" name="file">
				<input type="hidden" id="edit_key" name="key">
				<div class="modal-body">
					<div class="mb-3">
						<label
							class="form-label">{{__('translations.locale')}}</label>
						<input type="text" id="edit_locale_display"
							class="form-control" readonly>
					</div>

					<div class="mb-3">
						<label
							class="form-label">{{__('translations.file')}}</label>
						<input type="text" id="edit_file_display"
							class="form-control" readonly>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.key')}}</label>
						<input type="text" id="edit_key_display"
							class="form-control" readonly>
					</div>

					<div class="mb-3">
						<label class="form-label">{{__('translations.value')}} <span
								class="text-danger">*</span></label>
						<textarea name="value" id="edit_value" class="form-control"
							rows="5" required></textarea>
						<div class="invalid-feedback"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">{{__('translations.cancel')}}</button>
					<button type="submit"
						class="btn btn-primary">{{__('translations.update')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>