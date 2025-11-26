@extends('layouts.app')
@section('extra-css')
    <link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" id="bootstrap-style" rel="stylesheet"
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
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active">{{__('admin.users')}}</li>
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
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger inverse alert-dismissible fade show" role="alert"><i class="icon-thumb-down"></i>

                                <p>{{ $error }}</p>
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" data-original-title="" title=""><span aria-hidden="true">×</span></button>

                            </div>

                        @endforeach
                    @endif



                    <form action="{{route('users.update',$user->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="updateuser" value="1">

                        <div class="row">


                            <div class="col-sm-12">


                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">   {{__('admin.name')}}  </label>
                                    <input type="text" required name="name" value="{{$user->name}}" class="form-control" id="formrow-firstname-input" placeholder="{{__('admin.name')}}">
                                </div>

                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">    {{__('admin.email')}}  </label>
                                    <input class="form-control" required name="email" value="{{$user->email}}"  type="email" id="formFile">
                                </div>
                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">    {{__('admin.password')}}  </label>
                                    <input class="form-control"  name="password"  type="password"  id="formFile">
                                </div>
                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">    {{__('admin.code')}}  </label>
                                    <input class="form-control" disabled  value="{{$user->country_code}}">
                                </div>
                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">    {{__('admin.phone')}}  </label>
                                    <input class="form-control" disabled  value="{{$user->phone}}"  type="number"  id="formFile">
                                </div>


                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="formrow-firstname-input" class="form-label">    {{__('admin.img')}}  </label>
                            <input class="form-control" type="file" name="img" id="formFile" accept="image/*">
                            <small class="form-text text-muted">{{__('admin.image-help')}}</small>
                        </div>

                        <div class="col-lg-3">
                            <img style="width: 150px;height: 150px;padding-bottom: 15px;" src="{{$user->image()}}">
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light"> {{__('admin.update')}}</button>

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
