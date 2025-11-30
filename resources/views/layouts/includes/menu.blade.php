<div class="vertical-menu">

	<div data-simplebar class="h-100">

		<!--- Sidemenu -->
		<div id="sidebar-menu">
			<!-- Logo Section -->
			<!-- <div class="logo-box text-center py-3 mb-3">
				<a href="{{route('admin.dashboard')}}" class="logo logo-dark">
					<span class="logo-lg">
						<img src="{{asset('admin_assets/images/logo.png')}}" alt="{{__('admin.project-name')}}" height="50" class="sidebar-logo">
					</span>
					<span class="logo-sm">
						<img src="{{asset('admin_assets/images/logo.png')}}" alt="{{__('admin.project-name')}}" height="35" class="sidebar-logo-sm">
					</span>
				</a>
				<a href="{{route('admin.dashboard')}}" class="logo logo-light">
					<span class="logo-lg">
						<img src="{{asset('admin_assets/images/white-logo.png')}}" alt="{{__('admin.project-name')}}" height="50" class="sidebar-logo">
					</span>
					<span class="logo-sm">
						<img src="{{asset('admin_assets/images/white-logo.png')}}" alt="{{__('admin.project-name')}}" height="35" class="sidebar-logo-sm">
					</span>
				</a>
			</div> -->

			<!-- Left Menu Start -->
			<ul class="metismenu list-unstyled" id="side-menu">
				@can('view-dashboard')
				<li class="{{Route::is('admin.dashboard') ? 'mm-active' : ''}}">
					<a href="{{route('admin.dashboard')}}" class="waves-effect">
						<i class="bx bx-home-circle"></i>
						<span key="t-chat">{{__('admin.Dashboard')}}</span>
					</a>
				</li>
				@endcan
				<li class="menu-title">{{__('admin.content-management')}}</li>
				@can('view-categories')
				<li @if(Route::is('category.index') || Route::is('category.create') ||
					Route::is('category.edit')) class="mm-active" @endif>
					<a href="{{route('category.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.categories')}}</span>
					</a>
				</li>
				@endcan
				@can('view-invitation-requests')
				<li @if(Route::is('invitation-request.index') &&
					request('invitation_type')==\App\Helpers\Constant::INVITATION_TYPE['Contact Design']) class="mm-active" @endif>
					<a href="{{route('invitation-request.index',['invitation_type'=>\App\Helpers\Constant::INVITATION_TYPE['Contact Design']])}}"
						class="waves-effect">
						<i class="bx bx-file-find"></i>
						<span
							key="t-chat">{{__('admin.invitation-requests')}}</span>
					</a>
				</li>
				@endcan
				@can('view-invitations')
				<li @if(Route::is('invitation.index') || Route::is('invitation.create') ||
					Route::is('invitation.edit') || Route::is('invitations.details'))
					class="mm-active" @endif>
					<a href="{{route('invitation.index')}}" class="waves-effect">
						<i class="bx bx-file"></i>
						<span key="t-chat">{{__('admin.invitations')}}</span>
					</a>
				</li>
				@endcan
				@can('view-users')
				<li class="menu-title">{{__('admin.user-management')}}</li>

				<li @if(Route::is('users.index') || Route::is('users.show') ||
					Route::is('users.edit')) class="mm-active" @endif>
					<a href="{{route('users.index')}}" class="waves-effect">
						<i class="bx bx-user"></i>
						<span key="t-chat">{{__('admin.users')}}</span>
					</a>
				</li>
				@endcan
				@can('view-admins')
				<li @if(Route::is('admins.index') || Route::is('admins.create') ||
					Route::is('admins.edit') || Route::is('admins.show'))
					class="mm-active" @endif>
					<a href="{{route('admins.index')}}" class="waves-effect">
						<i class="bx bx-user-circle"></i>
						<span key="t-chat">{{__('admin.admins')}}</span>
					</a>
				</li>
				@endcan
				@can('view-roles')
				<li @if(Route::is('roles.index') || Route::is('roles.create') ||
					Route::is('roles.edit') || Route::is('roles.show')) class="mm-active"
					@endif>
					<a href="{{route('roles.index')}}" class="waves-effect">
						<i class="bx bx-shield-quarter"></i>
						<span key="t-chat">{{__('admin.roles')}}</span>
					</a>
				</li>
				@endcan
				@can('view-permissions')
				<!-- <li @if(Route::is('permissions.index') || Route::is('permissions.create') ||
					Route::is('permissions.edit') || Route::is('permissions.show')) class="mm-active" @endif>
					<a href="{{route('permissions.index')}}" class="waves-effect">
						<i class="bx bx-lock"></i>
						<span key="t-chat">{{__('admin.permissions')}}</span>
					</a>
				</li> -->
				@endcan
				<li class="menu-title">{{__('admin.business-management')}}</li>
				@can('view-packages')
				<li @if(Route::is('package.index') || Route::is('package.create') ||
					Route::is('package.edit')) class="mm-active" @endif>
					<a href="{{route('package.index')}}" class="waves-effect">
						<i class="bx bx-package"></i>
						<span key="t-chat">{{__('admin.packages')}}</span>
					</a>
				</li>
				@endcan
				@can('view-promo-codes')
				<li @if(Route::is('promo-code.index') || Route::is('promo-code.create') ||
					Route::is('promo-code.edit') || Route::is('promo-code.details'))
					class="mm-active" @endif>
					<a href="{{route('promo-code.index')}}" class="waves-effect">
						<i class="bx bx-purchase-tag"></i>
						<span key="t-chat">{{__('admin.promo-codes')}}</span>
					</a>
				</li>
				@endcan
				@can('view-financial')
				<li @if(Route::is('financial.index') || Route::is('financial.monthly-report') ||
					Route::is('financial.annual-report')) class="mm-active" @endif>
					<a href="{{route('financial.index')}}" class="waves-effect">
						<i class="bx bx-money"></i>
						<span
							key="t-chat">{{__('admin.financial-transactions')}}</span>
					</a>
				</li>
				@endcan
				<li class="menu-title">{{__('admin.communication')}}</li>
				@can('view-contact-us')
				<li @if(Route::is('contact.index') || Route::is('contact.show') ||
					Route::is('contact.reply')) class="mm-active" @endif>
					<a href="{{route('contact.index')}}" class="waves-effect">
						<i class="bx bx-message-square-dots"></i>
						<span key="t-chat">{{__('admin.contact-us')}}</span>
					</a>
				</li>
				@endcan
				@can('view-notifications')
				<li @if(Route::is('notifications.index') || Route::is('notifications.create') ||
					Route::is('notifications.edit')) class="mm-active" @endif>
					<a href="{{route('notifications.index')}}" class="waves-effect">
						<i class="bx bx-bell"></i>
						<span key="t-chat">{{__('admin.notifications')}}</span>
					</a>
				</li>
				@endcan
				<li class="menu-title">{{__('admin.system')}}</li>

				@can('view-app-settings')

				<li>
					<a href="javascript: void(0);" class="has-arrow waves-effect">
						<i class="bx bx-cog"></i>
						<span key="t-dashboards">{{__('admin.settings')}}</span>
					</a>
					<ul class="sub-menu" aria-expanded="false">
						<li @if(Route::is('app-settings.index') ||
							Route::is('app-settings.edit')) class="mm-active"
							@endif>
							<a href="{{route('app-settings.index')}}"
								key="t-full-calendar">
								<i class="bx bx-right-arrow-alt"></i>
								{{__('admin.settings')}}
							</a>
						</li>
					</ul>
				</li>
				@endcan
			</ul>
		</div>
		<!-- Sidebar -->
	</div>
