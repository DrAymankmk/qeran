<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{route('admin.dashboard')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('admin_assets/images/white-logo.png')}}" alt="" height="22">
                                </span>
                    <span class="logo-lg">
                                    <img src="{{asset('admin_assets/images/white-logo.png')}}" alt="" height="17">
                                </span>
                </a>

                <a href="{{route('admin.dashboard')}}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset('admin_assets/images/white-logo.png')}}" alt="" height="70">
                                </span>
                    <span class="logo-lg">
                                    <!-- mx width of logo 137px -->
                                    <img src="{{asset('admin_assets/images/white-logo.png')}}" alt="" height="50"
                                         class="mt-17">
                                </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <!-- App Search-->
            <!-- <form class="app-search d-none d-lg-block" method="get" >
                <div class="position-relative">
                    <input type="text" name="name" class="form-control" placeholder="بحث ٫٫٫">
                    <span class="bx bx-search-alt"></span>
                </div>
            </form> -->

        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                     aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="بحث ٫٫٫"
                                       aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

{{--            <div class="dropdown d-inline-block">--}}
{{--                <button type="button" class="btn header-item waves-effect"--}}
{{--                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <img id="header-lang-img" src="{{asset('admin_assets/images/flags/us.jpg')}}" alt="Header Language"--}}
{{--                         height="16">--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu dropdown-menu-end">--}}

{{--                    <!-- item-->--}}
{{--                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="en">--}}
{{--                        <img src="{{asset('admin_assets/images/flags/us.jpg')}}" alt="user-image" class="me-1"--}}
{{--                             height="12"> <span class="align-middle">الانجليزية</span>--}}
{{--                    </a>--}}
{{--                    <!-- item-->--}}


{{--                    <!-- item-->--}}
{{--                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="ru">--}}
{{--                        <img src="{{asset('admin_assets/images/flags/saudi.png')}}" alt="user-image" class="me-1"--}}
{{--                             height="12"> <span class="align-middle">العربية</span>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}


            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            @can('view-notifications')
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect position-relative"
                        id="page-header-notifications-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        onclick="loadNotifications()">
                    <i class="bx bx-bell"></i>
                    <span class="badge bg-danger rounded-pill" id="notification-badge-count">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                     aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> {{__('admin.notifications')}} </h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{route('notifications.index')}}" class="small text-muted"
                                   key="t-view-all"> {{__('admin.view-all')}} </a>
                            </div>
                        </div>
                    </div>

                    <div data-simplebar data-simplebar-auto-hide="false" style="max-height: 400px; overflow-y: auto;" id="notifications-dropdown-list">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{route('notifications.index')}}">
                            <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">{{__('admin.view-all')}} ...</span>
                        </a>
                    </div>
                </div>
            </div>
            @endcan

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                         src="{{Auth::guard('admin')->user()->image() }}"
                         alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{__('admin.project-name')}}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{route('admin.profile')}}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i> <span
                            key="t-profile">{{__('admin.my-profile')}}</span></a>
                    {{--                    <a class="dropdown-item" href="index.html#"><i class="bx bx-wallet font-size-16 align-middle me-1"></i> <span key="t-my-wallet">المحفظة</span></a>--}}
                    {{--                    <a class="dropdown-item d-block" href="index.html#"><span class="badge bg-success float-end">11</span><i class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">الإعدادات</span></a>--}}
                    {{--                    <a class="dropdown-item" href="index.html#"><i class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span key="t-lock-screen">قفل الشاشة</span></a>--}}
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{route('admin.logout')}}"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">{{__('admin.logout')}}</span></a>
                </div>
            </div>

{{--            <div class="dropdown d-inline-block">--}}
{{--                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">--}}
{{--                    <i class="bx bx-cog bx-spin"></i>--}}
{{--                </button>--}}
{{--            </div>--}}

        </div>
    </div>
</header>
