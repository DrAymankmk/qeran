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
		tableId: '#designsTable',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1, 6],
		nonSearchableColumns: [1, 6],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "{{__('admin.all')}}"]
		]
	});
});

function openModalDelete(design_id) {
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		// Build the correct route URL for design destroy
		const baseUrl = "{{route('designs.destroy', 999)}}".replace('/999', '');
		form.action = baseUrl + '/' + design_id;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	modal.show();
}
</script>
@endsection






















