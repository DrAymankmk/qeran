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
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Verify Pusher loads
        if (typeof Pusher === 'undefined') {
            console.error('❌ Pusher library failed to load from CDN');
        }
    </script>

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

@can('view-notifications')
<script>
    // Debug: Check if Pusher is loaded
    console.log('Checking Pusher setup...');
    console.log('Pusher library loaded:', typeof Pusher !== 'undefined');
    console.log('Broadcast driver:', '{{ config('broadcasting.default') }}');
    console.log('Pusher key configured:', {{ config('broadcasting.connections.pusher.key') ? 'true' : 'false' }});
    
    // Initialize Pusher for real-time notifications
    @if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key'))
    @php
        $pusherKey = config('broadcasting.connections.pusher.key');
        $pusherCluster = env('PUSHER_APP_CLUSTER', 'mt1');
        $options = config('broadcasting.connections.pusher.options', []);
        $useTLS = isset($options['useTLS']) && $options['useTLS'];
    @endphp
    @if($pusherKey)
    console.log('Initializing Pusher with key:', '{{ substr($pusherKey, 0, 10) }}...');
    console.log('Cluster:', '{{ $pusherCluster }}');
    
    try {
        const pusher = new Pusher('{{ $pusherKey }}', {
            cluster: '{{ $pusherCluster }}',
            encrypted: true,
            forceTLS: {{ $useTLS ? 'true' : 'false' }},
            enabledTransports: ['ws', 'wss']
        });

        // Connection state logging
        pusher.connection.bind('state_change', function(states) {
            console.log('Pusher connection state:', states.previous, '->', states.current);
        });

        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected successfully!');
        });

        pusher.connection.bind('disconnected', function() {
            console.log('❌ Pusher disconnected');
        });

        pusher.connection.bind('error', function(err) {
            console.error('❌ Pusher connection error:', err);
        });

        // Subscribe to admin notifications channel
        console.log('Subscribing to admin-notifications channel...');
        const adminChannel = pusher.subscribe('admin-notifications');
        
        adminChannel.bind('pusher:subscription_succeeded', function() {
            console.log('✅ Successfully subscribed to admin-notifications channel');
        });

        adminChannel.bind('pusher:subscription_error', function(err) {
            console.error('❌ Subscription error:', err);
        });
        
        // Listen for new notifications
        adminChannel.bind('new-notification', function(data) {
            console.log('🔔 New notification received:', data);
            
            // Reload notification count and list
            loadNotificationCount();
            loadNotifications();
            
            // Show browser notification if permission granted
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(data.title || 'New Notification', {
                    body: data.body || '',
                    icon: '{{ asset('admin_assets/images/logo.png') }}',
                    badge: '{{ asset('admin_assets/images/logo.png') }}'
                });
            }
            
            // Show toast notification
            if (typeof toastr !== 'undefined') {
                toastr.info(data.body || '', data.title || 'New Notification', {
                    timeOut: 5000,
                    positionClass: 'toast-top-right'
                });
            }
        });
        
        // Test event listener
        adminChannel.bind('pusher:error', function(err) {
            console.error('❌ Pusher channel error:', err);
        });

        // Make pusher available globally for testing
        window.pusher = pusher;
        window.adminChannel = adminChannel;
        console.log('Pusher initialized. Test with: window.pusher.trigger("admin-notifications", "new-notification", {title: "Test", body: "Test message"})');
        
        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                console.log('Notification permission:', permission);
            });
        }
    } catch (error) {
        console.error('❌ Error initializing Pusher:', error);
    }
    @else
    console.error('❌ Pusher key is not configured. Check your .env file.');
    @endif
    @else
    console.warn('⚠️ Pusher is not configured. Set BROADCAST_DRIVER=pusher in .env');
    @endif
</script>
<style>
    .unread-notification {
        background-color: rgba(85, 110, 230, 0.05) !important;
    }
    .unread-notification:hover {
        background-color: rgba(85, 110, 230, 0.1) !important;
    }
    
    /* Notification dropdown scrollable styles */
    #notifications-dropdown-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    #notifications-dropdown-list .simplebar-scrollbar::before {
        background-color: rgba(0, 0, 0, 0.3);
    }
    
    #notifications-dropdown-list .simplebar-track.simplebar-vertical {
        width: 8px;
    }
    
    #notifications-dropdown-list .simplebar-content-wrapper {
        padding-right: 0;
    }
    
    /* Smooth scrolling */
    #notifications-dropdown-list {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
    }
    
    #notifications-dropdown-list::-webkit-scrollbar {
        width: 6px;
    }
    
    #notifications-dropdown-list::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #notifications-dropdown-list::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 3px;
    }
    
    #notifications-dropdown-list::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
<script>
    // Load notifications on page load
    $(document).ready(function() {
        loadNotificationCount();
        // Refresh count every 30 seconds
        setInterval(loadNotificationCount, 30000);
    });

    function loadNotificationCount() {
        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge-count');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error loading notification count:', error));
    }

    function loadNotifications() {
        const listContainer = document.getElementById('notifications-dropdown-list');
        if (!listContainer) return;

        listContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                if (data.notifications.length === 0) {
                    listContainer.innerHTML = '<div class="text-center p-3 text-muted">{{__("admin.no-notifications")}}</div>';
                    // Reinitialize simplebar after content change
                    if (typeof SimpleBar !== 'undefined') {
                        new SimpleBar(listContainer);
                    }
                    return;
                }

                let html = '';
                data.notifications.forEach(notification => {
                    const iconClass = notification.category === '{{__("admin.order_notifications")}}' ? 'bx-cart' :
                                     notification.category === '{{__("admin.payment_notifications")}}' ? 'bx-credit-card' :
                                     notification.category === '{{__("admin.user_notifications")}}' ? 'bx-user' :
                                     notification.category === '{{__("admin.contact_us_notification")}}' ? 'bx-envelope' : 'bx-bell';
                    
                    const bgClass = notification.is_read ? 'bg-light' : 'bg-primary';
                    const fontWeight = notification.is_read ? '' : 'font-weight-bold';

                    html += `
                        <a href="${notification.url || 'javascript:void(0);'}" 
                           class="text-reset notification-item ${notification.is_read ? '' : 'unread-notification'}"
                           ${notification.url ? 'onclick="markNotificationAsRead(' + notification.id + ')"' : ''}>
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title ${bgClass} rounded-circle font-size-16">
                                        <i class="bx ${iconClass}"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 ${fontWeight}">${notification.title || '{{__("admin.no-title")}}'}</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1">${notification.description || ''}</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>${notification.created_at}</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                });

                listContainer.innerHTML = html;
                
                // Reinitialize simplebar after content change
                if (typeof SimpleBar !== 'undefined') {
                    new SimpleBar(listContainer);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                listContainer.innerHTML = '<div class="text-center p-3 text-danger">{{__("admin.error-loading-notifications")}}</div>';
                // Reinitialize simplebar after content change
                if (typeof SimpleBar !== 'undefined') {
                    new SimpleBar(listContainer);
                }
            });
    }

    function markNotificationAsRead(notificationId) {
        fetch('{{ route("notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotificationCount();
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
</script>
@endcan

</body>

@yield('modal')

</html>
