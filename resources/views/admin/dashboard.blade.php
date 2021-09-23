@extends ('layouts.admin.main')

@section('page-css')
@endsection


@section('main-content')			
	
	<!-- Page Wrapper -->
	<div class="page-wrapper">
	
		<div class="content container-fluid">
			
			<!-- Page Header -->
			<div class="page-header">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="page-title">Welcome Admin!</h3>
						<ul class="breadcrumb">
							<li class="breadcrumb-item active">Dashboard</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /Page Header -->

			<div class="row">
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-primary border-primary">
									<i class="fe fe-users"></i>
								</span>
								<div class="dash-count">
									<h3>{{$clients}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								<h6 class="text-muted">Clients</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-primary w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-primary border-primary">
									<i class="fe fe-users"></i>
								</span>
								<div class="dash-count">
									<h3>{{$mission}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								<h6 class="text-muted">Mission</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-primary w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-primary border-primary">
									<i class="fe fe-users"></i>
								</span>
								<div class="dash-count">
									<h3>{{$consultants}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								<h6 class="text-muted">Consultants</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-primary w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>                        
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-primary border-primary">
									<i class="fe fe-users"></i>
								</span>
								<div class="dash-count">
									<h3>{{$secretary}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								<h6 class="text-muted">Secretary</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-primary w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>                        
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-primary">
									<i class="fe fe-calendar"></i>
								</span>
								<div class="dash-count">
									<h3>{{$bookings}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">All Appointments</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-primary w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success">
									<i class="fe fe-calendar"></i>
								</span>
								<div class="dash-count">
									<h3>{{$finished_bookings}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">Finished</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-success w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-warning">
									<i class="fe fe-calendar"></i>
								</span>
								<div class="dash-count">
									<h3>{{$upcoming_bookings}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">Upcoming</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-warning w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-danger">
									<i class="fe fe-calendar"></i>
								</span>
								<div class="dash-count">
									<h3>{{$declined_bookings}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">Declined</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-danger w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-warning border-warning">
									<i class="fe fe-info"></i>
								</span>
								<div class="dash-count">
									<h3>{{$transactions}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">Transactions</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-warning w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-warning border-warning">
									<i class="fe fe-money"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$earn}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">
								
								<h6 class="text-muted">Revenue</h6>
								<div class="progress progress-sm">
									<div class="progress-bar bg-warning w-50"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
		</div>			
	</div>
	<!-- /Page Wrapper -->

@endsection


@section('page-js')
@endsection


@section('bottom-js')
@endsection

