@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Link - {{ ucfirst($type) }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cms.pages.index')}}">CMS Pages</a></li>
                    <li class="breadcrumb-item active">Edit Link</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('cms.links.update', [$type, $id, $link])}}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name', $link->name)}}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Link/URL <span class="text-danger">*</span></label>
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{old('link', $link->link)}}" required>
                                @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="{{old('icon', $link->icon)}}" placeholder="e.g., fab fa-facebook, fas fa-envelope">
                                <small class="form-text text-muted">FontAwesome class or icon path</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{old('type', $link->type)}}" placeholder="e.g., social, contact, quick">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Target</label>
                                <select name="target" class="form-select">
                                    <option value="_self" {{old('target', $link->target) == '_self' ? 'selected' : ''}}>Same Window</option>
                                    <option value="_blank" {{old('target', $link->target) == '_blank' ? 'selected' : ''}}>New Window</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" name="order" class="form-control" value="{{old('order', $link->order)}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{old('is_active', $link->is_active) ? 'selected' : ''}}>Active</option>
                                    <option value="0" {{!old('is_active', $link->is_active) ? 'selected' : ''}}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update Link</button>
                        <a href="{{route('cms.links.index', [$type, $id])}}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
















