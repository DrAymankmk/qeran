@extends('layouts.app')
@section('extra-css')
    <link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}"
          id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
          id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
          id="bootstrap-style" rel="stylesheet"
          type="text/css"/>

@endsection
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{__('admin.invitations')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.invitations')}}</li>
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
                        <div class="col-sm-4">
                        </div>
                        {{--                        @can('create_categories')--}}

                        {{--                            <div class="col-sm-8">--}}
                        {{--                                <div class="text-sm-end">--}}
                        {{--                                    <a href="{{route('invitation.create')}}"--}}
                        {{--                                       class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i--}}
                        {{--                                            class="mdi mdi-plus me-1"></i> {{__('admin.add-new')}} </a>--}}

                        {{--                                </div>--}}
                        {{--                            </div><!-- end col-->--}}
                        {{--                        @endcan--}}
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="invitationsTable" class="table table-hover nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.category')}}</th>
                                <th scope="col">{{__('admin.invitation-type')}}</th>
                                <th scope="col">{{__('admin.name')}}</th>
                                <th scope="col">{{__('admin.invitation-mime-type')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-image')}}</th>
                                 <!--
                                <th scope="col">{{__('admin.invitation-uploaded-video')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-audio')}}</th>
                                <th scope="col">{{__('admin.desc')}}</th>
                                <th scope="col">{{__('admin.receipt-image')}}</th> -->
                                {{--  <th scope="col">{{__('admin.paid-status')}}</th> --}}
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invitations as $invitation)

                                <tr>
                                    <td>
                                        <a href="javascript: void(0);" class="text-body fw-bold">{{$invitation->id}}</a>
                                    </td>
                                    <td>
                                        {{ $invitation->category->name }}
                                    </td>
                                    <td> {{__('admin.invitation-type-'. $invitation->invitation_type)}}</td>
                                    <td> {{$invitation->name}}</td>
                                    <td> {{__('admin.media-type-'.$invitation->invitation_media_type)}}</td>
                                    <td>
                                        @php
                                            $imagePath = $invitation->designImage() ?: $invitation->getMainImagePath();
                                        @endphp

                                        @if($imagePath)
                                            <a target="_blank" href="{{ $imagePath }}">
                                                <img class="header-profile-user" src="{{ $imagePath }}" alt="Invitation">
                                            </a>
                                        @else
                                            {{ __('admin.no-data-available') }}
                                        @endif
                                    </td>
                                     <!--
                                    <td>
                                        @if($invitation->designVideo())

                                            <video width="150" height="150" controls>
                                                <source src="{{$invitation->designVideo()}}" type="video/mp4">
                                                <source src="{{$invitation->designVideo()}}" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif

                                    </td>
                                    <td>
                                        @if($invitation->designAudio())

                                            <audio controls>
                                                <source src="{{$invitation->designAudio()}}" type="audio/ogg">
                                                <source src="{{$invitation->designAudio()}}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif

                                    </td>
                                    <td>
                                        @if($invitation->invitation_type != 3)
                                            {{$invitation->description}}
                                        @else
                                            {{__('admin.not_data_description')}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->receiptImage())
                                            <a target="_blank" href="{{$invitation->receiptImage()}}">

                                                <img class=" header-profile-user" src="{{$invitation->receiptImage()}}"
                                                     alt="Invitation">
                                            </a>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif
                                    </td> -->
                                  

                                    <td>
                                        {{Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="https://api.whatsapp.com/send?phone={{str_replace('+','',$invitation->user?->country_code)}}{{$invitation->user?->phone}}"
                                               title="{{__('admin.whatsapp')}}" class="text-success" target="_blank"><i
                                                    class="mdi mdi-whatsapp font-size-18"></i></a>
                                            
                                              <a href="javascript:void(0);"
											onclick="showInvitationDetails({{$invitation->id}})"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye font-size-22"></i></a>

                                                    <a href="{{route('invitation.edit',$invitation->id)}}"
                                               title="{{__('admin.edit')}}" class="text-warning"><i
                                                    class="mdi mdi-file-edit-outline font-size-22"></i></a>

                                            <a href="{{route('invitations.getPackagesByInvitationId',['invitation_id'=>$invitation->id])}}"
                                               title="{{__('admin.packages')}}" class="text-success"><i
                                                    class="mdi mdi-package font-size-22"></i></a>
                                            <a href="{{route('invitation.guards',$invitation->id)}}"
                                               title="{{__('admin.guards')}}" class="text-success"><i
                                                    class="mdi mdi-account font-size-22"></i></a>

                                            <a onclick="openModalDelete({{$invitation->id}})"
                                               title="{{__('admin.delete')}}" class="text-danger"><i
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

@include('pages.invitation.scripts.invitation-scripts')

@section('modal')

<!-- Invitation Details Modal -->
<div class="modal fade" id="invitationDetailsModal" tabindex="-1"
	aria-labelledby="invitationDetailsModalLabel" aria-hidden="true">
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
