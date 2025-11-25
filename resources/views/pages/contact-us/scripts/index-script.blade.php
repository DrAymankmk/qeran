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
});


	
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
				'subject', 'status',
				'message', 'created_at'

			];

			fields.forEach(field => {
				const element =
					document
					.getElementById(
						`modal_${field}`
					);
				if (
					element
				) {
					element.textContent =
						data[
							field
						] ||
						'{{ __("admin.no-data-available") }}';
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
