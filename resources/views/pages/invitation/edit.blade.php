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
                <h4 class="mb-sm-0 font-size-18">{{__('admin.invitations')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('admin.invitations')}}</li>
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

                    <form action="{{route('invitation.update',$invitation->id)}}" method="post"
                          enctype="multipart/form-data">
                        <div class="tab-content crypto-buy-sell-nav-content p-4">

                            @csrf
                            @method('patch')

                            {{-- List all invitation media (hub files) with update/delete --}}
                            @if($invitation->hubFiles->isNotEmpty())
                                <div class="mb-4">
                                    <h5 class="mb-3">{{ __('admin.invitation-media') }}</h5>
                                    <div class="row g-3">
                                        @foreach($invitation->hubFiles as $hubFile)
                                            @php
                                                $fileType = $hubFile->file_type ?? 1;
                                                $typeLabel = $fileType === \App\Helpers\Constant::FILE_TYPE['Video'] ? __('admin.media-type-2') : ($fileType === \App\Helpers\Constant::FILE_TYPE['Audio'] ? __('admin.media-type-3') : ($fileType === \App\Helpers\Constant::FILE_TYPE['Gif'] ? __('admin.media-type-4') : __('admin.media-type-1')));
                                                $url = $hubFile->get_path();
                                            @endphp
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="badge bg-primary">{{ $typeLabel }}</span>
                                                            <span class="text-muted small">{{ $hubFile->original_name ?? $hubFile->path }}</span>
                                                        </div>
                                                        <div class="mb-2 text-center" style="min-height: 120px;">
                                                            @if($fileType === \App\Helpers\Constant::FILE_TYPE['Image'] || $fileType === \App\Helpers\Constant::FILE_TYPE['Gif'])
                                                                <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;"></a>
                                                            @elseif($fileType === \App\Helpers\Constant::FILE_TYPE['Video'])
                                                                <video width="100%" controls style="max-height: 180px;" preload="metadata"><source src="{{ $url }}" type="video/mp4">Your browser does not support the video tag.</video>
                                                            @elseif($fileType === \App\Helpers\Constant::FILE_TYPE['Audio'])
                                                                <audio controls class="w-100"><source src="{{ $url }}" type="audio/mpeg"><source src="{{ $url }}" type="audio/ogg">Your browser does not support the audio element.</audio>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <form action="{{ route('invitation.hub-file.replace', [$invitation->id, $hubFile->id]) }}" method="post" enctype="multipart/form-data" class="d-inline replace-form">
                                                                @csrf
                                                                <input type="file" name="file" class="form-control form-control-sm d-inline-block w-auto" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,image/jpeg,image/png,image/gif,.mp4,.webm,.ogg,.mov,.avi,.jpg,.jpeg,.png,.gif" required style="max-width: 140px;">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('admin.replace') }}</button>
                                                            </form>
                                                            <form action="{{ route('invitation.hub-file.destroy', [$invitation->id, $hubFile->id]) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm-delete-hub-file') }}');">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr class="my-4">
                            @endif

                            <div class="mb-3">
                                <label for="formrow-firstname-input" class="form-label">     {{__('admin.desc')}}   </label>
                                <textarea class="form-control" id="productdesc" disabled rows="10" placeholder=" {{__('admin.desc')}}">{{$invitation->description}}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="formrow-firstname-input"
                                       class="form-label">    {{__('admin.upload-invitation-file')}}  </label>
                                <input class="form-control" type="file" name="file" id="formFile"
                                       accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,image/jpeg,image/png,image/gif,.mp4,.webm,.ogg,.mov,.avi,.jpg,.jpeg,.png,.gif">
                                <div class="form-text">
                                    {{ __('admin.invitation-file-hint') }}
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="start_at-input"
                                       class="form-label">{{__('admin.date')}} </label>
                                <input name="date" style="text-align: right;" class="form-control " type="date" value="{{\Carbon\Carbon::parse($invitation->date)->format('Y-m-d')}}"
                                       id="start_at-input">
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text"
                                               id="pac-input"
                                               class="form-control"
                                               placeholder="{{__('admin.location')}}"
                                               name="address"
                                               value="{{$invitation->address}}"
                                        >
                                        <br>
                                        <input type="hidden" name="latitude" id="latitude" value="{{$invitation->latitude}}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{$invitation->longitude}}">

                                        <div id="map" style="padding: 15%"></div>

                                    </div>
                                </div>

                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                        class="btn btn-primary waves-effect waves-light"> {{__('admin.add')}}</button>

                            </div>

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
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDhnmMC23noePz6DA8iEvO9_yNDGGlEaeM&callback=initAutocomplete&v=weekly&libraries=places"
        async
    ></script>
    <script>
        function initAutocomplete() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: {{$invitation->latitude}}, lng: {{$invitation->longitude}} },
                zoom: 20,
                mapTypeId: "roadmap",
            });
            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            marker = new google.maps.Marker({
                position: new google.maps.LatLng({{$invitation->latitude}}, {{$invitation->longitude}}),
                map: map,
            });


            // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            let markers = [];

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();

                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());

                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };

                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );
                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }
        window.initAutocomplete = initAutocomplete;
        //setup before functions

    </script>


@endsection
