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
                <h4 class="mb-sm-0 font-size-18">Edit Design</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{route('category.index')}}">Categories</a></li>
                        <li class="breadcrumb-item"><a href="{{route('designs.index', ['category_id' => $design->category_id])}}">Designs</a></li>
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
                    <form action="{{route('designs.update',$design->id)}}" method="post" enctype="multipart/form-data">
                     @method('PATCH')
                        <div class="tab-content crypto-buy-sell-nav-content p-4">
                            @csrf
                            <div class="tab-pane active" id="buy" role="tabpanel">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select id="category_id" class="form-select" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{$cat->id}}" {{old('category_id', $design->category_id) == $cat->id ? 'selected' : ''}}>{{$cat->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Code</label>
                                            <div class="input-group">
                                                <input type="text" name="code" value="{{old('code', $design->code)}}"
                                                       class="form-control" id="code"
                                                       placeholder="Design Code">
                                                <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn">
                                                    <i class="mdi mdi-refresh"></i> Generate
                                                </button>
                                            </div>
                                            <div class="mt-2">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="codeLength" class="form-label small">Length</label>
                                                        <input type="number" id="codeLength" class="form-control form-control-sm" 
                                                               value="8" min="1" max="50">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="codeType" class="form-label small">Type</label>
                                                        <select id="codeType" class="form-select form-select-sm">
                                                            <option value="numbers">Numbers</option>
                                                            <option value="characters">Characters</option>
                                                            <option value="mixed" selected>Numbers & Characters</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            @if($design->image())
                                            <div class="mb-2">
                                                <img src="{{$design->image()}}" alt="Current Image" style="max-width: 200px; max-height: 200px; object-fit: cover;" class="img-thumbnail">
                                                <p class="text-muted small mt-1">Current Image</p>
                                            </div>
                                            @endif
                                            <input type="file" name="image" accept="image/*"
                                                   class="form-control" id="image">
                                            <small class="form-text text-muted">Leave empty to keep current image. JPEG, PNG, GIF, WebP, max 5MB</small>
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
                                                    <label for="en-name" class="form-label">Name (EN)</label>
                                                    <input type="text" name="en[name]" 
                                                           value="{{old('en.name', $design->translate('en')->name ?? '')}}"
                                                           class="form-control" id="en-name"
                                                           placeholder="Design Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Arabic Tab -->
                                    <div class="tab-pane fade" id="ar" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="ar-name" class="form-label">الاسم (AR)</label>
                                                    <input type="text" name="ar[name]" 
                                                           value="{{old('ar.name', $design->translate('ar')->name ?? '')}}"
                                                           class="form-control" id="ar-name"
                                                           placeholder="اسم التصميم">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                                                    <div class="d-flex flex-wrap gap-2">
                            <button type="submit"
                                    class="btn btn-primary waves-effect waves-light"> {{__('admin.update')}}</button>
                            <a href="{{route('designs.index', ['category_id' => $design->category_id])}}"
                               class="btn btn-secondary waves-effect waves-light">Cancel</a>

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

    <script>
        // Code Generator Function
        function generateCode(length, type) {
            let characters = '';
            let result = '';
            
            if (type === 'numbers') {
                characters = '0123456789';
            } else if (type === 'characters') {
                characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            } else if (type === 'mixed') {
                characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            }
            
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            
            return result;
        }
        
        // Generate Code Button Click Handler
        $(document).ready(function() {
            $('#generateCodeBtn').on('click', function() {
                const length = parseInt($('#codeLength').val()) || 8;
                const type = $('#codeType').val() || 'mixed';
                
                if (length < 1 || length > 50) {
                    alert('Length must be between 1 and 50');
                    return;
                }
                
                const generatedCode = generateCode(length, type);
                $('#code').val(generatedCode);
            });
        });
    </script>

@endsection

