<!doctype html>
<html lang="en" dir="rtl">

<head>

    <meta charset="utf-8" />
    <title>{{__('admin.project-name')}} - {{__('admin.Dashboard')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"
          name="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"/>
    <meta content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}" name="{{__('admin.project-name')}}"/>
    <link rel="shortcut icon" href="{{asset('admin_assets/images/logo.png')}}">
    <!-- spider effect -->
    <link rel="stylesheet" href="{{asset('admin_assets/css/style-slider.css')}}">

    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{asset('admin_assets/libs/owl.carousel/assets/owl.carousel.min.css')}}">

    <link rel="stylesheet" href="{{asset('admin_assets/libs/owl.carousel/assets/owl.theme.default.min.css')}}">

    <!-- Bootstrap Css -->
    <link href="{{asset('admin_assets/css/bootstrap-rtl.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('admin_assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('admin_assets/css/app-rtl.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />



</head>
<body class="auth-body-bg">

@yield('content')


<script src="{{asset('admin_assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/metismenu/metisMenu.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('admin_assets/libs/node-waves/waves.min.js')}}"></script>

<!-- owl.carousel js -->
<script src="{{asset('admin_assets/libs/owl.carousel/owl.carousel.min.js')}}"></script>

<!-- auth-2-carousel init -->
<script src="{{asset('admin_assets/js/pages/auth-2-carousel.init.js')}}"></script>

<!-- App js -->
<script src="{{asset('admin_assets/js/app.js')}}"></script>
<!-- spider effect -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script> <!-- stats.js lib -->
<script src="{{asset('admin_assets/js/script-slider.js')}}"></script>
</body>
</html>
