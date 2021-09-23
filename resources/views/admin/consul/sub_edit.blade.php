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
							<div class="col-sm-8">
								<h3 class="page-title">Consultants</h3>
								<ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{url('admin/consultant/add')}}">Consultant</a></li>
									<li class="breadcrumb-item active">Edit Sub Accounts</li>
								</ul>
                            </div>
						</div>
					</div>
					<!-- /Page Header -->

					<div class="row">
						<div class="col-md-8">
                            @include ('alert')
							<!-- Recent Orders -->
							<div class="card">
								<div class="card-body">                                                                

                                <div class="row">
                                    <div class="col-md-12">
                                    <div class="avatar avatar-xxl mr-3">
                                    <img src="{{ asset(json_decode($consul->user->userinfo->photo)->l) }}" 
                                        class="avatar-img rounded-circle "/>
                                    </div>
                                    @if ($consul->user->role == 'consul')
                                    <a class="btn  btn-rounded bg-success text-white mr-5">                                        
                                        Consultant Account
                                    </a>
                                    @else
                                    <a class="btn  btn-rounded bg-warning text-white mr-5">                                        
                                        Secretary Account
                                    </a>
                                    @endif
                                    @if ($consul->status)
                                        @if ($consul->status && strtotime($consul->expire_date) < strtotime(date('Y-m-d')))
                                        <a class="btn  btn-rounded bg-danger text-white mr-5">                                        
                                            Expired
                                        </a>
                                        @else
                                        <a class="btn btn-rounded bg-success text-white mr-5">                                        
                                            Active
                                        </a>
                                        @endif
                                    @else
                                    <a class="btn  btn-rounded bg-dark text-white mr-5">                                        
                                        Inactive
                                    </a>                                
                                    @endif                                                                       
                                </div>                                                                 
                                </div>
                               
                                <!-- Add Consultant -->
								<form method="post" enctype="multipart/form-data" autocomplete="off"  action="{{url('admin/updatesubconsultant')}}">
                                    @csrf
                                    <input type="hidden" name="c_id" value="{{$consul->id}}" />
                                    <input type="hidden" name="u_id" value="{{$consul->user->id}}" />
									<div class="submit-section text-right">                                                
										<button class="btn btn-primary submit-btn" >Submit</button>
									</div>                                        
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Username <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="username"  value="{{$consul->user->name}}" required="">
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>First Name <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="fname" value="{{$consul->user->userinfo->fname}}" required="">
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>Last Name <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="lname" value="{{$consul->user->userinfo->lname}}" required="">
												</div>
											</div>
										</div>
									</div>
									
									<div class="service-fields mb-3">
										<div class="row">		

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Address <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text" name="address" value="{{$consul->user->userinfo->address}}" required="">
                                                </div>
                                            </div>                                                          
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Mobile <span class="text-danger">*</span></label>
                                                    <input class="form-control phone_number" type="text" name="mobile" value="{{$consul->user->userinfo->mobile}}" required="">
                                                </div>
                                            </div>                                                
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Whatsapp <span class="text-danger">*</span></label>
                                                    <input class="form-control  phone_number" type="text" name="whatsapp" value="{{$consul->user->userinfo->whatsapp}}" required="">
                                                </div>
                                            </div>    
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Email <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="email" name="email" value="{{$consul->user->email}}" required="">
                                                </div>
                                            </div>    
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Password <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text" name="password" value="{{$consul->user->userinfo->pwd_code}}" required="">
                                                </div>
                                            </div>                                                
										</div>
									</div>																		                                                                                                           															
								</form>
								<!-- /Add Blog -->

								</div>
							</div>
							<!-- /Recent Orders -->
							
                        </div>
                        <div class="col-md-4">		
							<div class="card">
								<div class="card-header"><strong> Send Email, SMS, Whatsapp </strong></div>
								<div class="card-body">
								
								<div class="card">
									<div class="card-header"><h5>Sending Email</h5></div>
									<div class="card-body">
									<form action="{{url('/sendsingleemail')}}" method="Post">
										@csrf                        
										<div class="form-group">
											<label>Receiver <span class="text-danger">*</span></label>
											<input type="text" class="form-control" name="to_email" value="{{$consul->user->email}}" readonly />
										</div>                            
										<div class="form-group">
											<label>Subject <span class="text-danger">*</span></label>
											<input type="text" class="form-control" name="subject" required value="" />
										</div>                                                    
										<div class="form-group">
											<label>Content <span class="text-danger">*</span></label>
											<textarea type="text" class="form-control" name="about" rows="3" required></textarea>
										</div>   
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-sm" value="Send" />
										</div>     
										</form>                                                                                                                                                
									</div>
								</div>                
								<div class="card">
									<div class="card-header"><h5>Sending SMS</h5></div>
									<div class="card-body">
										<form action="{{url('/sendsinglesms')}}" method="Post">
										@csrf
										<div class="form-group">
											<label>Receiver Phone. No <span class="text-danger">*</span></label>
											<input type="text" class="form-control" name="to_phone" value="{{$consul->user->userinfo->mobile}}" readonly />
										</div>                            
										<div class="form-group">
											<label>Message <span class="text-danger">*</span></label>
											<textarea type="text" class="form-control" name="message" rows="3" required></textarea>
										</div>   
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-sm" value="Send" />
										</div> 
										</form>                                                                                                                                                    
									</div>
								</div>            
								<div class="card">
									<div class="card-header"><h5>Sending Whatsapp SMS</h5></div>
									<div class="card-body">
									<form action="{{url('/sendwhatsappsms')}}" method="Post">
										@csrf
										<div class="form-group">
											<label>Receiver Whatsapp. No <span class="text-danger">*</span></label>
											<input type="text" class="form-control" name="to_phone" value="{{$consul->user->userinfo->whatsapp}}" readonly />
										</div>                            
										<div class="form-group">
											<label>Message <span class="text-danger">*</span></label>
											<textarea type="text" class="form-control" name="message" rows="3" required></textarea>
										</div>   
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-sm" value="Send" />
										</div>           
									</form>                                                                                                                                          
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

