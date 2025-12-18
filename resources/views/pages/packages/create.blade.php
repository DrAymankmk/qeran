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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

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
                    <div class="crypto-buy-sell-nav">

                        <form action="{{route('package.store')}}" method="post" enctype="multipart/form-data">

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
                                                    <option value="{{$type}}">{{__('admin.invitation-type-'.$type)}}</option>
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
                                                    <option value="{{$type}}">{{__('admin.package-type-'.$type)}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="formrow-firstname-input"
                                                       class="form-label">   {{__('admin.count')}}  </label>
                                                <input type="number" name="count" value="{{old('count')}}" required
                                                       class="form-control" id="formrow-firstname-input"
                                                       placeholder="{{__('admin.count')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="formrow-firstname-input"
                                                       class="form-label">   {{__('admin.price')}}  </label>
                                                <input type="number" name="price" value="{{old('price')}}" required
                                                       class="form-control" id="formrow-firstname-input"
                                                       placeholder="{{__('admin.price')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="formrow-firstname-input"
                                                       class="form-label">   {{__('admin.free_invitations_count')}}  </label>
                                                <input type="number" name="free_invitations_count" value="{{old('free_invitations_count')}}" required
                                                       class="form-control" id="formrow-firstname-input"
                                                       placeholder="{{__('admin.free_invitations_count')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs mt-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">
                                                English
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">
                                                العربية
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-3" id="languageTabContent">
                                        <!-- English Tab -->
                                        <div class="tab-pane fade show active" id="en" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="en-title" class="form-label">Title (EN)</label>
                                                        <input type="text" name="en[title]" value="{{old('en.title')}}"
                                                               class="form-control" id="en-title"
                                                               placeholder="Package Title">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="en-subtitle" class="form-label">Subtitle (EN)</label>
                                                        <input type="text" name="en[subtitle]" value="{{old('en.subtitle')}}"
                                                               class="form-control" id="en-subtitle"
                                                               placeholder="Package Subtitle">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="en-content" class="form-label">Content (EN)</label>
                                                        <textarea name="en[content]" class="form-control summernote" id="en-content" rows="5"
                                                                  placeholder="Package Content">{{old('en.content')}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Arabic Tab -->
                                        <div class="tab-pane fade" id="ar" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="ar-title" class="form-label">العنوان (AR)</label>
                                                        <input type="text" name="ar[title]" value="{{old('ar.title')}}"
                                                               class="form-control" id="ar-title"
                                                               placeholder="عنوان الباقة">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="ar-subtitle" class="form-label">العنوان الفرعي (AR)</label>
                                                        <input type="text" name="ar[subtitle]" value="{{old('ar.subtitle')}}"
                                                               class="form-control" id="ar-subtitle"
                                                               placeholder="العنوان الفرعي للباقة">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="ar-content" class="form-label">المحتوى (AR)</label>
                                                        <textarea name="ar[content]" class="form-control summernote" id="ar-content" rows="5"
                                                                  placeholder="محتوى الباقة">{{old('ar.content')}}</textarea>
                                                    </div>
                                                </div>
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

    <!-- Summernote Editor -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        // Initialize Summernote editors
        $(document).ready(function() {
            function initEditors() {
                $('.summernote').summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }

            function reinitEditors() {
                $('.summernote').summernote('destroy');
                initEditors();
            }

            initEditors();

            // Re-init when switching tabs to handle hidden editors
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                reinitEditors();
            });
        });
    </script>

@endsection
