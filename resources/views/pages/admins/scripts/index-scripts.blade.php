@section('extra-js')
<script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
<!-- bootstrap-datepicker js -->
<script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

<!-- init js -->
<script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

<script>
	$(document).ready(function() {
		// Initialize DataTable using reusable function
		var table = initAdminDataTable({
			tableId: '#adminsTable',
			pdfRoute: '{{route("admins.export.pdf")}}',
			orderColumn: 0,
			orderDirection: 'desc',
			nonOrderableColumns: [1],
			nonSearchableColumns: [1],
			pageLength: 10,
			lengthMenu: [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "{{__('admin.all')}}"]
			]
		});
	});

	function openModalDelete(admin_id) {
		$('.action_form').attr('action', '{{route('admins.destroy', '')}}' + '/' + admin_id);
		$('#deleteModal').modal('show');
	}
</script>

@endsection















































