@section('extra-js')
@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')
<script>
$(document).ready(function() {
	// Initialize DataTable - disable ordering to maintain backend order
	var table = $('#mediaTable').DataTable({
		dom: '<"row mb-3 align-items-center justify-content-between"<"col-auto"l><"col-auto ms-3"f>>' +
			'rt' +
			'<"row"<"col-md-5"i><"col-md-7"p>>',
		ordering: false, // Disable client-side ordering to maintain backend order
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		],
		responsive: true,
		columnDefs: [{
				orderable: false,
				targets: [1]
			}, // Preview column
			{
				searchable: false,
				targets: [1]
			} // Preview column
		],
		drawCallback: function() {
			$('.dataTables_length select').addClass(
				'form-select form-select-sm'
				);
		}
	});

	// Style the elements
	$('.dataTables_filter').addClass('mb-0');
	$('.dataTables_filter input').addClass('form-control form-control-sm');
	$('.dataTables_length').addClass('mb-0');
	$('.dataTables_length label').addClass('form-label mb-0 me-2');
	$('.dataTables_length select').addClass(
		'form-select form-select-sm d-inline-block w-auto');
	$('.dt-buttons').addClass('mb-0');
});

function openModalDelete(id) {
	$('#deleteModal').modal('show');
	$('#deleteModal form').attr('action', '{{route("media.index")}}/' + id);
}
</script>
@endsection