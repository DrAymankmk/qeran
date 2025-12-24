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
                <h4 class="mb-sm-0 font-size-18">Testimonials</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">Testimonials</li>
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
                        <div class="col-sm-12">
                            <div class="text-sm-start">
                                <a href="{{route('testimonials.create')}}"
                                   class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
                                        class="mdi mdi-plus me-1"></i> Add New Testimonial </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="testimonialsTable" class="table table-hover dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Job</th>
                                <th scope="col">Rating</th>
                                <th scope="col">Status</th>
                                <th scope="col">Order</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.actions')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($testimonials as $testimonial)

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">{{$testimonial->id}}</a></td>
                                    <td>
                                        @if($testimonial->image())
                                        <a target="_blank" href="{{$testimonial->image()}}">
                                            <img class="header-profile-user"
                                                src="{{$testimonial->image()}}"
                                                alt="Testimonial Image"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        </a>
                                        @else
                                        <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{$testimonial->name ?? 'N/A'}}</td>
                                    <td>{{$testimonial->job ?? 'N/A'}}</td>
                                    <td>
                                        @if($testimonial->rating)
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($testimonial->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{$testimonial->order}}</td>
                                    <td>
                                        {{Carbon\Carbon::parse($testimonial->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                                <a href="{{route('testimonials.edit',$testimonial->id)}}" title="{{__('admin.edit')}}" class="text-warning"><i
                                                        class="mdi mdi-file-edit-outline font-size-22"></i></a>
                                                <a onclick="openModalDelete({{$testimonial->id}})" title="{{__('admin.delete')}}" class="text-danger"><i
                                                        class="mdi mdi-trash-can-outline font-size-22"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@include('pages.testimonials.scripts.index-scripts')


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








