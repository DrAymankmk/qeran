@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Sections - {{ $page->name }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cms.pages.index')}}">CMS Pages</a></li>
                    <li class="breadcrumb-item active">Sections</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{route('cms.pages.index')}}" class="btn btn-secondary btn-rounded waves-effect waves-light mb-2">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Pages
                        </a>
                        <a href="{{route('cms.sections.create', $page)}}" class="btn btn-primary btn-rounded waves-effect waves-light mb-2">
                            <i class="mdi mdi-plus me-1"></i> Add New Section
                        </a>
                        <a href="{{route('cms.links.index', ['page', $page->id])}}" class="btn btn-info btn-rounded waves-effect waves-light mb-2">
                            <i class="mdi mdi-link me-1"></i> Manage Links
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Title (EN)</th>
                                <th>Title (AR)</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sections as $section)
                                <tr>
                                    <td>{{ $section->id }}</td>
                                    <td>{{ $section->name }}</td>
                                    <td>{{ $section->type }}</td>
                                    <td>{{ $section->translate('en')->title ?? '-' }}</td>
                                    <td>{{ $section->translate('ar')->title ?? '-' }}</td>
                                    <td>{{ $section->order }}</td>
                                    <td>
                                        <span class="badge bg-{{ $section->is_active ? 'success' : 'danger' }}">
                                            {{ $section->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{route('cms.items.index', [$page, $section])}}" class="btn btn-sm btn-info" title="Items">
                                            <i class="mdi mdi-file-multiple"></i>
                                        </a>
                                        <a href="{{route('cms.links.index', ['section', $section->id])}}" class="btn btn-sm btn-warning" title="Links">
                                            <i class="mdi mdi-link"></i>
                                        </a>
                                        <a href="{{route('cms.sections.edit', [$page, $section])}}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{route('cms.sections.destroy', [$page, $section])}}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No sections found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






