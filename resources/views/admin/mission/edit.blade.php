@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/datatables/datatables.min.css') }}">		
	<link rel="stylesheet" href="{{asset('admin/assets/css/chosen.css') }}">		
	<style>
	.chosen-single {
		height:40px !important;
		padding:8px !important;
	}
	</style>
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
									<li class="breadcrumb-item active">Edit </li>
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
								<form method="post" enctype="multipart/form-data" autocomplete="off" id="update_service" action="{{url('admin/updatemission')}}">
									@csrf
                                    <input type="hidden" name="m_id" value="{{$mission->id}}" />
									<div class="submit-section text-right mb-1">
										<button class="btn btn-primary submit-btn" type="submit">Submit</button>
									</div>
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													@if (!$mission->status)
                                                    <div class="alert alert-dark alert-dismissible fade show mb-0">
                                                        Currently this mission Status is <label class="badge badge-pill badge-warning text-white badge-large py-2 px-3 ">Pending</label>.
                                                        To make it  <label class="badge badge-pill badge-success text-white badge-large py-2 px-3 ">Active</label> , please assign main consultant.
                                                    </div>
													@else
                                                    <div class="alert alert-dark alert-dismissible fade show mb-0">
                                                        Currently this mission Status is <label class="badge badge-pill badge-success text-white badge-large py-2 px-3 ">Active</label>.
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
													<input class="form-control" type="text" name="name" required value="{{$mission->name}}">
												</div>
											</div>
											<div class="col-lg-12">
												<div class="form-group">
													<label>Mission Name (Arabic) <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="name_ar" required value="{{$mission->name_ar}}">
												</div>
											</div>											
										</div>
									</div>
									
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Main Consultant</label>
													<select class="form-control chosen-select" name="consul"> 
														<option value="">Choose a Main Consultant</option>
														@foreach ($consuls as $one) 
														<option value="{{$one->id}}" {{$mission->consultant_id == $one->id?'selected':''}} >
															<h2 class="table-avatar">																
																<a href="#">
																	{{$one->user->userinfo->fname. ' '. $one->user->userinfo->lname}}
																	<span>@ {{$one->user->name}}</span>
																</a>                                                
															</h2>
														</option>
														@endforeach
														<!-- list of main consultants -->
													</select>
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>No. of Users </label>
													<input type="number" class="form-control" name="users_cnt" value="{{$mission->number_of_users}}" />
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Cost per User  (<i class="fa fa-dollar"></i>)<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="cost" value="{{$mission->cost_per_user}}"/>
												</div>
											</div>
										</div>
									</div>
																		                                    
                                    <div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label>About </label>
													<textarea id="about" class="form-control service-desc" name="about">{{$mission->description}}</textarea>
												</div>
											</div>
										</div>
                                    </div>
                                    
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="service-upload">
													<i class="fas fa-cloud-upload-alt"></i>
													<span>Upload Mission Cover Image</span>
													<input type="file" name="cover" id="cover_file" accept="image/jpeg, image/png, image/gif,">
												
												</div>	
												<div id="uploadPreview ">
													<ul class="upload-wrap offset-md-4 col-md-4" style="list-style-type: none;">
														<li>
															<div class=" upload-images text-center">
																<img alt="Blog Image" id="cover_img" class="w-100" 
                                                                    src="{{ 
                                                                        $mission->cover_image!=''?asset($mission->cover_image):
                                                                            asset('admin/assets/img/profiles/avatar-17.jpg')}}">
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
    <script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js') }}"></script>
	<script src="{{ asset('admin/assets/js/chosen.jquery.js') }}"></script>
@endsection


@section('bottom-js')
<script>


$(document).ready(function(){
	/* $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); */
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

