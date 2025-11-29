@extends('layouts.app')

@section('title', __('admin.add-new'))
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

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-muted">{{__('admin.home')}}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('notifications.index') }}" class="text-muted">{{__('admin.notifications')}}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="" class="text-muted">{{__('admin.add-new')}}</a>
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
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger inverse alert-dismissible fade show" role="alert"><i
                                    class="icon-thumb-down"></i>

                                <p>{{ $error }}</p>
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close"
                                        data-original-title="" title=""><span aria-hidden="true">×</span></button>

                            </div>

                        @endforeach
                    @endif
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" href="#buy" role="tab">
                                عربي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#sell" role="tab">
                                English
                            </a>
                        </li>
                    </ul>

                    <form action="{{route('notifications.store')}}" method="post" enctype="multipart/form-data">

                        @csrf

                        <div class="tab-content crypto-buy-sell-nav-content p-4">
                            <div class="tab-pane active" id="buy" role="tabpanel">

                                <div class="row">


                                    <div class="col-sm-12">


                                        <div class="mb-3">
                                            <label for="title-input"
                                                   class="form-label">   {{__('admin.title-text')}}  </label>
                                            <input type="text" name="ar[title]" value="{{old('ar[title]')}}" required
                                                   class="form-control" id="title-input"
                                                   placeholder="{{__('admin.title-text')}}">
                                        </div>

                                    </div>

                                </div>
                                <div class="row">


                                    <div class="col-sm-12">

                                        <div class="mb-3">
                                            <label for="description"
                                                   class="form-label">     {{__('admin.description')}}   </label>
                                            <textarea class="form-control" id="description"
                                                      name="ar[description]" rows="10"
                                                      placeholder=" {{__('admin.description')}}">{{old('ar.description')}}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane" id="sell" role="tabpanel">
                                <div class="row">


                                    <div class="col-sm-12">


                                        <div class="mb-3">
                                            <label for="english-title-input"
                                                   class="form-label">   {{__('admin.english-title')}}  </label>
                                            <input type="text" name="en[title]" value="{{old('en.title')}}" required
                                                   class="form-control" id="english-title-input"
                                                   placeholder="{{__('admin.english-title')}}">
                                        </div>

                                    </div>
                                </div>
                                <div class="row">


                                    <div class="col-sm-12">

                                        <div class="mb-3">
                                            <label for="english-description"
                                                   class="form-label">     {{__('admin.english-description')}}   </label>
                                            <textarea class="form-control" id="english-description"
                                                      name="en[description]" rows="10"
                                                      placeholder=" {{__('admin.english-description')}}">{{old('en.description')}}</textarea>
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
