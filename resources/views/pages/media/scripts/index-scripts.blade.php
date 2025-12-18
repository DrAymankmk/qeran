@section('extra-js')
@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')
<script>
$(document).ready(function() {
	// Initialize DataTable using reusable function
	var table = initAdminDataTable({
		tableId: '#mediaTable',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1],
		nonSearchableColumns: [1],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		]
	});
});

function openModalDelete(id) {
	$('#deleteModal').modal('show');
	$('#deleteModal form').attr('action', '{{route("media.index")}}/' + id);
}
</script>
@endsection
