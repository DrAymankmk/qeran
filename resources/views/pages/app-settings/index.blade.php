@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />

@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.settings')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.settings')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-2">
					<div class="col-sm-4">
					</div>
					{{--                        @can('create_categories')--}}

					<div class="col-md-12 col-sm-12">
						<div class="text-sm-start">
						@can('view-app-settings')
							<button type="button"
								class="btn btn-primary waves-effect waves-light"
								data-bs-toggle="modal"
								data-bs-target="#createSettingModal">
								<i class="mdi mdi-plus-circle me-1"></i>
								{{__('admin.add')}}
								{{__('admin.setting')}}
							</button>
						@endcan	
						</div>
					</div><!-- end col-->
					{{--                        @endcan--}}
				</div>
				<div class="table-responsive mt-2">
					<table id="appSettingsTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>

								<th scope="col">{{__('admin.key')}}</th>
								<th scope="col">{{__('admin.type')}}</th>
								<th scope="col">{{__('admin.value')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.more')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($appSettings as $appSetting)

							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$appSetting->id}}</a>
								</td>
								<td>
									{{$appSetting->key}}
								</td>
								<td>
									<span class="badge bg-info">{{$appSetting->type ?? 'text'}}</span>
								</td>
								<td>
									<div class="text-truncate"
										style="max-width: 300px;"
										title="{{strip_tags($appSetting->value)}}">
										{{mb_substr(strip_tags($appSetting->value), 0, 50)}}{{mb_strlen(strip_tags($appSetting->value)) > 50 ? '...' : ''}}
									</div>
								</td>

								<td>
									{{Carbon\Carbon::parse($appSetting->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">

									@can('view-app-settings')
										<a href="javascript:void(0);"
											onclick="openEditModal({{json_encode($appSetting->key)}}, {{json_encode($appSetting->value)}}, {{json_encode($appSetting->type ?? 'text')}})"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>

									@endcan
									@can('delete-app-settings')
										<a onclick="openModalDelete({{$appSetting->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>
									@endcan			
									</div>
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
<!-- end row -->
@endsection

@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<!-- TinyMCE Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.9/tinymce.min.js"></script>

<!-- init js -->
<script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

<script src="{{asset('admin_assets/js/jquery.printPage.js') }}"></script>

<script src="{{asset('admin_assets/js/print.js') }}"></script>

<script>
$('.btnprn').printPage();
</script>

<script>
// Global variables for TinyMCE instances
let createEditorInstance = null;
let editEditorInstance = null;

// Global function to show appropriate input based on type in edit modal
function showEditInputByType(type, value) {
	// Hide and disable all inputs
	$('.edit-value-input').hide().prop('disabled', true);
	
	// Remove TinyMCE if exists
	if (editEditorInstance) {
		tinymce.remove('#edit_value_editor');
		editEditorInstance = null;
	}

	// Show and enable appropriate input and set value
	if (type === 'text') {
		$('#edit_value_text').show().prop('disabled', false).val(value || '');
	} else if (type === 'number') {
		$('#edit_value_number').show().prop('disabled', false).val(value || '');
	} else if (type === 'email') {
		$('#edit_value_email').show().prop('disabled', false).val(value || '');
	} else if (type === 'textarea') {
		$('#edit_value_textarea').show().prop('disabled', false).val(value || '');
	} else if (type === 'editor') {
		$('#edit_value_editor').show().prop('disabled', false);
		// Initialize TinyMCE for editor
		if (typeof tinymce !== 'undefined') {
			tinymce.init({
				selector: '#edit_value_editor',
				height: 400,
				menubar: false,
				plugins: 'lists link table code',
				toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
				content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
				branding: false,
				promotion: false,
				setup: function(editor) {
					editor.on('init', function() {
						if (value) {
							editor.setContent(value);
						}
					});
				}
			});
			editEditorInstance = tinymce.get('edit_value_editor');
		}
	}
}

$(document).ready(function() {
	// Initialize DataTable using reusable function
	var table = initAdminDataTable({
		tableId: '#appSettingsTable',
		pdfRoute: '{{route("app-settings.export.pdf")}}',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1,
			5
		], // Key and Actions columns are not orderable
		nonSearchableColumns: [
			5
		], // Only Actions column is not searchable (Key, Type and Value should be searchable)
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		],
		hasPdf: false,
	});

	// Handle type change in create modal
	$('#create_type').on('change', function() {
		const type = $(this).val();
		showCreateInputByType(type);
	});

	// Function to show appropriate input based on type
	function showCreateInputByType(type) {
		// Hide and disable all inputs
		$('.create-value-input').hide().prop('disabled', true);
		
		// Remove TinyMCE if exists
		if (createEditorInstance) {
			tinymce.remove('#create_value_editor');
			createEditorInstance = null;
		}

		// Show and enable appropriate input
		if (type === 'text') {
			$('#create_value_text').show().prop('disabled', false);
		} else if (type === 'number') {
			$('#create_value_number').show().prop('disabled', false);
		} else if (type === 'email') {
			$('#create_value_email').show().prop('disabled', false);
		} else if (type === 'textarea') {
			$('#create_value_textarea').show().prop('disabled', false);
		} else if (type === 'editor') {
			$('#create_value_editor').show().prop('disabled', false);
			// Initialize TinyMCE for editor
			if (typeof tinymce !== 'undefined') {
				tinymce.init({
					selector: '#create_value_editor',
					height: 400,
					menubar: false,
					plugins: 'lists link table code',
					toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
					content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
					branding: false,
					promotion: false
				});
				createEditorInstance = tinymce.get('create_value_editor');
			}
		}
	}

	// Initialize TinyMCE when create modal is shown
	$('#createSettingModal').on('shown.bs.modal', function() {
		// Show default input (text)
		const defaultType = $('#create_type').val() || 'text';
		showCreateInputByType(defaultType);
	});

	// Initialize when edit modal is shown
	$('#editSettingModal').on('shown.bs.modal', function() {
		// Get type and value from data attributes
		const type = $('#editSettingModal').data('edit-type') || 'text';
		const value = $('#editSettingModal').data('edit-value') || '';
		showEditInputByType(type, value);
	});

	// Handle create form submission
	$('#createSettingForm').on('submit', function(e) {
		e.preventDefault();

		const form = $(this);
		const submitBtn = form.find('button[type="submit"]');
		const originalText = submitBtn.html();

		// Get content from appropriate input based on type
		const type = $('#create_type').val();
		let valueContent = '';
		
		if (type === 'editor') {
			// Get content from TinyMCE
			if (createEditorInstance) {
				createEditorInstance.save();
				valueContent = $('#create_value_editor').val() || '';
			} else if (typeof tinymce !== 'undefined' && tinymce.get('create_value_editor')) {
				tinymce.get('create_value_editor').save();
				valueContent = $('#create_value_editor').val() || '';
			}
		} else {
			// Get value from visible input
			const visibleInput = $('.create-value-input:visible');
			valueContent = visibleInput.val() || '';
		}

		// Validate value field
		const trimmedValue = valueContent.replace(/<[^>]*>/g, '').trim();

		if (!trimmedValue) {
			const valueField = $('.create-value-input:visible');
			valueField.addClass('is-invalid');
			valueField.siblings('.invalid-feedback').text('The value field is required.');
			submitBtn.prop('disabled', false).html(originalText);
			return false;
		}

		// Disable submit button
		submitBtn.prop('disabled', true).html(
			'<span class="spinner-border spinner-border-sm me-1"></span> {{__("admin.save")}}...'
		);

		// Clear previous errors
		form.find('.is-invalid').removeClass('is-invalid');
		form.find('.invalid-feedback').text('');

		$.ajax({
			url: '{{ route("app-settings.store") }}',
			type: 'POST',
			data: form.serialize(),
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			success: function(response) {
				if (response
					.success
				) {
					// Show success message


					// Close modal and reset form
					$('#createSettingModal')
						.modal(
							'hide'
						);
					form[0]
						.reset();

					// Reload page to show new setting
					location
						.reload();
				}
			},
			error: function(xhr) {
				if (xhr.status ===
					422
				) {
					// Validation errors
					const errors =
						xhr
						.responseJSON
						.errors;
					$.each(errors, function(key,
						value
					) {
						const input =
							form
							.find('[name="' +
								key +
								'"]'
							);
						input.addClass(
							'is-invalid'
						);
						input.siblings(
								'.invalid-feedback'
							)
							.text(value[
								0
							]);
					});
				} else {
					alert(
						'Error saving setting'
					);
				}
			},
			complete: function() {
				// Re-enable submit button
				submitBtn.prop('disabled',
						false
					)
					.html(
						originalText
					);
			}
		});
	});

	// Handle edit form submission
	$('#editSettingForm').on('submit', function(e) {
		e.preventDefault();

		const form = $(this);
		const submitBtn = form.find('button[type="submit"]');
		const originalText = submitBtn.html();
		const key = $('#edit_key').val();

		// Get content from appropriate input based on type
		const type = $('#edit_type_display').data('type') || 'text';
		let valueContent = '';
		
		if (type === 'editor') {
			// Get content from TinyMCE
			if (editEditorInstance) {
				editEditorInstance.save();
				valueContent = $('#edit_value_editor').val() || '';
			} else if (typeof tinymce !== 'undefined' && tinymce.get('edit_value_editor')) {
				tinymce.get('edit_value_editor').save();
				valueContent = $('#edit_value_editor').val() || '';
			}
		} else {
			// Get value from visible input
			const visibleInput = $('.edit-value-input:visible');
			valueContent = visibleInput.val() || '';
		}

		// Validate value field
		const trimmedValue = valueContent.replace(/<[^>]*>/g, '').trim();

		if (!trimmedValue) {
			const valueField = $('.edit-value-input:visible');
			valueField.addClass('is-invalid');
			valueField.siblings('.invalid-feedback').text('The value field is required.');
			submitBtn.prop('disabled', false).html(originalText);
			return false;
		}

		// Disable submit button
		submitBtn.prop('disabled', true).html(
			'<span class="spinner-border spinner-border-sm me-1"></span> {{__("admin.update")}}...'
		);

		// Clear previous errors
		form.find('.is-invalid').removeClass('is-invalid');
		form.find('.invalid-feedback').text('');

		$.ajax({
			url: '{{ route("app-settings.update", ":key") }}'
				.replace(':key',
					key),
			type: 'POST',
			data: form.serialize(),
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			success: function(response) {
				if (response
					.success
				) {

					// Close modal
					$('#editSettingModal')
						.modal(
							'hide'
						);

					// Reload page to show updated setting
					location
						.reload();
				}
			},
			error: function(xhr) {
				if (xhr.status ===
					422
				) {
					// Validation errors
					const errors =
						xhr
						.responseJSON
						.errors;
					$.each(errors, function(key,
						value
					) {
						const input =
							form
							.find('[name="' +
								key +
								'"]'
							);
						input.addClass(
							'is-invalid'
						);
						input.siblings(
								'.invalid-feedback'
							)
							.text(value[
								0
							]);
					});
				} else {
					alert(
						'Error updating setting'
					);
				}
			},
			complete: function() {
				// Re-enable submit button
				submitBtn.prop('disabled',
						false
					)
					.html(
						originalText
					);
			}
		});
	});

	// Reset form when modal is closed
	$('#createSettingModal').on('hidden.bs.modal', function() {
		// Destroy TinyMCE instance
		if (createEditorInstance) {
			tinymce.remove('#create_value_editor');
			createEditorInstance = null;
		}
		$('#createSettingForm')[0].reset();
		$('#createSettingForm').find('.is-invalid').removeClass('is-invalid');
		$('#createSettingForm').find('.invalid-feedback').text('');
		// Hide all inputs
		$('.create-value-input').hide();
	});

	$('#editSettingModal').on('hidden.bs.modal', function() {
		// Destroy TinyMCE instance
		if (editEditorInstance) {
			tinymce.remove('#edit_value_editor');
			editEditorInstance = null;
		}
		$('#editSettingForm').find('.is-invalid').removeClass('is-invalid');
		$('#editSettingForm').find('.invalid-feedback').text('');
		// Hide all inputs
		$('.edit-value-input').hide().prop('disabled', true);
		// Clear data attributes
		$('#editSettingModal').removeData('edit-type');
		$('#editSettingModal').removeData('edit-value');
	});
});

// Function to open edit modal
function openEditModal(key, value, type) {
	$('#edit_key').val(key);
	$('#edit_key_display').val(key);
	$('#edit_type_display').val(type || 'text');
	$('#edit_type_display').data('type', type || 'text');

	// Store type and value in modal data attributes
	$('#editSettingModal').data('edit-type', type || 'text');
	$('#editSettingModal').data('edit-value', value || '');

	$('#editSettingModal').modal('show');
}

function openModalDelete(settingId) {
	// Note: Delete functionality requires a destroy route to be added
	// For now, this function is kept for compatibility but won't work without a destroy route
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		// If destroy route exists, uncomment and adjust:
		const baseUrl = "{{route('app-settings.destroy', 999)}}".replace('/999', '');
		form.action = baseUrl + '/' + settingId;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	modal.show();
}
</script>

@endsection
@section('modal')
<!-- Create Setting Modal -->
<div class="modal fade" id="createSettingModal" tabindex="-1" aria-labelledby="createSettingModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createSettingModalLabel">{{__('admin.add')}}
					{{__('admin.setting')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="createSettingForm">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="create_key"
							class="form-label">{{__('admin.key')}} <span
								class="text-danger">*</span></label>
						<input type="text" class="form-control" id="create_key"
							name="key" required>
						<div class="invalid-feedback"></div>
					</div>
					<div class="mb-3">
						<label for="create_type"
							class="form-label">{{__('admin.type')}} <span
								class="text-danger">*</span></label>
						<select class="form-select" id="create_type" name="type" required>
							<option value="text">{{__('admin.text')}}</option>
							<option value="number">{{__('admin.number')}}</option>
							<option value="email">{{__('admin.email')}}</option>
							<option value="textarea">{{__('admin.textarea')}}</option>
							<option value="editor">{{__('admin.editor')}}</option>
						</select>
						<div class="invalid-feedback"></div>
					</div>
					<div class="mb-3">
						<label for="create_value"
							class="form-label">{{__('admin.value')}} <span
								class="text-danger">*</span></label>
						<!-- Text Input -->
						<input type="text" class="form-control create-value-input" id="create_value_text"
							name="value" style="display: none;">
						<!-- Number Input -->
						<input type="number" class="form-control create-value-input" id="create_value_number"
							name="value" style="display: none;">
						<!-- Email Input -->
						<input type="email" class="form-control create-value-input" id="create_value_email"
							name="value" style="display: none;">
						<!-- Textarea Input -->
						<textarea class="form-control create-value-input" id="create_value_textarea"
							name="value" rows="5" style="display: none;"></textarea>
						<!-- Editor Input -->
						<textarea class="form-control create-value-input" id="create_value_editor"
							name="value" rows="10" style="display: none;"></textarea>
						<div class="invalid-feedback"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">{{__('admin.cancel')}}</button>
					<button type="submit"
						class="btn btn-primary">{{__('admin.save')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1" aria-labelledby="editSettingModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editSettingModalLabel">{{__('admin.edit')}}
					{{__('admin.setting')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="editSettingForm">
				@csrf
				<input type="hidden" id="edit_key" name="key">
				<div class="modal-body">
					<div class="mb-3">
						<label for="edit_key_display"
							class="form-label">{{__('admin.key')}}</label>
						<input type="text" class="form-control"
							id="edit_key_display" disabled>
					</div>
					<div class="mb-3">
						<label for="edit_type_display"
							class="form-label">{{__('admin.type')}}</label>
						<input type="text" class="form-control"
							id="edit_type_display" disabled>
					</div>
					<div class="mb-3">
						<label for="edit_value"
							class="form-label">{{__('admin.value')}} <span
								class="text-danger">*</span></label>
						<!-- Text Input -->
						<input type="text" class="form-control edit-value-input" id="edit_value_text"
							name="value" style="display: none;">
						<!-- Number Input -->
						<input type="number" class="form-control edit-value-input" id="edit_value_number"
							name="value" style="display: none;">
						<!-- Email Input -->
						<input type="email" class="form-control edit-value-input" id="edit_value_email"
							name="value" style="display: none;">
						<!-- Textarea Input -->
						<textarea class="form-control edit-value-input" id="edit_value_textarea"
							name="value" rows="5" style="display: none;"></textarea>
						<!-- Editor Input -->
						<textarea class="form-control edit-value-input" id="edit_value_editor"
							name="value" rows="10" style="display: none;"></textarea>
						<div class="invalid-feedback"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">{{__('admin.cancel')}}</button>
					<button type="submit"
						class="btn btn-primary">{{__('admin.update')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@component('layouts.includes.modal')
@slot('modalID')
deleteModal
@endslot
@slot('modalTitle')
{{__('admin.delete-data')}}
@endslot
@slot('modalMethodPutOrDelete')
@method('delete')
@endslot
@slot('modalContent')
<div class="text-center">
	<span class="text-danger font-16">
		{{__('admin.delete-message-confirm')}}
	</span>
</div>
@endslot
@endcomponent
@endsection
