
@extends('layouts.app')
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{__('admin.admin-dashboard')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('admin.admin-dashboard')}}</a></li>
                        <li class="breadcrumb-item active"> {{__('admin.dashboard')}}</li>
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
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h4 class="text-primary">{{__('admin.welcome-back')}}</h4>
                                <p>{{__('admin.admin-dashboard')}}</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{auth()->guard('admin')->user()->img??asset('admin_assets/images/admin.png')}}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{auth()->guard('admin')->user()->img??asset('admin_assets/images/admin.png')}}" alt="" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">{{auth()->guard('admin')->user()->name}}</h5>
                            {{--                                    <p class="text-muted mb-0 text-truncate">للتجارة الإلكترونية</p>--}}
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">

                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{$usersCount}}</h5>
                                        <p class="text-muted mb-0">{{__('admin.users')}}</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">  {{$invitationsCount}}
                                        </h5>
                                        <p class="text-muted mb-0">{{__('admin.invitations')}}</p>
                                    </div>
                                </div>
                                {{--                                        <div class="mt-4">--}}
                                {{--                                            <a href="" class="btn btn-primary waves-effect waves-light btn-sm">{{__('admin.my-profile')}}<i class="mdi mdi-arrow-right ms-1"></i></a>--}}
                                {{--                                        </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--                    <div class="card">--}}
            {{--                        <div class="card-body">--}}
            {{--                            <h4 class="card-title mb-4">{{__('admin.monthly-income')}}</h4>--}}
            {{--                            <div class="row">--}}
            {{--                                <div class="col-sm-6">--}}
            {{--                                    <p class="text-muted">{{__('admin.this-month')}}</p>--}}
            {{--                                    <h3>--}}

            {{--                                        {{$monthlyProfits}}--}}
            {{--                                        {{$currency->value}}--}}
            {{--                                    </h3>--}}
            {{--                                    <p class="text-muted"><span class="@if($lessOrMore=='more') text-success @else text-danger @endif me-2"> {{$monthlyPreviousProfitsPercentage}}% <i class="mdi @if($lessOrMore=='more') mdi-arrow-up @else mdi-arrow-down @endif "></i> </span> {{__('admin.previous-month')}}</p>--}}

            {{--                                    <div class="mt-4">--}}
            {{--                                        <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light btn-sm">{{__('admin.view-more')}} <i class="mdi mdi-arrow-right ms-1"></i></a>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                                <div class="col-sm-6">--}}
            {{--                                    <div class="mt-4 mt-sm-0">--}}
            {{--                                        <div id="radialBar-chart" data-colors='["--bs-primary"]' class="apex-charts"></div>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                            <p class="text-muted mb-0">--}}
            {{--                                {{__('admin.view-more')}}--}}

            {{--                            </p>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">{{__('admin.users')}}</p>
                                    <h4 class="mb-0">{{$usersCount}}</h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                            <span class="avatar-title">
                                                                <i class="bx bx-check-shield font-size-24"></i>
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
                                    <p class="text-muted fw-medium">{{__('admin.invitations')}}</p>
                                    <h4 class="mb-0"> {{$invitationsCount}}</h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center ">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                            <span class="avatar-title rounded-circle ">
                                                                <i class="bx bx-cart-alt font-size-24"></i>
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
                                    <p class="text-muted fw-medium"> {{__('admin.contact_us')}} </p>
                                    <h4 class="mb-0"> {{$contactUsCount}}</h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                            <span class="avatar-title rounded-circle ">
                                                                <i class="bx bx-analyse font-size-24"></i>
                                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--                    <div class="row">--}}
            {{--                        <div class="col-md-3">--}}
            {{--                            <div class="card mini-stats-wid">--}}
            {{--                                <div class="card-body">--}}
            {{--                                    <div class="d-flex">--}}
            {{--                                        <div class="flex-grow-1">--}}
            {{--                                            <p class="text-muted fw-medium">{{__('admin.packing-shipments')}}</p>--}}
            {{--                                            <h4 class="mb-0">{{$packingShipments}}</h4>--}}
            {{--                                        </div>--}}

            {{--                                        <div class="flex-shrink-0 align-self-center">--}}
            {{--                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">--}}
            {{--                                                            <span class="avatar-title">--}}
            {{--                                                                <i class="bx bx-check-square font-size-24"></i>--}}
            {{--                                                            </span>--}}
            {{--                                            </div>--}}
            {{--                                        </div>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                        <div class="col-md-4">--}}
            {{--                            <div class="card mini-stats-wid">--}}
            {{--                                <div class="card-body">--}}
            {{--                                    <div class="d-flex">--}}
            {{--                                        <div class="flex-grow-1">--}}
            {{--                                            <p class="text-muted fw-medium">{{__('admin.packed-shipments')}}</p>--}}
            {{--                                            <h4 class="mb-0"> {{$packedShipments}}</h4>--}}
            {{--                                        </div>--}}

            {{--                                        <div class="flex-shrink-0 align-self-center ">--}}
            {{--                                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">--}}
            {{--                                                            <span class="avatar-title rounded-circle ">--}}
            {{--                                                                <i class="bx bxs-truck font-size-24"></i>--}}
            {{--                                                            </span>--}}
            {{--                                            </div>--}}
            {{--                                        </div>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                        <div class="col-md-4">--}}
            {{--                            <div class="card mini-stats-wid">--}}
            {{--                                <div class="card-body">--}}
            {{--                                    <div class="d-flex">--}}
            {{--                                        <div class="flex-grow-1">--}}
            {{--                                            <p class="text-muted fw-medium"> {{__('admin.delivering-shipments')}} </p>--}}
            {{--                                            <h4 class="mb-0"> {{$deliveringShipments}}</h4>--}}
            {{--                                        </div>--}}

            {{--                                        <div class="flex-shrink-0 align-self-center">--}}
            {{--                                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">--}}
            {{--                                                            <span class="avatar-title rounded-circle ">--}}
            {{--                                                                <i class="bx bx-user font-size-24"></i>--}}
            {{--                                                            </span>--}}
            {{--                                            </div>--}}
            {{--                                        </div>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
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

                            <div id="users-chart" class="apex-charts" data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
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

                                <div id="invitations-chart" class="apex-charts" data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
                            </div>
                        </div>
                </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4"> {{__('admin.invitations')}} </h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.user')}}</th>
                                <th scope="col">{{__('admin.invitation-type')}}</th>
                                <th scope="col">{{__('admin.name')}}</th>
                                <th scope="col">{{__('admin.invitation-mime-type')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-image')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-video')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-audio')}}</th>
                                <th scope="col">{{__('admin.desc')}}</th>
                                <th scope="col">{{__('admin.receipt-image')}}</th>
{{--                                <th scope="col">{{__('admin.paid-status')}}</th>--}}
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invitations as $invitation)

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">{{$invitation->id}}</a>
                                    </td>
                                    <td> {{__('admin.invitation-type-'.$invitation->invitation_type)}}</td>
                                    <td><a href="{{route('users.show',$invitation->user_id)}}" target="_blank">
                                            {{$invitation->user?->name}}
                                        </a></td>
                                    <td> {{$invitation->name}}</td>
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
{{--                                    <td>--}}
{{--                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">--}}
{{--                                            <input class="form-check-input" type="checkbox"--}}
{{--                                                   onchange="return window.location.href = '{{route('invitations.change-status',$invitation->id)}}'"--}}
{{--                                                   @if($invitation->paid==1)checked="" @endif>--}}

{{--                                        </div>--}}
{{--                                    </td>--}}


                                    <td>
                                        {{Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="{{route('invitation.edit',$invitation->id)}}"
                                               title="{{__('admin.edit')}}" class="text-success"><i
                                                    class="mdi mdi-pencil font-size-18"></i></a>

{{--                                            <a href="{{route('package.index',['invitation_id'=>$invitation->id])}}"--}}
{{--                                               title="{{__('admin.packages')}}" class="text-success"><i--}}
{{--                                                    class="mdi mdi-package font-size-18"></i></a>--}}

                                            <a href="{{route('invitations.getPackagesByInvitationId',['invitation_id'=>$invitation->id])}}"
                                               title="{{__('admin.packages')}}" class="text-success"><i
                                                        class="mdi mdi-package font-size-18"></i></a>


                                            <a onclick="openModalDelete({{$invitation->id}})"
                                               title="{{__('admin.delete')}}" class="text-danger"><i
                                                    class="mdi mdi-delete font-size-18"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4"> {{__('admin.invitation-requests')}} </h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.user')}}</th>
                                <th scope="col">{{__('admin.name')}}</th>
                                <th scope="col">{{__('admin.invitation-mime-type')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-image')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-video')}}</th>
                                <th scope="col">{{__('admin.invitation-uploaded-audio')}}</th>
                                <th scope="col">{{__('admin.desc')}}</th>
                                <th scope="col">{{__('admin.receipt-image')}}</th>
{{--                                <th scope="col">{{__('admin.paid-status')}}</th>--}}
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requestInvitations as $requestInvitation)

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">{{$requestInvitation->id}}</a>
                                    </td>
                                    <td> <a href="{{route('users.show',$requestInvitation->user_id)}}" target="_blank">
                                            {{$requestInvitation->user?->name}}
                                        </a></td>

                                    <td> {{$requestInvitation->name}}</td>
                                    <td> {{__('admin.media-type-'.$requestInvitation->invitation_media_type)}}</td>
                                    <td>
                                        @if($requestInvitation->designImage())
                                            <a target="_blank" href="{{$requestInvitation->designImage()}}">

                                                <img class=" header-profile-user" src="{{$invitation->designImage()}}"
                                                     alt="Invitation">
                                            </a>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($requestInvitation->designVideo())

                                            <video width="150" height="150" controls>
                                                <source src="{{$requestInvitation->designVideo()}}" type="video/mp4">
                                                <source src="{{$requestInvitation->designVideo()}}" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif

                                    </td>
                                    <td>
                                        @if($requestInvitation->designAudio())

                                            <audio controls>
                                                <source src="{{$requestInvitation->designAudio()}}" type="audio/ogg">
                                                <source src="{{$requestInvitation->designAudio()}}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif

                                    </td>
                                    <td>
                                        {{$requestInvitation->description}}
                                    </td>
                                    <td>
                                        @if($requestInvitation->receiptImage())
                                            <a target="_blank" href="{{$requestInvitation->receiptImage()}}">

                                                <img class=" header-profile-user" src="{{$requestInvitation->receiptImage()}}"
                                                     alt="Invitation">
                                            </a>
                                        @else
                                            {{__('admin.no-data-available')}}
                                        @endif
                                    </td>
{{--                                    <td>--}}
{{--                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">--}}
{{--                                            <input class="form-check-input" type="checkbox"--}}
{{--                                                   onchange="return window.location.href = '{{route('invitations.change-status',$requestInvitation->id)}}'"--}}
{{--                                                   @if($requestInvitation->paid==1)checked="" @endif>--}}

{{--                                        </div>--}}
{{--                                    </td>--}}


                                    <td>
                                        {{Carbon\Carbon::parse($requestInvitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            {{--                                            @can('edit_categories')--}}

                                            <a href="{{route('invitation.edit',$requestInvitation->id)}}"
                                               title="{{__('admin.edit')}}" class="text-success"><i
                                                    class="mdi mdi-pencil font-size-18"></i></a>
                                            {{--                                            @endcan--}}
                                            {{--                                            @can('delete_categories')--}}

                                            <a href="{{route('invitations.getPackagesByInvitationId',['invitation_id'=>$requestInvitation->id])}}"
                                               title="{{__('admin.packages')}}" class="text-success"><i
                                                        class="mdi mdi-package font-size-18"></i></a>


                                            <a onclick="openModalDelete({{$requestInvitation->id}})"
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
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4"> {{__('admin.contact_us')}} </h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check">
                            <thead class="table-light">
                            <tr>


                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.name')}}</th>
                                <th scope="col">{{__('admin.phone')}}</th>
                                <th scope="col">{{__('admin.message')}}</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($contactUs as $contact)

                                <tr>
                                    <td><a  class="text-body fw-bold">{{$contact->id}}</a></td>
                                    <td>
                                        {{$contact->name}}
                                    </td>
                                    <td>
                                        {{$contact->phone}}
                                    </td>
                                    <td>
                                        {{$contact->message}}
                                    </td>
                                    <td>
                                        {{Carbon\Carbon::parse($contact->created_at)->locale('ar')->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>

    <!-- end row -->

@endsection
@section('extra-js')
    <script src="{{asset('admin_assets/libs/apexcharts/apexcharts.min.js')}}"></script>
        <script>
            function getChartColorsArray(e) {
                if (null !== document.getElementById(e)) {
                    var t = document.getElementById(e).getAttribute("data-colors");
                    if (t) return (t = JSON.parse(t)).map(function (e) {
                        var t = e.replace(" ", "");
                        if (-1 === t.indexOf(",")) {
                            var r = getComputedStyle(document.documentElement).getPropertyValue(t);
                            return r || t
                        }
                        var a = e.split(",");
                        return 2 != a.length ? t : "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(a[0]) + "," + a[1] + ")"
                    })
                }
            }
            setTimeout(function () {
                $("#subscribeModal").modal("show")
            }, 2e3);
            var linechartBasicColors = getChartColorsArray("users-chart");
            linechartBasicColors && (options = {
                chart: {
                    height: 360,
                    type: "bar",
                    stacked: !0,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !0
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "15%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                series: [{
                    name: "{{__('admin.verified')}}",
                    data: [
                        {{$verifiedUsers11}},
                        {{$verifiedUsers10}},
                        {{$verifiedUsers9}},
                        {{$verifiedUsers8}},
                        {{$verifiedUsers7}},
                        {{$verifiedUsers6}},
                        {{$verifiedUsers5}},
                        {{$verifiedUsers4}},
                        {{$verifiedUsers3}},
                        {{$verifiedUsers2}},
                        {{$verifiedUsers1}},
                        {{$verifiedUsers}}]
                }, {
                    name: "{{__('admin.not-verified')}}",
                    data: [
                        {{$notVerifiedUsers11}},
                        {{$notVerifiedUsers10}},
                        {{$notVerifiedUsers9}},
                        {{$notVerifiedUsers8}},
                        {{$notVerifiedUsers7}},
                        {{$notVerifiedUsers6}},
                        {{$notVerifiedUsers5}},
                        {{$notVerifiedUsers4}},
                        {{$notVerifiedUsers3}},
                        {{$notVerifiedUsers2}},
                        {{$notVerifiedUsers1}},
                        {{$notVerifiedUsers}}]
                }
                ],
                xaxis: {
                    categories: [
                        "{{Carbon\Carbon::now()->subMonths(11)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(10)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(9)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(8)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(7)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(6)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(5)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(4)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(3)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(2)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonth()->locale('ar')->translatedFormat('F')}}"
                        ,"{{Carbon\Carbon::now()->locale('ar')->translatedFormat('F')}}"
                    ]


                },
                colors: linechartBasicColors,
                legend: {
                    position: "bottom"
                },
                fill: {
                    opacity: 1
                }
            }

                , (chart = new ApexCharts(document.querySelector("#users-chart"), options)).render());
            var linechartBasicColors = getChartColorsArray("invitations-chart");
            linechartBasicColors && (options = {
                chart: {
                    height: 360,
                    type: "bar",
                    stacked: !0,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !0
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "15%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                series: [{
                    name: "{{__('admin.app-design')}}",
                    data: [
                        {{$invitationsAppDesign11}},
                        {{$invitationsAppDesign10}},
                        {{$invitationsAppDesign9}},
                        {{$invitationsAppDesign8}},
                        {{$invitationsAppDesign7}},
                        {{$invitationsAppDesign6}},
                        {{$invitationsAppDesign5}},
                        {{$invitationsAppDesign4}},
                        {{$invitationsAppDesign3}},
                        {{$invitationsAppDesign2}},
                        {{$invitationsAppDesign1}},
                        {{$invitationsAppDesign}}]
                }, {
                    name: "{{__('admin.contact-design')}}",
                    data: [
                        {{$invitationsContactDesign11}},
                        {{$invitationsContactDesign10}},
                        {{$invitationsContactDesign9}},
                        {{$invitationsContactDesign8}},
                        {{$invitationsContactDesign7}},
                        {{$invitationsContactDesign6}},
                        {{$invitationsContactDesign5}},
                        {{$invitationsContactDesign4}},
                        {{$invitationsContactDesign3}},
                        {{$invitationsContactDesign2}},
                        {{$invitationsContactDesign1}},
                        {{$invitationsContactDesign}}]
                }, {
                    name: "{{__('admin.user-design')}}",
                    data: [
                        {{$invitationsUserDesign11}},
                        {{$invitationsUserDesign10}},
                        {{$invitationsUserDesign9}},
                        {{$invitationsUserDesign8}},
                        {{$invitationsUserDesign7}},
                        {{$invitationsUserDesign6}},
                        {{$invitationsUserDesign5}},
                        {{$invitationsUserDesign4}},
                        {{$invitationsUserDesign3}},
                        {{$invitationsUserDesign2}},
                        {{$invitationsUserDesign1}},
                        {{$invitationsUserDesign}}]
                }
                ],
                xaxis: {
                    categories: [
                        "{{Carbon\Carbon::now()->subMonths(11)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(10)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(9)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(8)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(7)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(6)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(5)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(4)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(3)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonths(2)->locale('ar')->translatedFormat('F')}}",
                        "{{Carbon\Carbon::now()->subMonth()->locale('ar')->translatedFormat('F')}}"
                        ,"{{Carbon\Carbon::now()->locale('ar')->translatedFormat('F')}}"
                    ]


                },
                colors: linechartBasicColors,
                legend: {
                    position: "bottom"
                },
                fill: {
                    opacity: 1
                }
            }

                , (chart = new ApexCharts(document.querySelector("#invitations-chart"), options)).render());

        </script>

    <!-- dashboard init -->


@endsection
