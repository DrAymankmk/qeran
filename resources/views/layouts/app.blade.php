<!doctype html>
<html lang="en" dir="rtl">

<head>

    <meta charset="utf-8"/>
    <title>{{__('admin.project-name')}} - {{__('admin.Dashboard')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"
          name="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"/>
    <meta content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}" name="{{__('admin.project-name')}}"/>
    <link rel="shortcut icon" href="{{asset('admin_assets/images/logo.png')}}">
    <link rel="icon" href="{{asset('admin_assets/images/logo.png')}}">

@yield('extra-css')

<!-- Bootstrap Css -->
    <link href="{{asset('admin_assets/libs/toastr/build/toastr.min.css')}}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('admin_assets/css/bootstrap-rtl.min.css')}}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <!-- Icons Css -->
    <link href="{{asset('admin_assets/css/icons.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- App Css-->
    <link href="{{asset('admin_assets/css/app-rtl.min.css')}}" id="app-style" rel="stylesheet" type="text/css"/>
    @yield('extra-last-css')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

</head>

<body data-sidebar="dark" data-layout-mode="light">
<div id="layout-wrapper">


@include('layouts.includes.top-bar')
<!-- ========== Left Sidebar Start ========== -->
    @include('layouts.includes.menu')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @yield('content')
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> {{__('admin.main-copy-rights')}} <a
                           >{{__('admin.project-name')}}</a>.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            {{__('admin.copy-rights')}}
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Left Sidebar End -->

    </div>
    <!-- END layout-wrapper -->
</div>

<!-- Right Sidebar -->
@include('layouts.includes.right-bar')
<!-- /Right-bar -->


<!-- JAVASCRIPT -->
<script src="{{asset('admin_assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/metismenu/metisMenu.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/node-waves/waves.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/toastr/build/toastr.min.js')}}"></script>

@yield('extra-js')
<!-- apexcharts -->
<!-- App js -->
<script src="{{asset('admin_assets/js/app.js')}}"></script>
@yield('add-product-js')
{{--    @if(app()->getLocale()=='ar')--}}
<script>
    $('.datatable.dt-responsive').DataTable({
        language: {
            url: '{{asset('admin_assets/ar.json')}}'
        },
        "bPaginate": false,
    });

</script>
@if(Session::has('success'))

    <script>
        toastr["success"]("{{__('admin.stored-successfully')}}")

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-left",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": 100,
            "hideDuration": 1000,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

    </script>

@endif
@if ($errors->any())

    <script>
        toastr["error"]("{{__('admin.please-check-all-entered-data')}}")

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": 100,
            "hideDuration": 1000,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

    </script>

@endif


</body>

@yield('modal')

</html>
