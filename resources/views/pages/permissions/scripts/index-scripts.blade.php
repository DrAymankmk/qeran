@section('extra-js')
<script src="{{asset('admin_assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>
	$(document).ready(function() {
		$('#permissionsTable').DataTable({
			"language": {
				"url": "{{asset('admin_assets/libs/datatables.net/js/arabic.json')}}"
			},
			"order": [[0, "desc"]],
			"pageLength": 15,
			"responsive": true
		});
	});

	function openModalDelete(id) {
		$('#deleteModal').modal('show');
		$('#deleteModal form').attr('action', '{{route("permissions.index")}}/' + id);
	}
</script>
@endsection









