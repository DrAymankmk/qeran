@extends('layouts.app')

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
                                                class="mdi mdi-plus me-1"></i> {{__('admin.add-new')}} </a>

                                    </div>
                                </div><!-- end col-->
{{--                            @endcan--}}
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-hover  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">{{__('admin.title-text')}}</th>
                                <th scope="col">{{__('admin.description')}}</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.more')}}</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)

                                <tr >
                                    <td>{{$notification->id}} </td>

                                    <td>{{$notification->title}}</td>
                                    <td>{{$notification->description}}</td>

                                    <td>
                                        {{$notification->created_at}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
{{--                                            @can('edit_notifications')--}}

{{--                                                <a href="{{route('notifications.edit',$notification->id)}}" title="{{__('admin.edit')}}" class="text-success"><i class="mdi mdi-pencil font-size-18"></i></a>--}}
{{--                                            @endcan--}}
{{--                                            @can('delete_notifications')--}}

                                                <a  onclick="openModalDelete({{$notification->id}})" title="{{__('admin.delete')}}" class="text-danger"><i class="mdi mdi-delete font-size-18" ></i></a>
{{--                                            @endcan--}}

                                        </div>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>
                    {{$notifications->withQueryString()->links()}}

                </div>
            </div>
        </div>
    </div>
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


    <script>
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
