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
<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.notifications')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.notifications')}}</li>
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
				<div class="row mb-2">
					<div class="col-md-12 col-sm-12">
						<div class="text-sm-start d-flex gap-2">
							@can('view-notifications')
							<a href="{{route('notifications.create')}}"
								class="btn btn-primary btn-rounded waves-effect waves-light mb-2"><i
									class="mdi mdi-plus me-1"></i>
								{{__('admin.add-new')}} </a>
							<button type="button"
								class="btn btn-success btn-rounded waves-effect waves-light mb-2"
								onclick="markAllNotificationsAsRead()">
								<i class="mdi mdi-check-all me-1"></i>
								{{__('admin.read-all')}}
							</button>
							@endcan
						</div>
					</div>
				</div>

				<!-- Category Tabs -->
				@php
				$currentCategory = $category ?? null;
				$orderCategory = \App\Helpers\Constant::NOTIFICATION_CATEGORY['Order'];
				$paymentCategory = \App\Helpers\Constant::NOTIFICATION_CATEGORY['Payment'];
				$userCategory = \App\Helpers\Constant::NOTIFICATION_CATEGORY['User'];
				$contactCategory = \App\Helpers\Constant::NOTIFICATION_CATEGORY['Contact Us'];
				@endphp
				<ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
					<li class="nav-item">
						<a class="nav-link {{$currentCategory == null ? 'active' : ''}}"
							href="{{route('notifications.index')}}"
							role="tab">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-home"></i></span>
							<span class="d-none d-sm-block">{{__('admin.all')}}
								<span
									class="badge bg-primary rounded-pill ms-1">{{$categoryCounts['all'] ?? 0}}</span>
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{$currentCategory == $orderCategory ? 'active' : ''}}"
							href="{{route('notifications.index', ['category' => $orderCategory])}}"
							role="tab">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-cart"></i></span>
							<span class="d-none d-sm-block">{{__('admin.order_notifications')}}
								<span
									class="badge bg-primary rounded-pill ms-1">{{$categoryCounts[$orderCategory] ?? 0}}</span>
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{$currentCategory == $paymentCategory ? 'active' : ''}}"
							href="{{route('notifications.index', ['category' => $paymentCategory])}}"
							role="tab">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-credit-card"></i></span>
							<span class="d-none d-sm-block">{{__('admin.payment_notifications')}}
								<span
									class="badge bg-primary rounded-pill ms-1">{{$categoryCounts[$paymentCategory] ?? 0}}</span>
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{$currentCategory == $userCategory ? 'active' : ''}}"
							href="{{route('notifications.index', ['category' => $userCategory])}}"
							role="tab">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-account"></i></span>
							<span class="d-none d-sm-block">{{__('admin.user_notifications')}}
								<span
									class="badge bg-primary rounded-pill ms-1">{{$categoryCounts[$userCategory] ?? 0}}</span>
							</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{$currentCategory == $contactCategory ? 'active' : ''}}"
							href="{{route('notifications.index', ['category' => $contactCategory])}}"
							role="tab">
							<span class="d-block d-sm-none"><i
									class="mdi mdi-email"></i></span>
							<span class="d-none d-sm-block">{{__('admin.contact_us_notification')}}
								<span
									class="badge bg-primary rounded-pill ms-1">{{$categoryCounts[$contactCategory] ?? 0}}</span>
							</span>
						</a>
					</li>
				</ul>

				<div class="table-responsive mt-2">
					<table id="notificationsTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.status')}}
								</th>
								<th scope="col">{{__('admin.category')}}
								</th>
								<th scope="col">
									{{__('admin.notification_type')}}
								</th>
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
							<!-- Data will be loaded via AJAX -->
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

@include('pages.notifications.scripts.index-scripts')

@section('modal')
<!-- Notification Details Modal -->
<div class="modal fade" id="notificationDetailsModal" tabindex="-1" aria-labelledby="notificationDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="notificationDetailsModalLabel">
					{{__('admin.notification-details')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.id')}}:</strong> <span
							id="modal_id"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.status')}}:</strong> <span
							id="modal_status"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.type')}}:</strong> <span
							id="modal_type"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.category')}}:</strong> <span
							id="modal_category"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.notification_type')}}:</strong> <span
							id="modal_notification_type"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.user_id')}}:</strong> <span
							id="modal_user_id"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.target_id')}}:</strong> <span
							id="modal_target_id"></span>
					</div>
					<div class="col-md-12 mb-3">
						<strong>{{__('admin.title-text')}}:</strong>
						<div class="alert alert-light mt-2">
							<span id="modal_title"></span>
						</div>
					</div>
					<div class="col-md-12 mb-3">
						<strong>{{__('admin.description')}}:</strong>
						<div class="alert alert-light mt-2">
							<span id="modal_description"></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.created_at')}}:</strong> <span
							id="modal_created_at"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.read_at')}}:</strong> <span
							id="modal_read_at"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.updated_at')}}:</strong> <span
							id="modal_updated_at"></span>
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
