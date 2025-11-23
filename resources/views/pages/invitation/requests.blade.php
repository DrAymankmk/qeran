@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />

@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.invitation-requests')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.invitation-requests')}}
					</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-12">
						<div class="row g-3 align-items-end">
							<!-- <div class="col-md-3">
								<label for="statusFilter" class="form-label">{{__('admin.status')}}</label>
								<select id="statusFilter" class="form-select form-select-sm">
									<option value="">{{__('admin.all')}}</option>
									@foreach(\App\Helpers\Constant::INVITATION_STATUS as $key => $value)
										<option value="{{$value}}">{{__('admin.invitation-status-'.$value)}}</option>
									@endforeach
								</select>
							</div> -->
							<div class="col-md-3">
								<label for="dateFromFilter"
									class="form-label">{{__('admin.date-from')}}</label>
								<input type="date" id="dateFromFilter"
									class="form-control form-control-sm">
							</div>
							<div class="col-md-3">
								<label for="dateToFilter"
									class="form-label">{{__('admin.date-to')}}</label>
								<input type="date" id="dateToFilter"
									class="form-control form-control-sm">
							</div>
							<div class="col-md-3">
								<button type="button" id="resetFilters"
									class="btn btn-sm btn-secondary">
									<i
										class="mdi mdi-refresh"></i>
									{{__('admin.reset')}}
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive mt-2">
					<table id="invitationsRequestTable" class="table table-hover nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.category')}}
								</th>

								<th scope="col">{{__('admin.name')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-mime-type')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-uploaded-image')}}
								</th>
								<!-- 
								<th scope="col">
									{{__('admin.invitation-uploaded-video')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-uploaded-audio')}}
								</th>
								<th scope="col">{{__('admin.desc')}}
								</th>
								<th scope="col">
									{{__('admin.receipt-image')}}
								</th> -->
								{{--                                <th scope="col">{{__('admin.paid-status')}}
								</th>--}}
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							{{-- DataTables will populate this via server-side processing --}}
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection

@include('pages.invitation.scripts.request-scripts')


@section('modal')

<!-- Invitation Details Modal -->
<div class="modal fade" id="invitationRequestDetailsModal" tabindex="-1"
	aria-labelledby="invitationRequestDetailsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="invitationRequestDetailsModalLabel">
					{{__('admin.invitation-details')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.id')}}:</strong> <span
							id="modal_invitation_id"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.invitation-code')}}:</strong> <span
							id="modal_invitation_code"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.user')}}:</strong> <span
							id="modal_user_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.phone')}}:</strong> <span
							id="modal_user_phone"></span>
					</div>
					<!-- user_phone -->
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.invitation-type')}}:</strong> <span
							id="modal_invitation_type"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.name')}}:</strong> <span
							id="modal_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.category')}}:</strong> <span
							id="modal_category_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.invitation-mime-type')}}:</strong> <span
							id="modal_media_type"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.status')}}:</strong> <span
							id="modal_status"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.host-name')}}:</strong> <span
							id="modal_host_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.date')}}:</strong> <span
							id="modal_date"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.time')}}:</strong> <span
							id="modal_time"></span>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.address')}}:</strong> <span
							id="modal_address"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.groom')}}:</strong> <span
							id="modal_groom"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.bride')}}:</strong> <span
							id="modal_bride"></span>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.event-name')}}:</strong> <span
							id="modal_event_name"></span>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.description')}}:</strong>
						<p id="modal_description" class="mt-2"></p>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.invitation-uploaded-image')}}:</strong>
						<div id="modal_design_image" class="mt-2"></div>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.invitation-uploaded-video')}}:</strong>
						<div id="modal_design_video" class="mt-2"></div>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.invitation-uploaded-audio')}}:</strong>
						<div id="modal_design_audio" class="mt-2"></div>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.receipt-image')}}:</strong>
						<div id="modal_receipt_image" class="mt-2"></div>
					</div>
					<div class="col-12 mb-3">
						<strong>{{__('admin.created_at')}}:</strong> <span
							id="modal_created_at"></span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary"
					data-bs-dismiss="modal">{{__('admin.close')}}</button>
			</div>
		</div>
	</div>
</div>


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