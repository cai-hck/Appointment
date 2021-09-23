			<!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
					<div id="sidebar-menu" class="sidebar-menu">
						<ul>
							<li class="{{Request::is('admin/dashboard')?'active':''}}"> 
								<a href="{{url('admin/dashboard')}}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
							</li>
							<li class="{{Request::is('admin/appointments')||Request::is('admin/appointments/*')?'active':''}}"> 
								<a href="{{url('admin/appointments')}}"><i class="fe fe-layout"></i> <span>Appointments</span></a>
							</li>
							<li class="{{Request::is('admin/mission')||Request::is('admin/mission/*')?'active':''}}"> 
								<a href="{{url('admin/mission')}}"><i class="fe fe-users"></i> <span>Mission</span></a>
							</li>
							<li class="{{Request::is('admin/consultant')||Request::is('admin/consultant/*')?'active':''}}"> 
								<a href="{{url('admin/consultant')}}"><i class="fe fe-user-plus"></i> <span>Consultant</span></a>
                            </li>
                            <li class="{{Request::is('admin/secretary')||Request::is('admin/secretary/*')?'active':''}}"> 
								<a href="{{url('admin/secretary')}}"><i class="fe fe-user"></i> <span>Secretary</span></a>
							</li>
							<li class="{{Request::is('admin/clients')||Request::is('admin/clients/*')?'active':''}}"> 
								<a href="{{url('admin/clients')}}"><i class="fe fe-user"></i> <span>Client</span></a>
							</li>
							<li class="{{Request::is('admin/transactions')||Request::is('admin/transactions/*')?'active':''}}"> 
								<a href="{{url('admin/transactions')}}"><i class="fe fe-activity"></i> <span>Transactions</span></a>
							</li>
							<li class="{{Request::is('admin/setting')?'active':''}}"> 
								<a href="{{url('admin/setting') }}" ><i class="fe fe-vector"></i> <span>Settings</span></a>
							</li>
							<li>
								<a href="{{url('admin/terms') }}" ><i class="fe fe-document"></i> <span>Terms & Conditions</span></a>
							</li>
							<li>
								<a href="{{url('admin/policy') }}" ><i class="fe fe-document"></i> <span>Privacy Policy</span></a>
							</li>
							<li>
								<a href="{{url('admin/reports') }}" ><i class="fe fe-document"></i> <span>Report</span></a>
							</li>
							<li class="{{Request::is('admin/profile')?'active':''}}"> 
								<a href="{{url('admin/profile') }}" ><i class="fe fe-user-plus"></i> <span>Profile</span></a>
							</li>							
						</ul>
					</div>
                </div>
            </div>
			<!-- /Sidebar -->