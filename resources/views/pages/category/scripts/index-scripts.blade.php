@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<script src="{{asset('admin_assets/js/jquery.printPage.js') }}"></script>
<script src="{{asset('admin_assets/js/print.js') }}"></script>

<script>
$('.btnprn').printPage();
</script>

<script>
function change_status(id) {
	axios.get('category/status/' + id)
		.then(function(response) {
			console.log(response.data);
		})
		.catch(function(error) {
			console.log(error);
		});
}

function featured(id) {
	axios.get('category/featured/' + id)
		.then(function(response) {
			console.log(response.data);
		})
		.catch(function(error) {
			console.log(error);
		});
}
</script>

<script>
$(document).ready(function() {
	// Initialize DataTable using reusable function
	var table = initAdminDataTable({
		tableId: '#categoriesTable',
		pdfRoute: '{{route("category.export.pdf")}}',
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

function openModalDelete(category_id) {
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		// Build the correct route URL for category destroy
		// Use route helper with dummy ID and replace it with actual ID
		const baseUrl = "{{route('category.destroy', 999)}}".replace('/999', '');
		form.action = baseUrl + '/' + category_id;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	modal.show();
}

// Show invitation details via AJAX
window.showCategoryDetails = function(categoryId) {
	// Show loading state
	const modalElement = document.getElementById(
		'categoryDetailsModal');
	const modalBody = modalElement.querySelector('.modal-body');
	const originalContent = modalBody.innerHTML;
	modalBody.innerHTML =
		'<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">{{__("admin.loading")}}</p></div>';

	// Show modal
	const modal = new bootstrap.Modal(modalElement);
	modal.show();

	// Make AJAX request
	fetch('{{ route("category.show", ":id") }}'.replace(':id',
			categoryId), {
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
				'id', 'en_name',
				'ar_name',
				'en_title',
				'ar_title',
				'en_description',
				'ar_description',
				'image'
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

			// Handle date fields with formatted dates
			const createdEl = document.getElementById('modal_created_at');
			if (createdEl) {
				createdEl.textContent = data.created_at ||
					'{{ __("admin.no-data-available") }}';
			}

			const updatedEl = document.getElementById('modal_updated_at');
			if (updatedEl) {
				updatedEl.textContent = data.updated_at ||
					'{{ __("admin.no-data-available") }}';
			}

			// Handle media
			const imageEl = document
				.getElementById(
					'modal_image'
				);
			if (imageEl) {
				imageEl.innerHTML = data
					.image ?
					`<a target="_blank" href="${data.image}"><img src="${data.image}" class="img-fluid" alt="Image"></a>` :
					'{{ __("admin.no-data-available") }}';
			}




		})
		.catch(error => {
			console.error('Error:', error);
			modalBody.innerHTML = `<div class="alert alert-danger text-center p-5">
				<h5>{{__("admin.error")}}</h5>
				<p>{{__("admin.category-not-found")}}</p>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("admin.close")}}</button>
			</div>`;
		});
};
</script>

@endsection