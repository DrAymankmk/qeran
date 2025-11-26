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

    // Handle status select change
    $(document).on('change', '.status-select', function() {
        const select = $(this);
        const userId = select.data('user-id');
        const url = select.data('url');
        const selectedStatus = select.val();
        const previousStatus = select.data('previous-value') || select.find('option:selected').val();

        // Store previous value
        select.data('previous-value', selectedStatus);

        // Disable select during request
        select.prop('disabled', true);

        // Make AJAX request
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                status: selectedStatus
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    @if(app()->getLocale() == 'ar')
                    toastr.success('تم تحديث الحالة بنجاح');
                    @else
                    toastr.success('Status updated successfully');
                    @endif
                } else {
                    // Revert select value on error
                    select.val(previousStatus);
                }
            },
            error: function(xhr) {
                // Revert select value on error
                select.val(previousStatus);

                // Show error message
                @if(app()->getLocale() == 'ar')
                toastr.error('حدث خطأ أثناء تحديث الحالة');
                @else
                toastr.error('Error updating status');
                @endif
            },
            complete: function() {
                // Re-enable select
                select.prop('disabled', false);
            }
        });
    });
</script>



@endsection
