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
		tableId: '#usersTable',
		pdfRoute: '{{route("users.export.pdf")}}',
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

    


    function openModalDelete(user_id) {
        $('.action_form').attr('action', '{{route('users.destroy', '')}}' + '/' + user_id);
        $('#deleteModal').modal('show');
    }

    // Handle status switch toggle
    $(document).on('change', '.status-switch', function() {
        const checkbox = $(this);
        const userId = checkbox.data('user-id');
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        
        // Disable checkbox during request
        checkbox.prop('disabled', true);
        
        // Make AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message if needed
                    // The checkbox state is already updated by the user's click
                } else {
                    // Revert checkbox state on error
                    checkbox.prop('checked', !isChecked);
                }
            },
            error: function(xhr) {
                // Revert checkbox state on error
                checkbox.prop('checked', !isChecked);
                
                // Show error message
                @if(app()->getLocale() == 'ar')
                alert('حدث خطأ أثناء تحديث الحالة');
                @else
                alert('Error updating status');
                @endif
            },
            complete: function() {
                // Re-enable checkbox
                checkbox.prop('disabled', false);
            }
        });
    });
</script>



@endsection