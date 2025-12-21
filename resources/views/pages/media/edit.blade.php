@extends('layouts.app')
@section('extra-css')
@endsection
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Media</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{route('media.index')}}">Media</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

                        <form action="{{route('media.update', $medium->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="tab-content crypto-buy-sell-nav-content p-4">
                                <div class="tab-pane active" id="buy" role="tabpanel">
                                    <div class="row">
                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label">Current File Preview</label>
                                            <div>
                                                @if($medium->file_type == 1) {{-- Image --}}
                                                    <img src="{{$medium->get_path()}}" class="img-thumbnail" style="max-width: 300px; max-height: 300px;" id="currentPreview">
                                                @elseif($medium->file_type == 2) {{-- Video --}}
                                                    <div class="alert alert-info" id="currentPreview">
                                                        <i class="mdi mdi-video font-size-24"></i> Video File
                                                        <br><a href="{{$medium->get_path()}}" target="_blank">View Video</a>
                                                    </div>
                                                @elseif($medium->file_type == 3) {{-- Audio --}}
                                                    <div class="alert alert-info" id="currentPreview">
                                                        <i class="mdi mdi-music font-size-24"></i> Audio File
                                                        <br><a href="{{$medium->get_path()}}" target="_blank">Listen to Audio</a>
                                                    </div>
                                                @else
                                                    <div class="alert alert-secondary" id="currentPreview">
                                                        <i class="mdi mdi-file font-size-24"></i> File
                                                        <br><a href="{{$medium->get_path()}}" target="_blank">View File</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="file" class="form-label">Replace File (Optional)</label>
                                                <input type="file" name="file" accept="image/*,video/*,audio/*"
                                                       class="form-control" id="file">
                                                <small class="form-text text-muted">Upload a new file to replace the current one. Leave empty to keep current file. Max 10MB</small>
                                                <div id="filePreview" class="mt-3"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="bucket_name" class="form-label">Folder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="bucket_name" value="{{old('bucket_name', $medium->bucket_name)}}"
                                                       class="form-control" id="bucket_name"
                                                       placeholder="e.g., media, uploads, images" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="file_key" class="form-label">File Key</label>
                                                <select id="file_key" class="form-select" name="file_key">
                                                    <option value="2" {{old('file_key', $medium->file_key) == 2 ? 'selected' : ''}}>Not Main</option>
                                                    <option value="1" {{old('file_key', $medium->file_key) == 1 ? 'selected' : ''}}>Main</option>
                                                    <option value="3" {{old('file_key', $medium->file_key) == 3 ? 'selected' : ''}}>Receipt</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="file_name" class="form-label">File Name</label>
                                                <input type="text" name="file_name" value="{{old('file_name', $medium->original_name)}}"
                                                       class="form-control" id="file_name"
                                                       placeholder="File name">
                                                <small class="form-text text-muted">Custom name for the file.</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="original_name" class="form-label">Original Name (Legacy)</label>
                                                <input type="text" name="original_name" value="{{old('original_name', $medium->original_name)}}"
                                                       class="form-control" id="original_name"
                                                       placeholder="Original file name (for backward compatibility)">
                                                <small class="form-text text-muted">This field is kept for backward compatibility. Use "File Name" above instead.</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label class="form-label">File Information</label>
                                                <div class="alert alert-light">
                                                    <strong>Type:</strong> 
                                                    @if($medium->file_type == 1) Image
                                                    @elseif($medium->file_type == 2) Video
                                                    @elseif($medium->file_type == 3) Audio
                                                    @elseif($medium->file_type == 4) GIF
                                                    @else Unknown
                                                    @endif
                                                    <br>
                                                    <strong>Extension:</strong> {{$medium->extension ?? 'N/A'}}
                                                    <br>
                                                    <strong>Size:</strong> {{$medium->size ? number_format($medium->size / 1024, 2) . ' KB' : 'N/A'}}
                                                    <br>
                                                    <strong>MIME Type:</strong> {{$medium->getMimeType ?? 'N/A'}}
                                                    <br>
                                                    <strong>Path:</strong> <code>{{$medium->path}}</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                           <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                        class="btn btn-primary waves-effect waves-light"> Update Media</button>
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
            const currentPreview = document.getElementById('currentPreview');
            preview.innerHTML = '';
            
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<div class="alert alert-info"><strong>New Image Preview:</strong><br><img src="' + e.target.result + '" class="img-thumbnail mt-2" style="max-width: 300px; max-height: 300px;"></div>';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    preview.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-video"></i> <strong>New Video File Selected:</strong> ' + file.name + '<br><small>Video preview will be available after upload.</small></div>';
                } else if (file.type.startsWith('audio/')) {
                    preview.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-music"></i> <strong>New Audio File Selected:</strong> ' + file.name + '</div>';
                } else {
                    preview.innerHTML = '<div class="alert alert-warning">File selected: ' + file.name + '</div>';
                }
            }
        });
    </script>
@endsection

