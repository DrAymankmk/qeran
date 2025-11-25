@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />

@endsection
@section('title', __('admin.home'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
	<a href="javascript:" class="text-muted text-hover-primary">{{__('admin.home')}}</a>
</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-2">
					<div class="col-sm-4">
					</div>

					{{--                            @can('create_notifications')--}}

					<div class="col-sm-8">
						<div class="text-sm-end">
							<a href="{{route('notifications.create')}}"
								class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
									class="mdi mdi-plus me-1"></i>
								{{__('admin.add-new')}} </a>

						</div>
					</div><!-- end col-->
				</div>
				<div class="table-responsive mt-2">
					<table id="notificationsTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">
									{{__('admin.title-text')}}
								</th>
								<th scope="col">
									{{__('admin.description')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>

							</tr>
						</thead>
						<tbody>
							@foreach($notifications as $notification)

							<tr>
								<td>{{$notification->id}} </td>

								<td>{{$notification->title}}</td>
								<td>{{$notification->description}}</td>

								<td>
									{{$notification->created_at}}
								</td>
								<td>
									<div class="d-flex gap-3">
										
										<a onclick="openModalDelete({{$notification->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-delete font-size-18"></i></a>

									</div>
								</td>
							</tr>
							@endforeach


						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

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
		tableId: '#notificationsTable',
		pdfRoute: '{{route("notifications.export.pdf")}}',
		orderColumn: 0,
		orderDirection: 'desc',
		nonOrderableColumns: [1, 4],
		nonSearchableColumns: [1, 4],
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		]
	});
});
function openModalDelete(shipper_id) {
	$('.action_form').attr('action', '{{route('notifications.destroy', '')}}' + '/' + shipper_id);
	$('#deleteModal').modal('show');

}
</script>


@endsection
@section('modal')
@component('layouts.includes.modal')
@slot('modalID')
deleteModal
@endslot
@slot('modalTitle')
{{__('admin.delete-data')}}
@endslot
@slot('modalMethodPutOrDelete')
@method('delete')
@endslot
@slot('modalContent')
<div class="text-center">
	<span class="text-danger font-16">
		{{__('admin.delete-message-confirm')}}
	</span>
</div>
@endslot
@endcomponent
@endsection