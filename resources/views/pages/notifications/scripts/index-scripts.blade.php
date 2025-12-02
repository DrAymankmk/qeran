@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<!-- init js -->
<script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>



<script>
var notificationsTable;
$(document).ready(function() {
	// Get category from URL
	const urlParams = new URLSearchParams(window.location.search);
	const category = urlParams.get('category');

	// Initialize DataTable with server-side processing
	notificationsTable = $('#notificationsTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '{{ route("notifications.index") }}',
			type: 'GET',
			data: function(d) {
				// Add category filter
				if (category) {
					d.category =
						category;
				}
			},
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		dom: '<"row mb-3 align-items-center justify-content-between"<"col-auto"l><"col-auto ms-3"f><"col-auto ms-3"B>>' +
			'rt' +
			'<"row"<"col-md-5"i><"col-md-7"p>>',
		buttons: [{
				extend: 'copy',
				className: 'btn btn-sm btn-outline-primary',
				text: '<i class="mdi mdi-content-copy"></i> {{__("admin.copy")}}'
			},
			{
				extend: 'excel',
				className: 'btn btn-sm btn-outline-success',
				text: '<i class="mdi mdi-file-excel"></i> {{__("admin.excel")}}'
			},
			{
				text: '<i class="mdi mdi-file-pdf"></i> {{__("admin.pdf")}}',
				className: 'btn btn-sm btn-outline-danger',
				action: function(e, dt,
					button,
					config) {
					window.location
						.href =
						'{{route("notifications.export.pdf")}}';
				}
			},
			{
				extend: 'print',
				className: 'btn btn-sm btn-outline-info',
				text: '<i class="mdi mdi-printer"></i> {{__("admin.print")}}'
			},
		],
		responsive: true,
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		],
		order: [
			[0, 'desc']
		],
		language: {
			@if(app()->getLocale() == 'ar')
			url: "{{asset('admin_assets/ar.json')}}"
			@else
			search: "{{__('admin.search')}}:",
			lengthMenu: "{{__('admin.show')}} _MENU_ {{__('admin.entries')}}",
			info: "{{__('admin.showing')}} _START_ {{__('admin.to')}} _END_ {{__('admin.of')}} _TOTAL_ {{__('admin.entries')}}",
			infoEmpty: "{{__('admin.showing')}} 0 {{__('admin.to')}} 0 {{__('admin.of')}} 0 {{__('admin.entries')}}",
			infoFiltered: "({{__('admin.filtered')}} {{__('admin.from')}} _MAX_ {{__('admin.total')}} {{__('admin.entries')}})",
			paginate: {
				first: "{{__('admin.first')}}",
				last: "{{__('admin.last')}}",
				next: "{{__('admin.next')}}",
				previous: "{{__('admin.previous')}}"
			},
			zeroRecords: "{{__('admin.no-matching-records')}}",
			emptyTable: "{{__('admin.no-data-available')}}",
			processing: "{{__('admin.loading')}}..."
			@endif
		},
		columnDefs: [{
			targets: [1, 4,
				7
			], // Status, Title, Actions columns
			orderable: false,
			searchable: false
		}],
		createdRow: function(row, data, dataIndex) {
			// Add table-warning class for unread notifications
			// Check if the status column (index 1) contains "unread" badge
			const statusCell = $(row).find(
				'td:eq(1)');
			if (statusCell.find('.badge.bg-danger')
				.length > 0) {
				$(row).addClass(
					'table-warning');
			}
		},
		drawCallback: function() {
			$('.dataTables_length select').addClass(
				'form-select form-select-sm'
			);
		}
	});

	// Style the search box
	$('.dataTables_filter').addClass('mb-0');
	$('.dataTables_filter input').addClass('form-control form-control-sm');
	$('.dataTables_filter input').attr('placeholder', "{{__('admin.search')}}...");

	// Style the length menu (per page selector)
	$('.dataTables_length').addClass('mb-0');
	$('.dataTables_length label').addClass('form-label mb-0 me-2');
	$('.dataTables_length select').addClass(
		'form-select form-select-sm d-inline-block w-auto');

	// Style the buttons container
	$('.dt-buttons').addClass('mb-0');
});

// Mark all notifications as read
function markAllNotificationsAsRead() {
	if (!confirm('{{__("admin.confirm-mark-all-read")}}')) {
		return;
	}

	fetch('{{ route("notifications.mark-all-read") }}', {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Content-Type': 'application/json',
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				toastr.success(data.message ||
					'{{__("admin.all-notifications-marked-as-read")}}'
				);
				// Reload page to refresh the list
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				toastr.error('{{__("admin.error-occurred")}}');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			toastr.error('{{__("admin.error-occurred")}}');
		});
}

function openModalDelete(shipper_id) {
	$('.action_form').attr('action', '{{route("notifications.destroy", "")}}' + '/' + shipper_id);
	$('#deleteModal').modal('show');
}

// Show notification details via AJAX
window.showNotificationDetails = function(notificationId) {
	// Show loading state
	const modalElement = document.getElementById('notificationDetailsModal');
	const modalBody = modalElement.querySelector('.modal-body');
	const originalContent = modalBody.innerHTML;
	modalBody.innerHTML =
		'<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">{{__("admin.loading")}}</p></div>';

	// Show modal
	const modal = new bootstrap.Modal(modalElement);
	modal.show();

	// Make AJAX request
	fetch('{{ route("notifications.details", ":id") }}'.replace(':id', notificationId), {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			}
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(data => {
			// Restore original modal body structure
			modalBody.innerHTML = originalContent;

			// Fill modal with data
			const fields = {
				'id': data.id,
				'type': data.type_name || data.type ||
					'{{__("admin.no-data-available")}}',
				'category': data.category_text ||
					'{{__("admin.no-data-available")}}',
				'notification_type': data
					.notification_type_text ||
					'{{__("admin.no-data-available")}}',
				'user_id': data.user_id ||
					'{{__("admin.no-data-available")}}',
				'target_id': data.target_id ||
					'{{__("admin.no-data-available")}}',
				'title': data.title ||
					'{{__("admin.no-data-available")}}',
				'description': data.description ||
					'{{__("admin.no-data-available")}}',
				'read_at': data.read_at_formatted || (data
					.read_at ? data.read_at :
					'{{__("admin.no-data-available")}}'
				),
				'created_at': data.created_at_formatted || (data
					.created_at ? data
					.created_at :
					'{{__("admin.no-data-available")}}'
				),
				'updated_at': data.updated_at ||
					'{{__("admin.no-data-available")}}',
				'status': data.is_read ? '{{__("admin.read")}}' :
					'{{__("admin.unread")}}'
			};

			Object.keys(fields).forEach(field => {
				const element = document.getElementById(
					`modal_${field}`);
				if (element) {
					element.textContent = fields[
						field];
				}
			});
		})
		.catch(error => {
			console.error('Error:', error);
			modalBody.innerHTML = `<div class="alert alert-danger text-center p-5">
			<h5>{{__("admin.error")}}</h5>
			<p>{{__("admin.notification-not-found")}}</p>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("admin.close")}}</button>
		</div>`;
		});
};
</script>


@endsection