@extends('layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.admin-dashboard')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="javascript: void(0);">{{__('admin.admin-dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active"> {{__('admin.dashboard')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<!-- Date Filter -->
@include('pages.dashboard.partials.filters')

<div class="row">
	<div class="col-xl-4">
		<div class="card overflow-hidden">
			<div class="bg-primary bg-soft">
				<div class="row">
					<div class="col-7">
						<div class="text-primary p-3">
							<h4 class="text-primary">
								{{__('admin.welcome-back')}}
							</h4>
							<p>{{__('admin.admin-dashboard')}}</p>
						</div>
					</div>
					<div class="col-5 align-self-end">
						<img src="{{auth()->guard('admin')->user()->img??asset('admin_assets/images/admin.png')}}"
							alt="" class="img-fluid">
					</div>
				</div>
			</div>
			<div class="card-body pt-0">
				<div class="row">
					<div class="col-sm-4">
						<div class="avatar-md profile-user-wid mb-4">
							<img src="{{auth()->guard('admin')->user()->img??asset('admin_assets/images/admin.png')}}"
								alt=""
								class="img-thumbnail rounded-circle">
						</div>
						<h5 class="font-size-15 text-truncate">
							{{auth()->guard('admin')->user()->name}}
						</h5>
					</div>

					<div class="col-sm-8">
						<div class="pt-4">

							<div class="row">
								<div class="col-6">
									<h5 class="font-size-15">
										{{$usersCount}}
									</h5>
									<p class="text-muted mb-0">
										{{__('admin.users')}}
									</p>
								</div>
								<div class="col-6">
									<h5 class="font-size-15">
										{{$invitationsCount}}
									</h5>
									<p class="text-muted mb-0">
										{{__('admin.invitations')}}
									</p>
								</div>
							</div>
							{{-- <div class="mt-4">--}}
							{{-- <a href="" class="btn btn-primary waves-effect waves-light btn-sm">{{__('admin.my-profile')}}<i
								class="mdi mdi-arrow-right ms-1"></i></a>--}}
							{{-- </div>--}}
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="col-xl-8">
		<div class="row">
			<div class="col-md-4">
				<div class="card mini-stats-wid">
					<div class="card-body">
						<div class="d-flex">
							<div class="flex-grow-1">
								<p class="text-muted fw-medium">
									{{__('admin.users')}}
								</p>
								<h4 class="mb-0">{{$usersCount}}</h4>
							</div>

							<div class="flex-shrink-0 align-self-center">
								<div
									class="mini-stat-icon avatar-sm rounded-circle bg-primary">
									<span class="avatar-title">
										<i
											class="bx bx-check-shield font-size-24"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card mini-stats-wid">
					<div class="card-body">
						<div class="d-flex">
							<div class="flex-grow-1">
								<p class="text-muted fw-medium">
									{{__('admin.invitations')}}
								</p>
								<h4 class="mb-0"> {{$invitationsCount}}
								</h4>
							</div>

							<div class="flex-shrink-0 align-self-center ">
								<div
									class="avatar-sm rounded-circle bg-primary mini-stat-icon">
									<span
										class="avatar-title rounded-circle ">
										<i
											class="bx bx-cart-alt font-size-24"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card mini-stats-wid">
					<div class="card-body">
						<div class="d-flex">
							<div class="flex-grow-1">
								<p class="text-muted fw-medium">
									{{__('admin.invitation-requests')}}
								</p>
								<h4 class="mb-0"> {{$requestInvitationsCount}}
								</h4>
							</div>

							<div class="flex-shrink-0 align-self-center ">
								<div
									class="avatar-sm rounded-circle bg-primary mini-stat-icon">
									<span
										class="avatar-title rounded-circle ">
										<i
											class="bx bx-cart-alt font-size-24"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card mini-stats-wid">
					<div class="card-body">
						<div class="d-flex">
							<div class="flex-grow-1">
								<p class="text-muted fw-medium">
									{{__('admin.contact_us')}}
								</p>
								<h4 class="mb-0"> {{$contactUsCount}}
								</h4>
							</div>

							<div class="flex-shrink-0 align-self-center">
								<div
									class="avatar-sm rounded-circle bg-primary mini-stat-icon">
									<span
										class="avatar-title rounded-circle ">
										<i
											class="bx bx-analyse font-size-24"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end row -->


	</div>
</div>
<div class="row">
	<div class="card">
		<div class="card-body">
			<div class="d-sm-flex flex-wrap">
				<h4 class="card-title mb-4"> {{__('admin.users-statistics')}}</h4>
				<div class="ms-auto">
				</div>
			</div>

			<div id="users-chart" class="apex-charts"
				data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
		</div>
	</div>

</div>
<!-- end row -->

<div class="row">

	<div class="card">
		<div class="card-body">
			<div class="d-sm-flex flex-wrap">
				<h4 class="card-title mb-4"> {{__('admin.invitations-statistics')}}</h4>
				<div class="ms-auto">
				</div>
			</div>

			<div id="invitations-chart" class="apex-charts"
				data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="card">
		<div class="card-body">
			<div class="d-sm-flex flex-wrap">
				<h4 class="card-title mb-4"> {{__('admin.most-used-categories')}}</h4>
				<div class="ms-auto">
				</div>
			</div>

			<div id="categories-chart" class="apex-charts"
				data-colors='["--bs-primary", "--bs-warning", "--bs-success", "--bs-info", "--bs-danger"]'
				dir="ltr"></div>
		</div>
	</div>
</div>


@include('pages.dashboard.partials.invitations', ['invitations' => $invitations])

@include('pages.dashboard.partials.invitation-requests', ['requestInvitations' => $requestInvitations])


<!-- end row -->

@endsection
@section('extra-js')
@include('pages.dashboard.scripts.dashboard-scripts')

<script>

</script>
@endsection


@section('modal')
<!-- Invitation Details Modal -->
<div class="modal fade" id="invitationDetailsModal" tabindex="-1" aria-labelledby="invitationDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="invitationDetailsModalLabel">
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
						<strong>{{__('admin.email')}}:</strong> <span
							id="modal_user_email"></span>
					</div>
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

<!-- Delete Invitation Modal -->
<div class="modal fade" id="deleteInvitationModal" tabindex="-1" aria-labelledby="deleteInvitationModalLabel"
	aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-dialog-centered">
		<form class="action_form" method="POST" action="" enctype="multipart/form-data">
			@csrf
			@method('DELETE')
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteInvitationModalLabel">
						{{__('admin.delete-data')}}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"
						aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<span class="text-danger font-16">
							{{__('admin.delete-message-confirm')}}
						</span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light"
						data-bs-dismiss="modal">{{__('admin.close')}}</button>
					<button type="submit"
						class="btn btn-primary">{{__('admin.confirm')}}</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
