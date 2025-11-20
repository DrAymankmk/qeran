@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

<!-- Required datatable js -->
<script src="{{asset('admin_assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<!-- Buttons -->
<script src="{{asset('admin_assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/jszip/jszip.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>


<script src="{{asset('admin_assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>

<!-- Responsive examples -->
<script src="{{asset('admin_assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

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
	// Helper function to extract clean text from HTML nodes
	function extractText(node) {
		if (!node) return '';
		if (node.nodeType === 3) {
			// Text node
			return node.textContent || '';
		}
		if (node.nodeType === 1) {
			// Element node - recursively get text from children
			var text = '';
			for (var i = 0; i < node.childNodes.length; i++) {
				text += extractText(node.childNodes[i]);
			}
			return text;
		}
		return '';
	}

	// Initialize DataTable with buttons and custom layout
	var table = $('#categoriesTable').DataTable({
		dom: '<"row d-flex flex-row-reverse mb-3"<"col-md-6 d-flex justify-content-end"B><"col-md-6 d-flex justify-content-start"f>>' +
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
					// Use server-side PDF export with Arabic support
					window.location
						.href =
						'{{route("category.export.pdf")}}';
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
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "{{__('admin.all')}}"]
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
			emptyTable: "{{__('admin.no-data-available')}}"
			@endif
		},
		columnDefs: [{
			targets: [1, 4],
			orderable: false,
			searchable: false
		}],
		drawCallback: function() {
			$('.dataTables_length select').addClass(
				'form-select form-select-sm'
			);
		}
	});


	// Style the search box to appear on the right
	$('.dataTables_filter').addClass('text-end');
	$('.dataTables_filter input').addClass('form-control form-control-sm');
	$('.dataTables_filter input').attr('placeholder', "{{__('admin.search')}}...");
});

function openModalDelete(category_id) {
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		form.action = "{{route('category.destroy', 'category_id ')}}" + '/' + category_id;
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
				'image',
				'created_at',
				'updated_at'
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
