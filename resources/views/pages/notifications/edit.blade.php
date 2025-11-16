@php use App\Helpers\Constant; @endphp
@extends('admin.layouts.app')

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
        <a href="{{ route('admin.categories.index') }}" class="text-muted">{{__('admin.home')}}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.notifications.index') }}" class="text-muted">{{__('admin.notifications')}}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="" class="text-muted">{{__('admin.edit')}}</a>
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
                        @foreach(Constant::LANGUAGES as $key=>$value)

                            <li class="nav-item">
                                <a class="nav-link @if($value=='ar') active show @endif " data-bs-toggle="tab"
                                   href="#{{$value}}" role="tab">
                                    {{__('admin.'.$key)}}
                                </a>
                            </li>
                        @endforeach

                    </ul>

                    <form action="{{route('admin.notifications.update',$notification->id)}}" method="post"
                          enctype="multipart/form-data">

                        <div class="tab-content crypto-buy-sell-nav-content p-4">
                            @csrf
                            @method('PATCH')
                            @foreach(Constant::LANGUAGES as $key=>$value)

                                <div class="tab-pane @if($value=='ar') active @endif" id="{{$value}}" role="tabpanel">

                                    <div class="row">


                                        <div class="col-sm-12">


                                            <div class="mb-3">
                                                <label for="formrow-firstname-input"
                                                       class="form-label">   {{__('admin.title-'.$value)}}  </label>
                                                <input type="text" name="title[{{$value}}]"
                                                       value="{{$notification->getTranslation('title',$value,'ar')}}" required
                                                       class="form-control" id="formrow-firstname-input"
                                                       placeholder="{{__('admin.title-text')}}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description"
                                                       class="form-label">     {{__('admin.description')}}   </label>
                                                <textarea class="form-control" id="description"
                                                          name="description[{{$value}}]" rows="10"
                                                          placeholder=" {{__('admin.description')}}">{{$notification->getTranslation('description',$value,'ar')}}</textarea>
                                            </div>
                                        </div>



                                    </div>
                                </div>
                            @endforeach

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
