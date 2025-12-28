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
		tableId: '#testimonialsTable',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1, 8],
		nonSearchableColumns: [1, 8],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "{{__('admin.all')}}"]
		]
	});
});

function openModalDelete(testimonial_id) {
	const form = document.querySelector('#deleteModal .action_form');
	if (form) {
		// Build the correct route URL for testimonial destroy
		const baseUrl = "{{route('testimonials.destroy', 999)}}".replace('/999', '');
		form.action = baseUrl + '/' + testimonial_id;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	modal.show();
}
</script>
@endsection












