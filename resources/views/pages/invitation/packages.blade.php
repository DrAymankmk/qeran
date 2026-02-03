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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.packages')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.packages')}}</li>
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

                            <div class="col-sm-8">
                                <div class="text-sm-end">
                                    <a href="{{route('package.create')}}"
                                       class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
                                            class="mdi mdi-plus me-1"></i> {{__('admin.add-new')}} </a>

                                </div>
                            </div><!-- end col-->
{{--                        @endcan--}}
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-hover datatable dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr class="tr-colored">
                                    <th scope="col">{{__('admin.id')}}</th>
                                    <th scope="col">{{__('admin.package-invitation-type')}}</th>
                                    <th scope="col">{{__('admin.package-type')}}</th>
                                    <th scope="col">{{__('admin.receipt-image')}}</th>
                                    <th scope="col">{{__('admin.count')}}</th>
                                    <th scope="col">{{__('admin.price')}}</th>
                                    <th scope="col">{{__('admin.extra_count')}}</th>
                                    <th scope="col">{{__('admin.extra_price')}}</th>
                                    <th scope="col">{{__('admin.free_invitations_count')}}</th>
                                    <th scope="col">{{__('admin.created_at')}}</th>
                                    <th scope="col">{{__('admin.more')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($invitationPackages))
                                @foreach($invitationPackages as $invitationPackage)

                                    <tr>
                                        <td><a href="javascript: void(0);" class="text-body fw-bold">{{$invitationPackage->id}}</a></td>
                                        <td>
                                            @if($invitationPackage->package)
                                                {{__('admin.package-type-'.$invitationPackage->package->package_type)}}
                                            @else
                                                {{__('admin.extra_package')}}
                                            @endif
                                        </td>
                                           <td>
                                            @if($invitationPackage->invitation->invitation_type)
                                                {{__('admin.invitation-type-'.$invitationPackage->invitation->invitation_type)}}
                                            @else
                                                {{""}}
                                            @endif

                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ $invitationPackage->image()}}">
                                                <img class=" header-profile-user" src="{{ $invitationPackage->image() }}"
                                                     alt="Invitation">
                                            </a>

                                        </td>
                                        <td>
                                            @if($invitationPackage->package)
                                                {{$invitationPackage->package->count}}
                                            @else
                                                {{ $invitationPackage->count }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($invitationPackage->package)
                                                {{$invitationPackage->package->price}}
                                            @else
                                                {{ $invitationPackage->price }}
                                            @endif
                                        </td>
                                          <td>
                                                {{$invitationPackage->count}}
                                        </td>
                                        <td>
                                                {{$invitationPackage->price}}
                                        </td>
                                        <td>
                                            @if($invitationPackage->package)
                                                {{$invitationPackage->package->free_invitations_count}}
                                            @else
                                                {{ 0 }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($invitationPackage->package)
                                                {{Carbon\Carbon::parse($invitationPackage->package->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                            @else
                                                {{Carbon\Carbon::parse($invitationPackage->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm"
                                                    onchange="window.location.href = '{{ route('invitations.packages.change-status') }}?' + 'invitation_package_id={{ $invitationPackage->id }}&status=' + this.value">
                                                @foreach(\App\Helpers\Constant::PAID_STATUS as $label => $value)
                                                    <option value="{{ $value }}" @selected($invitationPackage->status == $value)>
                                                        {{ __('admin.paid-status-' . $value) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    {{$invitationPackages->links()}}

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
        function openModalDelete(package_id) {
            $('.action_form').attr('action', '{{route('package.destroy', '')}}' + '/' + package_id);
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
