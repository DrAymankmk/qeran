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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.promo-codes')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.promo-codes')}}</li>
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


					<div class="col-sm-12">
						<div class="text-sm-start">
						@can('create-promo-codes')
							<button type="button"
								class="btn btn-primary waves-effect waves-light"
								data-bs-toggle="modal"
								data-bs-target="#createPromoCodeModal">
								<i class="mdi mdi-plus-circle me-1"></i>
								{{__('admin.add')}}
								{{__('admin.promo-code')}}
							</button>
						@endcan	
						</div>
					</div><!-- end col-->
				</div>
				<div class="table-responsive mt-2">
					<table id="promoCodesTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.name')}}
								</th>
								<th scope="col">{{__('admin.code')}}
								</th>
								<th scope="col">{{__('admin.discount')}}
									(%)</th>
								<th scope="col">{{__('admin.package')}}
								</th>
								<th scope="col">
									{{__('admin.valid-date')}}
								</th>
								<th scope="col">
									{{__('admin.expire-date')}}
								</th>
								<th scope="col">{{__('admin.status')}}
								</th>
								<th scope="col">{{__('admin.usage')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($promoCodes as $promoCode)

							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$promoCode->id}}</a>
								</td>
								<td>{{$promoCode->name}}</td>
								<td><span>{{$promoCode->code}}</span>
								</td>
								<td>{{$promoCode->discount_percentage}}%
								</td>
								<td>
									@if($promoCode->package_id)
									{{$promoCode->package->id ?? __('admin.package')}}
									#{{$promoCode->package_id}}
									@else
									<span
										class="badge bg-info">{{__('admin.all-packages')}}</span>
									@endif
								</td>
								<td>{{Carbon\Carbon::parse($promoCode->valid_date)->format('Y-m-d')}}
								</td>
								<td>{{Carbon\Carbon::parse($promoCode->expire_date)->format('Y-m-d')}}
								</td>
								<td>
									@if($promoCode->is_active)
									<span
										class="badge bg-success">{{__('admin.active')}}</span>
									@else
									<span
										class="badge bg-danger">{{__('admin.inactive')}}</span>
									@endif
								</td>
								<td>
									@if($promoCode->usage_limit)
									{{$promoCode->used_count}} /
									{{$promoCode->usage_limit}}
									@else
									{{$promoCode->used_count}} /
									{{__('admin.unlimited')}}
									@endif
								</td>
								<td>
									{{Carbon\Carbon::parse($promoCode->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
									@can('edit-promo-codes')
										<a href="javascript:void(0);"
											onclick="openEditModal({{$promoCode->id}})"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>
									@endcan
									@can('delete-promo-codes')
										<a onclick="openModalDelete({{$promoCode->id}})"
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

<script>
$(document).ready(function() {
	// Initialize DataTable using reusable function
	var table = initAdminDataTable({
		tableId: '#promoCodesTable',
		pdfRoute: '{{route("promo-code.export.pdf")}}',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [4, 7, 8, 10],
		nonSearchableColumns: [10],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		]
	});

	// Handle create form submission
	$('#createPromoCodeForm').on('submit', function(e) {
		e.preventDefault();

		const form = $(this);
		const submitBtn = form.find('button[type="submit"]');
		const originalText = submitBtn.html();

		// Disable submit button
		submitBtn.prop('disabled', true).html(
			'<span class="spinner-border spinner-border-sm me-1"></span> {{__("admin.save")}}...'
		);

		// Clear previous errors
		form.find('.is-invalid').removeClass('is-invalid');
		form.find('.invalid-feedback').text('');

		// Ensure is_active is always sent (0 if unchecked, 1 if checked)
		// Remove existing is_active from serialized data if present, then add the correct value
		let formData = form.serialize();
		// Remove is_active parameter (handles both at start and middle/end)
		formData = formData.replace(/(^|&)is_active=\d*/g, '')
			.replace(/^&+/, '');
		const isActive = $('#create_is_active').is(':checked') ?
			'1' : '0';
		formData += (formData ? '&' : '') + 'is_active=' + isActive;

		$.ajax({
			url: '{{ route("promo-code.store") }}',
			type: 'POST',
			data: formData,
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			success: function(response) {
				if (response
					.success
				) {
					toastr.success(
						'{{__("admin.created-successfully")}}'
						);
					$('#createPromoCodeModal')
						.modal(
							'hide'
						);
					form[0]
						.reset();
					location
						.reload();
				}
			},
			error: function(xhr) {
				if (xhr.status ===
					422
				) {
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
					toastr.error(
						'{{__("admin.error-saving")}}'
						);
				}
			},
			complete: function() {
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
	$('#editPromoCodeForm').on('submit', function(e) {
		e.preventDefault();

		const form = $(this);
		const submitBtn = form.find('button[type="submit"]');
		const originalText = submitBtn.html();
		const promoCodeId = $('#edit_promo_code_id').val();

		// Disable submit button
		submitBtn.prop('disabled', true).html(
			'<span class="spinner-border spinner-border-sm me-1"></span> {{__("admin.update")}}...'
		);

		// Clear previous errors
		form.find('.is-invalid').removeClass('is-invalid');
		form.find('.invalid-feedback').text('');

		// Ensure is_active is always sent (0 if unchecked, 1 if checked)
		// Remove existing is_active from serialized data if present, then add the correct value
		let formData = form.serialize();
		// Remove is_active parameter (handles both at start and middle/end)
		formData = formData.replace(/(^|&)is_active=\d*/g, '')
			.replace(/^&+/, '');
		const isActive = $('#edit_is_active').is(':checked') ? '1' :
			'0';
		formData += (formData ? '&' : '') +
			'_method=PUT&is_active=' + isActive;

		$.ajax({
			url: '{{ route("promo-code.update", ":id") }}'
				.replace(':id',
					promoCodeId
				),
			type: 'POST',
			data: formData,
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			success: function(response) {
				if (response
					.success
				) {
					toastr.success(
						'{{__("admin.updated-successfully")}}'
						);
					$('#editPromoCodeModal')
						.modal(
							'hide'
						);
					location
						.reload();
				}
			},
			error: function(xhr) {
				if (xhr.status ===
					422
				) {
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
					toastr.error(
						'{{__("admin.error-updating")}}'
						);
				}
			},
			complete: function() {
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
	$('#createPromoCodeModal').on('hidden.bs.modal', function() {
		$('#createPromoCodeForm')[0].reset();
		$('#createPromoCodeForm').find('.is-invalid').removeClass(
			'is-invalid');
		$('#createPromoCodeForm').find('.invalid-feedback').text(
			'');
		$('#create_package_id').val('').trigger('change');
	});

	$('#editPromoCodeModal').on('hidden.bs.modal', function() {
		$('#editPromoCodeForm').find('.is-invalid').removeClass(
			'is-invalid');
		$('#editPromoCodeForm').find('.invalid-feedback').text('');
	});

	// Handle package selection change for create form
	$('#create_package_type').on('change', function() {
		const packageType = $(this).val();
		const packageWrapper = $('#create_package_wrapper');

		if (packageType === 'all') {
			packageWrapper.hide();
			$('#create_package_id').val('');
		} else {
			packageWrapper.show();
		}
	});

	// Handle package selection change for edit form
	$('#edit_package_type').on('change', function() {
		const packageType = $(this).val();
		const packageWrapper = $('#edit_package_wrapper');

		if (packageType === 'all') {
			packageWrapper.hide();
			$('#edit_package_id').val('');
		} else {
			packageWrapper.show();
		}
	});
});

// Function to open edit modal
function openEditModal(promoCodeId) {
	$.ajax({
		url: '{{ route("promo-code.show", ":id") }}'.replace(':id', promoCodeId),
		type: 'GET',
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		},
		success: function(response) {
			if (response.success) {
				const data = response.data;
				$('#edit_promo_code_id').val(data.id);
				$('#edit_name').val(data.name);
				$('#edit_code').val(data.code);

				// Format dates for date input (YYYY-MM-DD)
				if (data.valid_date) {
					// Handle different date formats
					let validDateStr = data.valid_date;
					// If it's already in YYYY-MM-DD format, use it directly
					if (!/^\d{4}-\d{2}-\d{2}$/.test(
							validDateStr)) {
						// Parse and format the date
						const validDate = new Date(
							validDateStr
						);
						if (!isNaN(validDate
								.getTime()
								)) {
							validDateStr =
								validDate
								.getFullYear() +
								'-' +
								String(validDate
									.getMonth() +
									1
								)
								.padStart(2,
									'0'
								) +
								'-' +
								String(validDate
									.getDate()
								)
								.padStart(2,
									'0'
								);
						}
					}
					$('#edit_valid_date').val(validDateStr);
				}

				if (data.expire_date) {
					// Handle different date formats
					let expireDateStr = data.expire_date;
					// If it's already in YYYY-MM-DD format, use it directly
					if (!/^\d{4}-\d{2}-\d{2}$/.test(
							expireDateStr)) {
						// Parse and format the date
						const expireDate = new Date(
							expireDateStr
						);
						if (!isNaN(expireDate
								.getTime()
								)) {
							expireDateStr =
								expireDate
								.getFullYear() +
								'-' +
								String(expireDate
									.getMonth() +
									1
								)
								.padStart(2,
									'0'
								) +
								'-' +
								String(expireDate
									.getDate()
								)
								.padStart(2,
									'0'
								);
						}
					}
					$('#edit_expire_date').val(
						expireDateStr);
				}

				$('#edit_discount_percentage').val(data
					.discount_percentage);
				$('#edit_usage_limit').val(data.usage_limit);
				$('#edit_is_active').prop('checked', data
					.is_active == 1 || data
					.is_active === true);

				// Handle package selection
				if (data.package_id) {
					$('#edit_package_type').val('specific')
						.trigger('change');
					$('#edit_package_id').val(data
							.package_id)
						.trigger('change');
				} else {
					$('#edit_package_type').val('all')
						.trigger('change');
					$('#edit_package_id').val('');
				}

				$('#editPromoCodeModal').modal('show');
			}
		},
		error: function() {
			toastr.error('{{__("admin.error-loading-data")}}');
		}
	});
}

function openModalDelete(promoCodeId) {
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		const baseUrl = "{{route('promo-code.destroy', 999)}}".replace('/999', '');
		form.action = baseUrl + '/' + promoCodeId;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	modal.show();
}
</script>

@endsection

@section('modal')
<!-- Create Promo Code Modal -->
<div class="modal fade" id="createPromoCodeModal" tabindex="-1" aria-labelledby="createPromoCodeModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createPromoCodeModalLabel">{{__('admin.add')}}
					{{__('admin.promo-code')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="createPromoCodeForm">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="create_name"
								class="form-label">{{__('admin.name')}}
								<span
									class="text-danger">*</span></label>
							<input type="text" class="form-control"
								id="create_name" name="name" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="create_code"
								class="form-label">{{__('admin.code')}}
								<span
									class="text-danger">*</span></label>
							<input type="text" class="form-control"
								id="create_code" name="code" required
								style="text-transform: uppercase;">
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="create_valid_date"
								class="form-label">{{__('admin.valid-date')}}
								<span
									class="text-danger">*</span></label>
							<input type="date" class="form-control"
								id="create_valid_date" name="valid_date"
								required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="create_expire_date"
								class="form-label">{{__('admin.expire-date')}}
								<span
									class="text-danger">*</span></label>
							<input type="date" class="form-control"
								id="create_expire_date"
								name="expire_date" required>
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="create_discount_percentage"
								class="form-label">{{__('admin.discount-percentage')}}
								(%) <span
									class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" max="100"
								class="form-control"
								id="create_discount_percentage"
								name="discount_percentage" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="create_usage_limit"
								class="form-label">{{__('admin.usage-limit')}}</label>
							<input type="number" min="1" class="form-control"
								id="create_usage_limit"
								name="usage_limit"
								placeholder="{{__('admin.unlimited')}}">
							<div class="invalid-feedback"></div>

						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="create_package_type"
								class="form-label">{{__('admin.package-type')}}
								<span
									class="text-danger">*</span></label>
							<select class="form-select"
								id="create_package_type" required>
								<option value="all">
									{{__('admin.all-packages')}}
								</option>
								<option value="specific">
									{{__('admin.specific-package')}}
								</option>
							</select>
						</div>
						<div class="col-md-6 mb-3" id="create_package_wrapper"
							style="display: none;">
							<label for="create_package_id"
								class="form-label">{{__('admin.package')}}</label>
							<select class="form-select" id="create_package_id"
								name="package_id">
								<option value="">
									{{__('admin.select-package')}}
								</option>
								@foreach($packages as $package)
								<option value="{{$package->id}}">
									{{__('admin.package')}}
									#{{$package->id}} -
									{{$package->price}}
									{{__('admin.currency')}}
								</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox"
								id="create_is_active" name="is_active"
								value="1" checked>
							<label class="form-check-label"
								for="create_is_active">{{__('admin.active')}}</label>
						</div>
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

<!-- Edit Promo Code Modal -->
<div class="modal fade" id="editPromoCodeModal" tabindex="-1" aria-labelledby="editPromoCodeModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editPromoCodeModalLabel">{{__('admin.edit')}}
					{{__('admin.promo-code')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="editPromoCodeForm">
				@csrf
				<input type="hidden" id="edit_promo_code_id" name="id">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="edit_name"
								class="form-label">{{__('admin.name')}}
								<span
									class="text-danger">*</span></label>
							<input type="text" class="form-control"
								id="edit_name" name="name" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="edit_code"
								class="form-label">{{__('admin.code')}}
								<span
									class="text-danger">*</span></label>
							<input type="text" class="form-control"
								id="edit_code" name="code" required
								style="text-transform: uppercase;">
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="edit_valid_date"> {{__('admin.valid-date')}} <span class="text-danger">*</span></label>
							<input type="date" class="form-control"
								id="edit_valid_date" name="valid_date"
								required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="edit_expire_date"
								class="form-label">{{__('admin.expire-date')}}
								<span
									class="text-danger">*</span></label>
							<input type="date" class="form-control"
								id="edit_expire_date" name="expire_date"
								required>
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="edit_discount_percentage"
								class="form-label">{{__('admin.discount-percentage')}}
								(%) <span
									class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" max="100"
								class="form-control"
								id="edit_discount_percentage"
								name="discount_percentage" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="edit_usage_limit"
								class="form-label">{{__('admin.usage-limit')}}</label>
							<input type="number" min="1" class="form-control"
								id="edit_usage_limit" name="usage_limit"
								placeholder="{{__('admin.unlimited')}}">
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="edit_package_type"
								class="form-label">{{__('admin.package-type')}}
								<span
									class="text-danger">*</span></label>
							<select class="form-select" id="edit_package_type"
								required>
								<option value="all">
									{{__('admin.all-packages')}}
								</option>
								<option value="specific">
									{{__('admin.specific-package')}}
								</option>
							</select>
						</div>
						<div class="col-md-6 mb-3" id="edit_package_wrapper"
							style="display: none;">
							<label for="edit_package_id"
								class="form-label">{{__('admin.package')}}</label>
							<select class="form-select" id="edit_package_id"
								name="package_id">
								<option value="">
									{{__('admin.select-package')}}
								</option>
								@foreach($packages as $package)
								<option value="{{$package->id}}">
									{{__('admin.package')}}
									#{{$package->id}} -
									{{$package->price}}
									{{__('admin.currency')}}
								</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox"
								id="edit_is_active" name="is_active"
								value="1">
							<label class="form-check-label"
								for="edit_is_active">{{__('admin.active')}}</label>
						</div>
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
