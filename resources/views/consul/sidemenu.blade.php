<!-- Profile Sidebar -->
<div class="profile-sidebar">
	<div class="widget-profile pro-widget-content">
		<div class="profile-info-widget">
			<a href="#" class="booking-doc-img">
				<img src="{{asset(json_decode($user->userinfo->photo)->l)}}" alt="User Image">
			</a>
			<div class="profile-det-info">
				<h3>{{$user->userinfo->fname. ' ' .$user->userinfo->lname}}</h3>
				
				<div class="patient-details">
					<h5 class="mb-0">Consultant</h5>
					<h5 class="mb-0">@ {{$user->name}}</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="dashboard-widget">
		<nav class="dashboard-menu">
			<ul>
				<li class="{{ Request::is('consul/dashboard')?'active':''}}" >
					<a href="{{url('consul/dashboard')}}">
						<i class="fas fa-columns"></i>
						<span>{{ __('Dashboard') }}</span>
					</a>
				</li>
				<li class="{{Request::is('calendars') || Request::is('calendars/*')?'active':''}}">
					<a href="{{url('calendars')}}">
						<i class="fas fa-calendar"></i>
						<span>{{ __('My Calendar')}}</span>
					</a>
				</li>  
				<li class="{{Request::is('timingslots') || Request::is('timingslots/*')?'active':''}}" >
					<a href="{{url('timingslots')}}">
						<i class="fa fa-clock"></i>
						<span>{{ __('Schedule Timings')}}</span>
					</a>
				</li>   
				<li class="{{Request::is('holidays') || Request::is('holidays/*')?'active':''}}" >
					<a href="{{url('holidays')}}">
						<i class="fa fa-gifts"></i>
						<span>{{ __('Holidays')}}</span>
					</a>
				</li>   				
				<li class="{{Request::is('schedules') || Request::is('schedules/*')?'active':''}}" >
					<a href="{{url('schedules')}}">
						<i class="fa fa-calendar-check"></i>
						<span>{{ __('Schedules')}}</span>
					</a>
				</li>                                                                                        				  
				<li class="{{Request::is('consul/sections') || Request::is('consul/sections/*')?'active':''}}" >
					<a href="{{url('consul/sections')}}">
						<i class="fa fa-envelope"></i>
						<span>{{ __('Sections')}}</span>
					</a>
				</li>                                                                                        
				<li  class="{{Request::is('appointments') || Request::is('appointments/*')?'active':''}}" >
					<a href="{{url('/appointments')}}">
						<i class="fas fa-calendar-check"></i>
						<span>{{ __('Appointments')}}</span>
					</a>
				</li>
				<li class="{{Request::is('clients') || Request::is('clients/*')?'active':''}}">
					<a href="{{url('/clients')}}">
						<i class="fas fa-user-injured"></i>
						<span>{{ __('My Clients')}}</span>
					</a>
				</li>
				@if ($user->consultant->type != 'sub')
				<li class="{{Request::is('consul/payments') || Request::is('consul/payments/*')?'active':''}}">
					<a href="{{url('/consul/payments')}}">
						<i class="fas fa-file-invoice"></i>
						<span>{{ __('Payments')}}</span>
					</a>
				</li>							
				@endif			
				<li class="{{Request::is('meetings') || Request::is('meetings/*')?'active':''}}">
					<a href="{{url('/meetings')}}">
						<i class="fas fa-comments"></i>
						<span>{{ __('Meeting')}}</span>
					</a>
				</li>
				<li class="{{Request::is('internal-chat') || Request::is('internal-chat/*')?'active':''}}">
					<a href="{{url('/internal-chat')}}">
						<i class="fas fa-comments"></i>
						<span>{{ __('Internal Chat')}}</span>
					</a>
				</li>				
				<li class="{{Request::is('reports') || Request::is('reports/*')?'active':''}}">
					<a href="{{url('/reports')}}">
						<i class="fas fa-check"></i>
						<span>{{ __('Report')}}</span>
					</a>
				</li>											
				<li class="{{Request::is('consul/profile') || Request::is('consul/profile/*')?'active':''}}">
					<a href="{{url('consul/profile')}}">
						<i class="fas fa-user-cog"></i>
						<span>{{ __('Profile Settings')}}</span>
					</a>
				</li>	
				<li class="{{Request::is('consul/news') || Request::is('consul/news/*')?'active':''}}">
					<a href="{{url('consul/news')}}">
						<i class="fas fa-newspaper"></i>
						<span>{{ __('News')}}</span>
					</a>
				</li>								
				@if ($user->consultant->type != 'sub')
				<li class="{{Request::is('consul/msetting') || Request::is('consul/msetting/*')?'active':''}}">
					<a href="{{url('consul/msetting')}}">
						<i class="fas fa-cog"></i>
						<span>{{ __('Mission Settings')}}</span>
					</a>
				</li>															
				@endif
				<li>
					<a href="{{url('/logout')}}">
						<i class="fas fa-sign-out-alt"></i>
						<span>{{ __('Logout')}}</span>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</div>
<!-- /Profile Sidebar -->