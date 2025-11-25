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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.contact-us')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.contact-us')}}</li>
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

				<div class="table-responsive mt-2">
					<table id="contactTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>

								<th scope="col">{{__('admin.status')}}
								</th>
								<th scope="col">{{__('admin.name')}}
								</th>
								<th scope="col">{{__('admin.email')}}
								</th>
								<th scope="col">{{__('admin.phone')}}
								</th>
								<!-- <th scope="col">{{__('admin.subject')}}
								</th>
								<th scope="col">{{__('admin.message')}}
								</th> -->
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>

							</tr>
						</thead>
						<tbody>
							@foreach($contacts as $contact)

							<tr>
								<td>{{$contact->id}}</a></td>
								<td>{{$contact->status==2?__('admin.not-replied-yet'):__('admin.replied')}}
								</td>

								<td>{{$contact->name}}</td>
								<td>{{$contact->email??__('admin.no-data-available')}}
								</td>
								<td style="direction: ltr;"><a
										href="tel:{{$contact->phone}}{{$contact->country_code}}">
										{{$contact->country_code}}{{$contact->phone}}
									</a></td>
								<!-- <td>{{$contact->subject}}</td>
								<td>{{$contact->message}}</td> -->

								<td>
									{{Carbon\Carbon::parse($contact->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">

										<a href="javascript:void(0);"
											onclick="showContactDetails({{$contact->id}})"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye font-size-22"></i></a>

										<a href="{{route('contact.reply',['contact_id'=>$contact->id])}}"
											title="{{__('admin.reply')}}"
											class="text-success"><i
												class="mdi mdi-message font-size-22"></i></a>




										<a onclick="openModalDelete({{$contact->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>

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
<!-- end row -->
@endsection


<!-- contact details modal -->
<div class="modal fade" id="contactDetailsModal" tabindex="-1" aria-labelledby="contactDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="contactDetailsModalLabel">
					{{__('admin.contact-details')}}</h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.name')}}:</strong> <span
								id="modal_name"></span>

						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.email')}}:</strong> <span
								id="modal_email"></span>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.phone')}}:</strong> <span
								id="modal_phone"></span>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.subject')}}:</strong> <span
								id="modal_subject"></span>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.message')}}:</strong> <span
								id="modal_message"></span>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.created_at')}}:</strong> <span
								id="modal_created_at"></span>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<strong>{{__('admin.status')}}:</strong> <span
								id="modal_status"></span>
						</div>
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

@include('pages.contact-us.scripts.index-script')


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
