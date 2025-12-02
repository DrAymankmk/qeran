@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<!-- init js -->
<script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

<script src="{{asset('admin_assets/js/jquery.printPage.js') }}"></script>

<script>

   $(document).ready(function() {
	// Initialize DataTable using reusable function
	var table = initAdminDataTable({
		tableId: '#contactTable',
		pdfRoute: '{{route("contact.export.pdf")}}',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1, 4],
		nonSearchableColumns: [1, 4],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "{{__('admin.all')}}"]
		]
	});

	// Highlight row if highlight parameter exists
	const urlParams = new URLSearchParams(window.location.search);
	const highlightId = urlParams.get('highlight');
	if (highlightId) {
		table.on('draw', function() {
			$('#contactTable tbody tr').each(function() {
				const firstCell = $(this).find('td:first');
				if (firstCell.text().trim() == highlightId) {
					$(this).addClass('table-warning highlight-row');
					$(this).css({
						'background-color': '#fff3cd',
						'border-left': '4px solid #ffc107',
						'animation': 'pulse-highlight 2s ease-in-out'
					});

					// Scroll to the row
					$('html, body').animate({
						scrollTop: $(this).offset().top - 100
					}, 500);

					// Remove highlight after 5 seconds
					setTimeout(function() {
						$(this).removeClass('table-warning highlight-row');
						$(this).css({
							'background-color': '',
							'border-left': '',
							'animation': ''
						});
					}.bind(this), 5000);

					return false;
				}
			});
		});
		table.draw();
	}
});

// Add CSS for pulse animation
if (document.getElementById('highlight-style') === null) {
	const style = document.createElement('style');
	style.id = 'highlight-style';
	style.textContent = `
		@keyframes pulse-highlight {
			0%, 100% {
				box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
			}
			50% {
				box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
			}
		}
		.highlight-row {
			transition: all 0.3s ease;
		}
	`;
	document.head.appendChild(style);
}



function openModalDelete(contact_id) {
	$('.action_form').attr('action', '{{route('contact.destroy', '')}}' + '/' + contact_id);
	$('#deleteModal').modal('show');
}


// Show invitation details via AJAX
window.showContactDetails = function(contactId) {
	// Show loading state
	const modalElement = document.getElementById(
		'contactDetailsModal');
	const modalBody = modalElement.querySelector('.modal-body');
	const originalContent = modalBody.innerHTML;
	modalBody.innerHTML =
		'<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">{{__("admin.loading")}}</p></div>';

	// Show modal
	const modal = new bootstrap.Modal(modalElement);
	modal.show();

	// Make AJAX request
	fetch('{{ route("contact.show", ":id") }}'.replace(':id',
			contactId), {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			}
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(
					'Network response was not ok'
				);
			}
			return response.json();
		})
		.then(data => {
			// Restore original modal body structure first
			modalBody.innerHTML = originalContent;

			// Fill modal with data
			const fields = [
				'id', 'name', 'email', 'country_code', 'phone',
				'subject', 'status', 'conversation_status',
				'message', 'created_at'
			];

			fields.forEach(field => {
				const element = document.getElementById(`modal_${field}`);
				if (element) {
					if (field === 'conversation_status') {
						// Map conversation status to text
						const statusMap = {
							1: '{{__("admin.new")}}',
							2: '{{__("admin.under_review")}}',
							3: '{{__("admin.closed")}}'
						};
						element.textContent = statusMap[data[field]] || '{{ __("admin.no-data-available") }}';
					} else {
						element.textContent = data[field] || '{{ __("admin.no-data-available") }}';
					}
				}
			});


		})
		.catch(error => {
			console.error('Error:', error);
			modalBody.innerHTML = `<div class="alert alert-danger text-center p-5">
				<h5>{{__("admin.error")}}</h5>
				<p>{{__("admin.contact-not-found")}}</p>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("admin.close")}}</button>
			</div>`;
		});
};
</script>



@endsection
