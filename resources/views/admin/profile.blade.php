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
								<h3 class="page-title">Profile</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Profile</li>
								</ul>
                            </div>
						</div>
					</div>
					<!-- /Page Header -->
					<div class="row">                    
                        <div class="col-md-12">

                            @include ('alert')
							<div class="profile-header">
								<div class="row align-items-center">
									<div class="col-auto profile-image">
                                        <?php 
                                            $pic = $user->userinfo? asset(json_decode($user->userinfo->photo)->m):'assets/img/profiles/avatar-01.jpg';
                                        ?>
										<a href="#">                                        
											<img class="rounded-circle" alt="User Image" 
                                                src="{{$pic}}"
                                            >
										</a>
									</div>
									<div class="col ml-md-n2 profile-user-info">
										<h4 class="user-name mb-0">{{$user->userinfo?$user->userinfo->fname. ' ' . $user->userinfo->lname:$user->name}}</h4>                                        
										<h6 class="text-muted">{{$user->email}}</h6>
                                        <div class="user-Location"><i class="fa fa-map-marker"></i> {{$user->userinfo?$user->userinfo->address:''}}</div>
                                        <div class="user-Location"><i class="fa fa-user"></i> Administrator</div>
									</div>
									<div class="col-auto profile-btn">
										
										<a class="edit-link" data-toggle="modal" href="#edit_personal_photo">
											<i class="fa fa-edit mr-1"></i> Edit Photo
										</a>
									</div>
								</div>
							</div>
							<div class="profile-menu">
								<ul class="nav nav-tabs nav-tabs-solid">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#per_details_tab">About</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#password_tab">Password</a>
									</li>
								</ul>
							</div>	
							<div class="tab-content profile-tab-cont">
								
								<!-- Personal Details Tab -->
								<div class="tab-pane fade show active" id="per_details_tab">
								
									<!-- Personal Details -->
									<div class="row">
										<div class="col-lg-12">
											<div class="card">
												<div class="card-body">
													<h5 class="card-title d-flex justify-content-between">
														<span>Personal Details</span> 
														<a class="edit-link" data-toggle="modal" href="#edit_personal_details"><i class="fa fa-edit mr-1"></i>Edit</a>
													</h5>
													<div class="row">
														<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Full Name</p>
														<p class="col-sm-10">{{$user->userinfo?$user->userinfo->fname. ' ' . $user->userinfo->lname:$user->name}}</p>
													</div>
													<div class="row">
														<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Email</p>
														<p class="col-sm-10">{{$user->email}}</p>
													</div>
													<div class="row">
														<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Mobile</p>
														<p class="col-sm-10">{{$user->userinfo?$user->userinfo->mobile:'Null'}}</p>
                                                    </div>
													<div class="row">
														<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Whatsapp</p>
														<p class="col-sm-10">{{$user->userinfo?$user->userinfo->whatsapp:'Null'}}</p>
													</div>                                                    
													<div class="row">
														<p class="col-sm-2 text-muted text-sm-right mb-0">Address</p>
														<p class="col-sm-10 mb-0">{{$user->userinfo?$user->userinfo->address:'Null'}}</p>
													</div>
												</div>
											</div>
											
											<!-- Edit Details Modal -->
											<div class="modal fade" id="edit_personal_details" aria-hidden="true" role="dialog">
												<div class="modal-dialog modal-dialog-centered" role="document" >
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title">Personal Details</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<form actio="{{url('admin/profile')}}" method="post">
                                                            @csrf
                                                                <input type="hidden" name="admin_id" value="{{$user->id}}" />
																<div class="row form-row">
                                                                    <div class="col-12 col-sm-12">
																		<div class="form-group">
																			<label>Username</label>
																			<input type="text" class="form-control" value="{{$user->name}}" name="username">
																		</div>
																	</div>                                                                
																	<div class="col-12 col-sm-6">
																		<div class="form-group">
																			<label>First Name</label>
																			<input type="text" class="form-control" value="{{$user->userinfo?$user->userinfo->fname:''}}"  name="fname">
																		</div>
																	</div>
																	<div class="col-12 col-sm-6">
																		<div class="form-group">
																			<label>Last Name</label> 
																			<input type="text"  class="form-control" value="{{$user->userinfo?$user->userinfo->lname:''}}"  name="lname">
																		</div>
																	</div>															
																	<div class="col-12 col-sm-6">
																		<div class="form-group">
																			<label>Email</label>
																			<input type="email" class="form-control" value="{{$user->email}}"  name="email">
																		</div>
																	</div>
																	<div class="col-12 col-sm-6">
																		<div class="form-group">
																			<label>Mobile</label>
																			<input type="text" value="{{$user->userinfo?$user->userinfo->mobile:''}}" class="form-control  phone_number"  name="mobile">
																		</div>
                                                                    </div>
																	<div class="col-12 col-sm-6">
																		<div class="form-group">
																			<label>Whatsapp</label>
																			<input type="text" value="{{$user->userinfo?$user->userinfo->whatsapp:''}}" class="form-control  phone_number"  name="whatsapp">
																		</div>
																	</div>                                                                    
																	<div class="col-12">
																		<h5 class="form-title"><span>Address</span></h5>
																	</div>
																	<div class="col-12">
																		<div class="form-group">
																		<label>Address</label>
																			<input type="text" class="form-control" value="{{$user->userinfo?$user->userinfo->address:''}}"  name="address">
																		</div>
																	</div>	
                                                                </div>                                                                																
																<button type="submit" class="btn btn-primary btn-block">Save Changes</button>
															</form>
														</div>
													</div>
												</div>
											</div>
											<!-- /Edit Details Modal -->
                                            
											<!-- Edit Details Modal -->
											<div class="modal fade" id="edit_personal_photo" aria-hidden="true" role="dialog">
												<div class="modal-dialog modal-dialog-centered" role="document" >
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title">Photo</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<form action="{{url('admin/picupload')}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
																<div class="row form-row">
                                                                    <div class="col-12 col-sm-12">
																		<div class="form-group text-center">
																			<label>Preview Photo</label><br/>
																			<img id="photo_preview" src="assets/img/profile.jpg" class="w-50"/>
																		</div>
																	</div>		
																	<div class="col-12 col-sm-12">
																		<div class="form-group">
																			<label>Change Photo</label>
																			<input type="file" class="form-control" name="photo" id="picture_photo"/>
																		</div>
																	</div>																	
																</div>
																<button type="submit" class="btn btn-primary btn-block">Save Changes</button>
															</form>
														</div>
													</div>
												</div>
											</div>
                                            <!-- /Edit Details Modal -->
                                                                                        
										</div>

									
									</div>
									<!-- /Personal Details -->

								</div>
								<!-- /Personal Details Tab -->
								
								<!-- Change Password Tab -->
								<div id="password_tab" class="tab-pane fade">
								
									<div class="card">
										<div class="card-body">
											<h5 class="card-title">Change Password</h5>
											<div class="row">
												<div class="col-md-10 col-lg-6">
													<form method="post" action="{{url('admin/pwdchange')}}">
                                                        @csrf
														<div class="form-group">
															<label>Old Password</label>
															<input type="password" class="form-control" name="old">
														</div>
														<div class="form-group">
															<label>New Password</label>
															<input type="password" class="form-control" name="new">
														</div>
														<div class="form-group">
															<label>Confirm Password</label>
															<input type="password" class="form-control" name="confirm">
														</div>
														<button class="btn btn-primary" type="submit">Save Changes</button>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Change Password Tab -->
								
							</div>
						</div>
					</div>
                </div>			
                                
			</div>
			<!-- /Page Wrapper -->


@endsection


@section('page-js')
<script>
$(document).ready(function(){
    $('#picture_photo').change(function(e){
        const file = this.files[0]; 
        if (file){ 
          let reader = new FileReader(); 
          reader.onload = function(event){
            $('#photo_preview').attr('src', event.target.result); 
          } 
          reader.readAsDataURL(file); 
        } 
    });
})
</script>
@endsection


@section('bottom-js')
@endsection

