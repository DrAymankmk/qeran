<div class="vertical-menu">

	<div data-simplebar class="h-100">

		<!--- Sidemenu -->
		<div id="sidebar-menu">
			<!-- Left Menu Start -->
			<ul class="metismenu list-unstyled" id="side-menu">
				<li class="mm-active">
					<a href="{{route('admin.dashboard')}}" class="waves-effect">
						<i class="bx bx-home-circle"></i>
						<span key="t-chat">{{__('admin.Dashboard')}}</span>
					</a>
				</li>
				<li @if(Route::is('category.index')) class="mm-active" @endif>
					<a href="{{route('category.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.categories')}}</span>
					</a>
				</li>
				<li @if(Route::is('invitation-request.index') &&
					request('invitation_type')==\App\Helpers\Constant::INVITATION_TYPE['Contact Design']) class="mm-active" @endif>
					<a href="{{route('invitation-request.index',['invitation_type'=>\App\Helpers\Constant::INVITATION_TYPE['Contact Design']])}}"
						class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span
							key="t-chat">{{__('admin.invitation-requests')}}</span>
					</a>
				</li>
				<li @if(Route::is('invitation.index')) class="mm-active" @endif>
					<a href="{{route('invitation.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.invitations')}}</span>
					</a>
				</li>
				<li @if(Route::is('users.index')) class="mm-active" @endif>
					<a href="{{route('users.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.users')}}</span>
					</a>
				</li>
				<li @if(Route::is('package.index')) class="mm-active" @endif>
					<a href="{{route('package.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.packages')}}</span>
					</a>
				</li>
				<li @if(Route::is('contact.index')) class="mm-active" @endif>
					<a href="{{route('contact.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.contact-us')}}</span>
					</a>
				</li>
				<li @if(Route::is('notifications.index')) class="mm-active" @endif>
					<a href="{{route('notifications.index')}}" class="waves-effect">
						<i class="bx bx-list-ul"></i>
						<span key="t-chat">{{__('admin.notifications')}}</span>
					</a>
				</li>
				<li>
					<a href="javascript: void(0);" class="has-arrow waves-effect">
						<i class="bx bx-brightness"></i>
						<span key="t-dashboards">{{__('admin.settings')}}</span>
					</a>
					<ul class="sub-menu" aria-expanded="false">
						<li><a href="{{route('app-settings.edit',['key'=>'extra_guard_fees'])}}"
								key="t-full-calendar">
								{{__('admin.extra_guard_fees')}}</a>
						</li>
						<li><a href="{{route('app-settings.edit',['key'=>'extra_invitation_fees'])}}"
								key="t-full-calendar">
								{{__('admin.extra_invitation_fees')}}</a>
						</li>
						<li><a href="{{route('app-settings.edit',['key'=>'facebook'])}}"
								key="t-full-calendar">
								{{__('admin.facebook')}}</a></li>
						<li><a href="{{route('app-settings.edit',['key'=>'twitter'])}}"
								key="t-full-calendar">
								{{__('admin.twitter')}}</a></li>
						<li><a href="{{route('app-settings.edit',['key'=>'instagram'])}}"
								key="t-full-calendar">
								{{__('admin.instagram')}}</a></li>
						<li><a href="{{route('app-settings.edit',['key'=>'snapchat'])}}"
								key="t-full-calendar">
								{{__('admin.snapchat')}}</a></li>
						<li><a href="{{route('app-settings.edit',['key'=>'youtube'])}}"
								key="t-full-calendar">
								{{__('admin.youtube')}}</a></li>
						<li><a href="{{route('app-settings.edit',['key'=>'tiktok'])}}"
								key="t-full-calendar">
								{{__('admin.tiktok')}}</a></li>
					</ul>
				</li>


			</ul>
		</div>
		<!-- Sidebar -->
	</div>
</div>