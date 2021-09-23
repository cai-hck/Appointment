<!-- Header -->
<header class="header">
	<nav class="navbar navbar-expand-lg header-nav">
		<div class="navbar-header">					
			<a href="{{url('consul/dashboard')}}" class="navbar-brand logo">
				<img src="{{asset($config['logo'])}}" class="img-fluid" alt="Logo">
			</a>
		</div>
		<select class="form-control change-language mobile-change-lang">
			<option value="en" {{session()->get('locale')=='en'?'selected':''}}>English</option>
			<option value="ar" {{session()->get('locale')=='ar'?'selected':''}}>Arabic</option>
		</select>
		<div class="main-menu-wrapper">
			<div class="menu-header">
				<a href="{{url('consul/dashboard')}}" class="menu-logo">
					<img src="{{url('consul/dashboard')}}" class="img-fluid" alt="Logo">
				</a>
				<a id="menu_close" class="menu-close" href="javascript:void(0);">
					<i class="fas fa-times"></i>
				</a>
			</div>				
		</div>		 
		<ul class="nav header-navbar-rht">
			<li class="nav-item contact-item">
				<div class="header-contact-img">
					<i class="far fa-hospital"></i>							
				</div>
				<div class="header-contact-detail">
					<p class="contact-header">{{__('Contanct')}}</p>
					<p class="contact-info-header"> {{$config['contact_number']}}</p>
				</div>
			</li>
			<!-- User Menu -->
			<li class="nav-item dropdown has-arrow logged-item">
				<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
					<span class="user-img">
						<img class="rounded-circle" 
							src="{{ asset(json_decode($user->userinfo->photo)->s)}}" 
						width="31" alt="Darren Elder">
					</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<div class="user-header">
						<div class="avatar avatar-sm">
							<img src="{{ asset(json_decode($user->userinfo->photo)->s)}}" alt="User Image" class="avatar-img rounded-circle">
						</div>
						<div class="user-text">
							<h6>{{ $user->userinfo->fname . ' ' .$user->userinfo->lname}}</h6>
							<p class="text-muted mb-0">{{$user->consultant->type=='main'?__('Main Consultant'):__('Sub Consultant')}}</p>
							<p class="text-muted mb-0">@ {{$user->name}}</p>
						</div>
					</div>
					<a class="dropdown-item" href="{{url('consul/dashboard')}}">{{__('Dashboard')}}</a>
					<a class="dropdown-item" href="{{url('/consul/profile')}}">{{__('Profile Settings') }}</a>
					<a class="dropdown-item" href="{{url('/logout')}}">{{__('Logout')}}</a>
				</div>
			</li>

			<!-- Notify Menu -->
			<li class="nav-item dropdown logged-item dropdown-notifications">
				<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
					<span class="user-img">
						<i class="fa fa-bell"  data-count="0" ></i> <span class="text-white badge badge-pill bg-success notif-count">0</span>
					</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right notifications-container" style="min-width:300px">					
				</div>
			</li>

			
			<li class="nav-item">
                <select class="form-control change-language">
                    <option value="en" {{session()->get('locale')=='en'?'selected':''}}>English</option>
                    <option value="ar" {{session()->get('locale')=='ar'?'selected':''}}>Arabic</option>
                </select>
            </li>
			<!-- /User Menu -->
			
		</ul>
	</nav>
	<form action="{{url('/setlang')}}" method="post" id="lang_form">
        @csrf
        <input type="hidden" name="locale" id="locale"/>
    </form>
</header>
<!-- /Header -->