@extends('layouts.app')
@section('extra-css')
@endsection
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Upload Media</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{route('media.index')}}">Media</a></li>
                        <li class="breadcrumb-item active">Upload</li>
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

                        <form action="{{route('media.store')}}" method="post" enctype="multipart/form-data">

                            <div class="tab-content crypto-buy-sell-nav-content p-4">
                                @csrf
                                <div class="tab-pane active" id="buy" role="tabpanel">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="file" class="form-label">Media File <span class="text-danger">*</span></label>
                                                <input type="file" name="file" accept="image/*,video/*,audio/*"
                                                       class="form-control" id="file" required>
                                                <small class="form-text text-muted">Images, Videos, Audio files. Max 10MB</small>
                                                <div id="filePreview" class="mt-3"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="bucket_name" class="form-label">Folder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="bucket_name" value="{{old('bucket_name')}}"
                                                       class="form-control" id="bucket_name"
                                                       placeholder="e.g., media, uploads, images" required>
                                                <small class="form-text text-muted">Folder where the file will be stored</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="file_type" class="form-label">File Type</label>
                                                <select id="file_type" class="form-select" name="file_type">
                                                    <option value="">Auto-detect</option>
                                                    <option value="1" {{old('file_type') == '1' ? 'selected' : ''}}>Image</option>
                                                    <option value="2" {{old('file_type') == '2' ? 'selected' : ''}}>Video</option>
                                                    <option value="3" {{old('file_type') == '3' ? 'selected' : ''}}>Audio</option>
                                                    <option value="4" {{old('file_type') == '4' ? 'selected' : ''}}>GIF</option>
                                                </select>
                                                <small class="form-text text-muted">Leave as auto-detect to determine from file</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="file_key" class="form-label">File Key</label>
                                                <select id="file_key" class="form-select" name="file_key">
                                                    <option value="2" {{old('file_key', '2') == '2' ? 'selected' : ''}}>Not Main</option>
                                                    <option value="1" {{old('file_key') == '1' ? 'selected' : ''}}>Main</option>
                                                    <option value="3" {{old('file_key') == '3' ? 'selected' : ''}}>Receipt</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="file_name" class="form-label">File Name (Optional)</label>
                                                <input type="text" name="file_name" value="{{old('file_name')}}"
                                                       class="form-control" id="file_name"
                                                       placeholder="Enter custom file name (defaults to original file name)">
                                                <small class="form-text text-muted">Custom name for the file. If left empty, the original file name will be used.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                           <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                        class="btn btn-primary waves-effect waves-light"> Upload Media</button>
                                <a href="{{route('media.index')}}"
                                   class="btn btn-secondary waves-effect waves-light">Cancel</a>

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
    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';
            
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    preview.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-video"></i> Video file selected: ' + file.name + '</div>';
                } else if (file.type.startsWith('audio/')) {
                    preview.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-music"></i> Audio file selected: ' + file.name + '</div>';
                } else {
                    preview.innerHTML = '<div class="alert alert-warning">File selected: ' + file.name + '</div>';
                }
            }
        });
    </script>
@endsection


