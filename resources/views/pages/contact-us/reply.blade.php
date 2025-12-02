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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.reply')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.reply')}}</li>
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
                        <!-- Contact Information Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{__('admin.contact-information')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{__('admin.name')}}:</strong> {{$contact->name}}</p>
                                        <p><strong>{{__('admin.email')}}:</strong> {{$contact->email ?? __('admin.no-data-available')}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>{{__('admin.phone')}}:</strong> 
                                            <a href="tel:{{$contact->country_code}}{{$contact->phone}}" style="direction: ltr;">
                                                {{$contact->country_code}}{{$contact->phone}}
                                            </a>
                                        </p>
                                        <p><strong>{{__('admin.subject')}}:</strong> {{$contact->subject}}</p>
                                    </div>
                                    <div class="col-12">
                                        <p><strong>{{__('admin.message')}}:</strong></p>
                                        <div class="alert alert-light">
                                            {{$contact->message}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{route('contact.reply.submit')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="contact_id" value="{{$contact->id}}">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="reply" class="form-label">{{__('admin.reply')}}</label>
                                        <textarea class="form-control" id="reply"
                                                  name="message" rows="10"
                                                  placeholder="{{__('admin.reply')}}"
                                                  required>{{old('message')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="send_whatsapp" name="send_whatsapp" value="1"
                                                   {{old('send_whatsapp') ? 'checked' : ''}}>
                                            <label class="form-check-label" for="send_whatsapp">
                                                <i class="mdi mdi-whatsapp text-success"></i> 
                                                {{__('admin.send_reply_via_whatsapp')}}
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            {{__('admin.whatsapp_reply_note')}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-send"></i> {{__('admin.send_reply')}}
                                </button>
                                <a href="{{route('contact.index')}}" class="btn btn-secondary waves-effect waves-light">
                                    {{__('admin.cancel')}}
                                </a>
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


@endsection
