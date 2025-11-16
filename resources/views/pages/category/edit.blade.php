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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.categories')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.categories')}}</li>
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


                    <form action="{{route('category.update',$category->id)}}" method="post"
                          enctype="multipart/form-data">
                        <div class="tab-content crypto-buy-sell-nav-content p-4">

                            @csrf
                            @method('patch')
                            <input type="hidden" name="update" value="1">
                            <div class="tab-pane active" id="buy" role="tabpanel">

                                <div class="row">


                                    <div class="col-sm-12">

                                        <div class="mb-3">
                                            <label for="formrow-title-input"
                                                   class="form-label">   {{__('admin.title')}}  </label>
                                            <input type="text" required name="ar[title]"
                                                   value="{{$category->getTranslation('ar')->title ?? ''}}"
                                                   class="form-control" id="formrow-title-input"
                                                   placeholder="{{__('admin.title')}}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="formrow-firstname-input"
                                                   class="form-label">   {{__('admin.name')}}  </label>
                                            <input type="text" required name="ar[name]"
                                                   value="{{$category->getTranslation('ar')->name}}"
                                                   class="form-control" id="formrow-firstname-input"
                                                   placeholder="{{__('admin.name')}}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="formrow-description-input"
                                                   class="form-label">   {{__('admin.description')}}  </label>
                                            <textarea required name="ar[description]" class="form-control" id="formrow-description-input" rows="4"
                                                      placeholder="{{__('admin.description')}}">{{$category->getTranslation('ar')->description ?? ''}}</textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="sell" role="tabpanel">

                                <div class="row">


                                    <div class="col-sm-12">

                                        <div class="mb-3">
                                            <label for="formrow-english-title-input"
                                                   class="form-label">   {{__('admin.english-title')}}  </label>
                                            <input type="text" required name="en[title]"
                                                   value="{{$category->getTranslation('en') ? $category->getTranslation('en')->title : ''}}"
                                                   class="form-control" id="formrow-english-title-input"
                                                   placeholder="{{__('admin.title')}}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="formrow-firstname-input"
                                                   class="form-label">   {{__('admin.english-name')}}  </label>
                                            <input type="text" required name="en[name]"
                                                   value="{{$category->getTranslation('en')?$category->getTranslation('en')->name:''}}"
                                                   class="form-control" id="formrow-firstname-input"
                                                   placeholder="{{__('admin.english-name')}}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="formrow-description-input-en"
                                                   class="form-label">   {{__('admin.english-description')}}  </label>
                                            <textarea name="en[description]" class="form-control" id="formrow-description-input-en" rows="4"
                                                      placeholder="{{__('admin.description')}}">{{$category->getTranslation('en') ? $category->getTranslation('en')->description ?? '' : ''}}</textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <img style="width: 150px;height: 150px;" src="{{$category->image()}}">
                            </div>

                            <div class="mb-3">
                                <label for="formrow-firstname-input"
                                       class="form-label">    {{__('admin.img')}}  </label>
                                <input class="form-control" type="file" name="image" id="formFile">
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                        class="btn btn-primary waves-effect waves-light"> {{__('admin.add')}}</button>

                            </div>
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
