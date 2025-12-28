@extends('layouts.app')
@section('extra-css')
    <link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}"
          id="bootstrap-style" rel="stylesheet"
          type="text/css"/>

@endsection
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Testimonial</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{route('testimonials.index')}}">Testimonials</a></li>
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
                    <form action="{{route('testimonials.update',$testimonial->id)}}" method="post" enctype="multipart/form-data">
                     @method('PATCH')
                        <div class="tab-content crypto-buy-sell-nav-content p-4">
                            @csrf
                            <div class="tab-pane active" id="buy" role="tabpanel">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating (Stars)</label>
                                            <select id="rating" class="form-select" name="rating">
                                                <option value="">No Rating</option>
                                                <option value="1" {{old('rating', $testimonial->rating) == '1' ? 'selected' : ''}}>1 Star</option>
                                                <option value="2" {{old('rating', $testimonial->rating) == '2' ? 'selected' : ''}}>2 Stars</option>
                                                <option value="3" {{old('rating', $testimonial->rating) == '3' ? 'selected' : ''}}>3 Stars</option>
                                                <option value="4" {{old('rating', $testimonial->rating) == '4' ? 'selected' : ''}}>4 Stars</option>
                                                <option value="5" {{old('rating', $testimonial->rating) == '5' ? 'selected' : ''}}>5 Stars</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Order</label>
                                            <input type="number" name="order" value="{{old('order', $testimonial->order)}}"
                                                   class="form-control" id="order"
                                                   placeholder="Display Order">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Status</label>
                                            <select id="is_active" class="form-select" name="is_active">
                                                <option value="1" {{old('is_active', $testimonial->is_active) == '1' ? 'selected' : ''}}>Active</option>
                                                <option value="0" {{old('is_active', $testimonial->is_active) == '0' ? 'selected' : ''}}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image (Optional)</label>
                                            @if($testimonial->image())
                                                <div class="mb-2">
                                                    <img src="{{$testimonial->image()}}" alt="Current Image" 
                                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                                </div>
                                            @endif
                                            <input type="file" name="image" accept="image/*"
                                                   class="form-control" id="image">
                                            <small class="form-text text-muted">JPEG, PNG, GIF, WebP, max 5MB. Leave empty to keep current image.</small>
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
                                                    <label for="en-name" class="form-label">Name (EN) <span class="text-danger">*</span></label>
                                                    <input type="text" name="en[name]" value="{{old('en.name', $testimonial->translate('en')->name ?? '')}}"
                                                           class="form-control" id="en-name"
                                                           placeholder="Client Name" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="en-job" class="form-label">Job Title (EN)</label>
                                                    <input type="text" name="en[job]" value="{{old('en.job', $testimonial->translate('en')->job ?? '')}}"
                                                           class="form-control" id="en-job"
                                                           placeholder="Job Title">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="en-message" class="form-label">Testimonial Message (EN) <span class="text-danger">*</span></label>
                                                    <textarea name="en[message]" class="form-control" id="en-message" rows="5" required
                                                              placeholder="Testimonial message">{{old('en.message', $testimonial->translate('en')->message ?? '')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Arabic Tab -->
                                    <div class="tab-pane fade" id="ar" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="ar-name" class="form-label">الاسم (AR) <span class="text-danger">*</span></label>
                                                    <input type="text" name="ar[name]" value="{{old('ar.name', $testimonial->translate('ar')->name ?? '')}}"
                                                           class="form-control" id="ar-name"
                                                           placeholder="اسم العميل" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="ar-job" class="form-label">المسمى الوظيفي (AR)</label>
                                                    <input type="text" name="ar[job]" value="{{old('ar.job', $testimonial->translate('ar')->job ?? '')}}"
                                                           class="form-control" id="ar-job"
                                                           placeholder="المسمى الوظيفي">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="ar-message" class="form-label">رسالة الشهادة (AR) <span class="text-danger">*</span></label>
                                                    <textarea name="ar[message]" class="form-control" id="ar-message" rows="5" required
                                                              placeholder="رسالة الشهادة">{{old('ar.message', $testimonial->translate('ar')->message ?? '')}}</textarea>
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
                            <a href="{{route('testimonials.index')}}"
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
@endsection















