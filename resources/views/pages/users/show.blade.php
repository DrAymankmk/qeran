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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.users')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.users')}}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-4 align-self-end">
                        </div>
                        <div class="col-4 align-self-end">
                            <img src="{{$user->image()}}" alt="" class="img-fluid">
                        </div>
                        <div class="col-4 align-self-end">
                        </div>

                    </div>
                </div>
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{__('admin.Personal Information')}}</h4>

                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                            <tr>
                                <th scope="row">{{__('admin.name')}} :</th>
                                <td>{{$user->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{__('admin.phone')}} :</th>
                                <td>{{$user->country_code}} {{$user->phone}}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{__('admin.email')}} :</th>
                                <td>{{$user->email??__('admin.no-data-available')}} </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end card -->

            <!-- end card -->
        </div>

        <div class="col-xl-8">

{{--            <div class="row">--}}
{{--                <div class="col-md-4">--}}
{{--                    <div class="card mini-stats-wid">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="d-flex">--}}
{{--                                <div class="flex-grow-1">--}}
{{--                                    <p class="text-muted fw-medium mb-2">{{__('admin.orders-finished')}}</p>--}}
{{--                                    <h4 class="mb-0">{{count($user->finishedOrders)}}</h4>--}}
{{--                                </div>--}}

{{--                                <div class="flex-shrink-0 align-self-center">--}}
{{--                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">--}}
{{--                                                            <span class="avatar-title">--}}
{{--                                                                <i class="bx bx-cart font-size-24"></i>--}}
{{--                                                            </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-4">--}}
{{--                    <div class="card mini-stats-wid">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="d-flex">--}}
{{--                                <div class="flex-grow-1">--}}
{{--                                    <p class="text-muted fw-medium mb-2">{{__('admin.pending-orders')}}</p>--}}
{{--                                    <h4 class="mb-0">{{count($user->pendingOrders)}}</h4>--}}
{{--                                </div>--}}

{{--                                <div class="flex-shrink-0 align-self-center">--}}
{{--                                    <div class="avatar-sm mini-stat-icon rounded-circle bg-primary">--}}
{{--                                                            <span class="avatar-title">--}}
{{--                                                                <i class="bx bx-cart-alt font-size-24"></i>--}}
{{--                                                            </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-4">--}}
{{--                    <div class="card mini-stats-wid">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="d-flex">--}}
{{--                                <div class="flex-grow-1">--}}
{{--                                    <p class="text-muted fw-medium mb-2">{{__('admin.wallet')}}</p>--}}
{{--                                    <h4 class="mb-0">{{$user->wallet}} {{$currency->value}}</h4>--}}
{{--                                </div>--}}

{{--                                <div class="flex-shrink-0 align-self-center">--}}
{{--                                    <div class="avatar-sm mini-stat-icon rounded-circle bg-primary">--}}
{{--                                                            <span class="avatar-title">--}}
{{--                                                                <i class="bx bx-wallet font-size-24"></i>--}}
{{--                                                            </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{__('admin.invitations')}}</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.invitation-type')}}</th>
                                <th scope="col">{{__('admin.invitation-mime-type')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-image')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-video')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-audio')}}</th>
                                <th scope="col">{{__('admin.desc')}}</th>
                                <th scope="col">{{__('admin.receipt-image')}}</th>
                                <th scope="col">{{__('admin.paid-status')}}</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->myInvitations as $invitation)

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">{{$invitation->id}}</a>
                                    </td>
                                    <td> {{__('admin.invitation-type-'.$invitation->invitation_type)}}</td>
                                    <td> {{__('admin.media-type-'.$invitation->invitation_media_type)}}</td>
                                    <td>
                                        @if($invitation->designImage())
                                            <a target="_blank" href="{{$invitation->designImage()}}">

                                                <img class=" header-profile-user" src="{{$invitation->designImage()}}"
                                                     alt="Invitation">
                                            </a>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif
                                    </td>
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
                                        {{$invitation->description}}
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
                                    </td>
                                    <td>
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input class="form-check-input" type="checkbox"
                                                   onchange="return window.location.href = '{{route('invitations.change-status',$invitation->id)}}'"
                                                   @if($invitation->paid==1)checked="" @endif>

                                        </div>
                                    </td>


                                    <td>
                                        {{Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            {{--                                            @can('edit_categories')--}}

                                            {{--                                            <a href="{{route('invitation.edit',$invitation->id)}}"--}}
                                            {{--                                               title="{{__('admin.edit')}}" class="text-success"><i--}}
                                            {{--                                                    class="mdi mdi-pencil font-size-18"></i></a>--}}
                                            {{--                                            @endcan--}}
                                            {{--                                            @can('delete_categories')--}}

                                            <a onclick="openModalDelete({{$invitation->id}})"
                                               title="{{__('admin.delete')}}" class="text-danger"><i
                                                    class="mdi mdi-delete font-size-18"></i></a>
                                            {{--                                            @endcan--}}
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

@section('extra-js')
    <script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
    <!-- bootstrap-datepicker js -->
    <script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

    <!-- Required datatable js -->
    <script src="{{asset('admin_assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin_assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Responsive examples -->
    <script src="{{asset('admin_assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('admin_assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

    <!-- init js -->
    <script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

    <script src="{{asset('admin_assets/js/jquery.printPage.js') }}"></script>

    <script src="{{asset('admin_assets/js/print.js') }}"></script>

    <script>
        $('.btnprn').printPage();
    </script>
    <script>
        function openModalDelete(user_id) {
            $('.action_form').attr('action', '{{route('users.destroy', '')}}' + '/' + user_id);
            $('#deleteModal').modal('show');
        }
    </script>



@endsection
