@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/datatables/datatables.min.css') }}">		
@endsection


@section('main-content')

			<!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-8">
								<h3 class="page-title">Missions</h3>
								<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{url('admin/missin')}}">Mission</a></li>
									<li class="breadcrumb-item active">Add </li>
								</ul>
                            </div>
						</div>
					</div>
					<!-- /Page Header -->
					<div class="row">
						<div class="col-md-12">
							@include ('alert')						
							<!-- Recent Orders -->
							<div class="card">
								<div class="card-body">
                                    
                                <!-- Add Mission -->
								<form method="post" enctype="multipart/form-data" autocomplete="off" id="update_service" action="{{url('admin/savemission')}}">
									@csrf
									<div class="submit-section text-right">
										<button class="btn btn-primary submit-btn" type="submit">Submit</button>
									</div>
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													@if (count($consuls) == 0)
                                                    <div class="alert alert-warning alert-dismissible fade show mb-0">
														There is no available main consultants. Please create new main consultants.
                                                    </div>
													@endif												
												</div>
											</div>
										</div>
									</div>      
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label>Mission Name <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="name" value="" required>
												</div>
											</div>

											<div class="col-lg-12">
												<div class="form-group">
													<label>Mission Name (Arabic) <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="name_ar" value="" required>
												</div>
											</div>

										</div>
									</div>
									
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Main Consultant</label>
													<select class="form-control" name="consul"> 
														<option value="">Choose a Main Consultant</option>
														<!-- list of main consultants -->
														@foreach ($consuls as $one) 
														<option value="{{$one->id}}">
															<h2 class="table-avatar">																
																<a href="#">
																	{{$one->user->userinfo->fname. ' '. $one->user->userinfo->lname}}
																	<span>@ {{$one->user->name}}</span>
																</a>                                                
															</h2>
														</option>
														@endforeach
													</select>
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>No. of Users </label>
													 <input type="number" class="form-control" name="users_cnt" value="3"/>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Cost per User (<i class="fa fa-dollar"></i>) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="cost"/>
												</div>
											</div>
										</div>
									</div>
																		                                    
                                    <div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label>About </label>
													<textarea id="about" class="form-control service-desc" name="about"></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                    
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="service-upload">
													<i class="fas fa-cloud-upload-alt"></i>
													<span>Upload Mission Cover Image <span class="text-danger">*</span></span>
													<input type="file" name="cover" id="cover_file" accept="image/jpeg, image/png, image/gif,">
												
												</div>	
												<div id="uploadPreview ">
													<ul class="upload-wrap offset-md-4 col-md-4" style="list-style-type: none;">
														<li>
															<div class=" upload-images text-center">
																<img alt="Blog Image" id="cover_img" class="w-100" src="{{ asset('admin/assets/img/profiles/avatar-17.jpg')}}">
															</div>
														</li>													
													</ul>
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
					</div>
				</div>			
			</div>
			<!-- /Page Wrapper -->

@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
@endsection


@section('bottom-js')
<script>
$(document).ready(function(){
    $("#cover_file").change(function(e){
        const file = this.files[0]; 
        if (file){ 
          let reader = new FileReader(); 
          reader.onload = function(event){
            $('#cover_img').attr('src', event.target.result); 
          } 
          reader.readAsDataURL(file); 
        } 
    });
})
</script>
@endsection

