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
                <h4 class="mb-sm-0 font-size-18">Designs
                    @if($category)
                        - {{ $category->name }}
                    @endif
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{route('category.index')}}">Categories</a></li>
                        @if($category)
                        <li class="breadcrumb-item active">Designs - {{ $category->name }}</li>
                        @else
                        <li class="breadcrumb-item active">Designs</li>
                        @endif
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

                            <div class="col-sm-12">
                                <div class="text-sm-start">
                                    <a href="{{route('designs.create', ['category_id' => $category?->id])}}"
                                       class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
                                            class="mdi mdi-plus me-1"></i> Add New Design </a>
                                    @if($category)
                                    <a href="{{route('category.index')}}"
                                       class="btn btn-secondary btn-rounded waves-effect waves-light mb-2 me-2"><i
                                            class="mdi mdi-arrow-left me-1"></i> Back to Categories </a>
                                    @endif
                                </div>
                            </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="designsTable" class="table table-hover dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="tr-colored">
                                <th scope="col">{{__('admin.id')}}</th>
                                <th scope="col">Image</th>
                                <th scope="col">Category</th>
                                <th scope="col">Name</th>
                                <th scope="col">Code</th>
                                <th scope="col">{{__('admin.created_at')}}</th>
                                <th scope="col">{{__('admin.actions')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($designs as $design)

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">{{$design->id}}</a></td>
                                    <td>
                                        @if($design->image())
                                        <a target="_blank" href="{{$design->image()}}">
                                            <img class="header-profile-user"
                                                src="{{$design->image()}}"
                                                alt="Design Image"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                        @else
                                        <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{$design->category->name ?? 'N/A'}}</td>
                                    <td>{{$design->name ?? 'N/A'}}</td>
                                    <td>{{$design->code ?? 'N/A'}}</td>
                                    <td>
                                        {{Carbon\Carbon::parse($design->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                                <a href="{{route('designs.edit',$design->id)}}" title="{{__('admin.edit')}}" class="text-warning"><i
                                                        class="mdi mdi-file-edit-outline font-size-22"></i></a>
                                                <a onclick="openModalDelete({{$design->id}})" title="{{__('admin.delete')}}" class="text-danger"><i
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

@include('pages.designs.scripts.index-scripts')


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









