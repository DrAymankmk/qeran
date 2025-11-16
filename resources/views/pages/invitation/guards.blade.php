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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.guards')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.guards')}}</li>
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
                        <div class="col-sm-8">

{{--                            <div class="text-sm-end">--}}
{{--                                <button type="button" onclick="window.location='{{route('users.download')}}'"--}}
{{--                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i--}}
{{--                                        class="mdi mdi-microsoft-excel me-1"></i> {{__('admin.download-excel')}}--}}
{{--                                </button>--}}
{{--                            </div>--}}
                        </div><!-- end col-->
                    </div>
                    {{--                    <form id="form-change"  method="post">--}}
                    {{--                        @csrf--}}
                    {{--                        <div class="row">--}}

                    {{--                            <div class="col-md-5">--}}
                    {{--                                <div class="mb-3">--}}
                    {{--                                    <label for="formrow-email-input" class="form-label">{{__('admin.from-date')}}</label>--}}
                    {{--                                    <input  name="from" class="form-control" type="date" value="2019-08-19"--}}
                    {{--                                            id="example-date-input">--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                            <div class="col-md-5">--}}
                    {{--                                <div class="mb-3">--}}
                    {{--                                    <label for="formrow-password-input" class="form-label">{{__('admin.to-date')}}</label>--}}
                    {{--                                    <input name="to" class="form-control" type="date" value="2019-08-19"--}}
                    {{--                                           id="example-date-input">--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                            <div class="col-md-2 ">--}}
                    {{--                                <div class="d-grid">--}}
                    {{--                                    <label for="formrow-email-input" class="form-label hidden">{{__('admin.search')}}</label>--}}
                    {{--                                    <input data-repeater-delete="" type="button" id="search" class="btn btn-primary inner" value="{{__('admin.search')}}">--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </form>--}}
                    <div class="table-responsive mt-2">
                        <table class="table table-hover datatable dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.img')}}</th>

                                <th scope="col">{{__('admin.name')}}</th>
                                <th scope="col">{{__('admin.email')}}</th>
                                <th scope="col">{{__('admin.phone')}}</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($guards as $guard)

                                <tr @if($guard->verified_status==2) style=" background: #f38e8e;" @endif>
                                    <td><a href="{{route('users.show',$guard->id)}}" class="text-body fw-bold">{{$guard->id}}</a></td>
                                    <td>
                                        <a href="{{$guard->image()}}" target="_blank" class="text-body fw-bold">

                                        <img class="rounded-circle header-profile-user" src="{{$guard->image()}}"
                                             alt="Header Avatar">
                                        </a>
                                    </td>

                                    <td>{{$guard->name}}</td>
                                    <td>{{$guard->email??__('admin.no-data-available')}}  </td>
                                    <td style="direction: ltr;"><a href="tel:{{$guard->phone}}{{$guard->country_code}}"> {{$guard->country_code}}{{$guard->phone}} </a></td>

                                    <td>
                                        {{Carbon\Carbon::parse($guard->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
{{--                                            @can('edit_terms')--}}

{{--                                                <a href="javascript:void(0);" class="text-success"><i--}}
{{--                                                        class="mdi mdi-pencil font-size-18"></i></a>--}}
{{--                                            @endcan--}}



                                                <a href="{{route('users.edit',$guard->id)}}" title="{{__('admin.edit')}}" class="text-success"><i
                                                        class="mdi mdi-pencil font-size-18"></i></a>


                                        </div>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>

                    </div>
                    {{$guards->links()}}

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

    <script>
        function openModalDelete(user_id) {
            $('.action_form').attr('action', '{{route('users.destroy', '')}}' + '/' + user_id);
            $('#deleteModal').modal('show');
        }
    </script>



@endsection
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
