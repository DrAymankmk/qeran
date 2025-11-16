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
                    <form action="{{route('package.update',$package->id)}}" method="post" enctype="multipart/form-data">
                     @method('PATCH')
                        <div class="tab-content crypto-buy-sell-nav-content p-4">
                            @csrf
                            <div class="tab-pane active" id="buy" role="tabpanel">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="formrow-inputState"
                                               class="form-label">     {{__('admin.package-invitation-type')}}  </label>
                                        <select id="formrow-inputState" class="form-select" name="package_invitation_type">
                                            <option selected="" disabled> {{__('admin.choose-package-invitation-type')}}</option>
                                            @foreach(\App\Helpers\Constant::INVITATION_TYPE as $key=>$type)
                                                <option value="{{$type}}" @if($package->package_invitation_type==$type) selected @endif>{{__('admin.invitation-type-'.$type)}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="formrow-inputState"
                                               class="form-label">     {{__('admin.package-type')}}  </label>
                                        <select id="formrow-inputState" class="form-select" name="package_type">
                                            <option selected="" disabled> {{__('admin.choose-package-type')}}</option>
                                            @foreach(\App\Helpers\Constant::PACKAGE_TYPE as $key=>$type)
                                                <option value="{{$type}}"@if($package->package_type==$type) selected @endif>{{__('admin.package-type-'.$type)}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="row">


                                    <div class="col-sm-12">


                                        <div class="mb-3">
                                            <label for="formrow-firstname-input"
                                                   class="form-label">   {{__('admin.count')}}  </label>
                                            <input type="number" name="count" value="{{$package->count}}" required
                                                   class="form-control" id="formrow-firstname-input"
                                                   placeholder="{{__('admin.count')}}">
                                        </div>

                                    </div>
                                    <div class="col-sm-12">


                                        <div class="mb-3">
                                            <label for="formrow-firstname-input"
                                                   class="form-label">   {{__('admin.price')}}  </label>
                                            <input type="number" name="price" value="{{$package->price}}" required
                                                   class="form-control" id="formrow-firstname-input"
                                                   placeholder="{{__('admin.price')}}">
                                        </div>

                                    </div>
                                    <div class="col-sm-12">


                                        <div class="mb-3">
                                            <label for="formrow-firstname-input"
                                                   class="form-label">   {{__('admin.free_invitations_count')}}  </label>
                                            <input type="number" name="free_invitations_count" value="{{$package->free_invitations_count}}" required
                                                   class="form-control" id="formrow-firstname-input"
                                                   placeholder="{{__('admin.free_invitations_count')}}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                                                    <div class="d-flex flex-wrap gap-2">
                            <button type="submit"
                                    class="btn btn-primary waves-effect waves-light"> {{__('admin.add')}}</button>

                        </div>

                    </form>

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


@endsection
