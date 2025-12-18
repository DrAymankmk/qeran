@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create CMS Page</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cms.pages.index')}}">CMS Pages</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('cms.pages.store')}}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{old('slug')}}" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">URL-friendly identifier (lowercase, numbers, hyphens only)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Internal Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" name="order" class="form-control" value="{{old('order', 0)}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{old('is_active', true) ? 'selected' : ''}}>Active</option>
                                    <option value="0" {{!old('is_active', true) ? 'selected' : ''}}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Content</h5>
                    
                    <!-- Language Tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-content" type="button" role="tab" aria-controls="en-content" aria-selected="true">
                                English
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar-content" type="button" role="tab" aria-controls="ar-content" aria-selected="false">
                                Arabic
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="languageTabContent">
                        <!-- English Tab -->
                        <div class="tab-pane fade show active" id="en-content" role="tabpanel" aria-labelledby="en-tab">
                            <div class="mb-3">
                                <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                <input type="text" name="en[title]" class="form-control @error('en.title') is-invalid @enderror" value="{{old('en.title')}}" required>
                                @error('en.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description (EN)</label>
                                <textarea name="en[meta_description]" class="form-control" rows="2">{{old('en.meta_description')}}</textarea>
                            </div>
                        </div>
                        
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="ar-content" role="tabpanel" aria-labelledby="ar-tab">
                            <div class="mb-3">
                                <label class="form-label">Title (AR) <span class="text-danger">*</span></label>
                                <input type="text" name="ar[title]" class="form-control @error('ar.title') is-invalid @enderror" value="{{old('ar.title')}}" required>
                                @error('ar.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description (AR)</label>
                                <textarea name="ar[meta_description]" class="form-control" rows="2">{{old('ar.meta_description')}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Create Page</button>
                        <a href="{{route('cms.pages.index')}}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