</div>

<style>
/* Sidebar Logo Styling */
.logo-box {
	border-bottom: 1px solid rgba(255, 255, 255, 0.1);
	margin-bottom: 1rem;
	padding-bottom: 1rem;
}

.sidebar-logo,
.sidebar-logo-sm {
	transition: transform 0.3s ease;
}

.sidebar-logo:hover,
.sidebar-logo-sm:hover {
	transform: scale(1.05);
}

/* Menu Title Styling */
.menu-title {
	font-size: 11px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: rgba(255, 255, 255, 0.5);
	padding: 12px 20px 8px;
	margin-top: 15px;
	margin-bottom: 5px;
}

.menu-title:first-of-type {
	margin-top: 0;
}

/* Enhanced Menu Item Styling */
#side-menu>li>a {
	padding: 12px 20px;
	display: flex;
	align-items: center;
	transition: all 0.3s ease;
	border-radius: 8px;
	margin: 2px 10px;
}

#side-menu>li>a:hover {
	background-color: rgba(255, 255, 255, 0.1);
	transform: translateX(5px);
}

#side-menu>li.mm-active>a {
	background-color: rgba(85, 110, 230, 0.15);
	border-left: 3px solid #556ee6;
	color: #556ee6;
	font-weight: 600;
}

/* Icon Styling */
#side-menu>li>a i {
	font-size: 20px;
	width: 24px;
	text-align: center;
	margin-left: 10px;
	transition: transform 0.3s ease;
}

#side-menu>li>a:hover i {
	transform: scale(1.1);
}

/* Sub-menu Styling */
.sub-menu li a {
	padding: 10px 20px 10px 50px;
	display: flex;
	align-items: center;
	transition: all 0.3s ease;
}

.sub-menu li a i {
	font-size: 14px;
	margin-left: 8px;
}

.sub-menu li.mm-active a {
	background-color: rgba(85, 110, 230, 0.1);
	color: #556ee6;
	font-weight: 500;
}

/* Smooth Transitions */
.metismenu .mm-collapse {
	transition: height 0.35s ease;
}

/* Badge/Notification Count Styling (if needed in future) */
.menu-badge {
	margin-right: auto;
	margin-left: 10px;
	padding: 2px 8px;
	border-radius: 12px;
	font-size: 11px;
	font-weight: 600;
	background-color: #f46a6a;
	color: white;
}
</style>
