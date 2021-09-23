@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/plugins/datatables/datatables.min.css">		
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
                                
                                <!-- Add Consultant -->
								<form method="post" enctype="multipart/form-data" autocomplete="off"  action="{{url('admin/saveconsultant')}}">
                                    @csrf
									<div class="submit-section text-right">
										<button class="btn btn-primary submit-btn" >Submit</button>
									</div>                                    
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Username <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="username"  value="" required="">
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>First Name <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="fname" value="" required="">
												</div>
                                            </div>
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>Last Name <span class="text-danger">*</span></label>
													<input class="form-control" type="text" name="lname" value="" required="">
												</div>
											</div>
										</div>
									</div>
									
									<div class="service-fields mb-3">
										<div class="row">											
                                            <div class="col-lg-4">
												<div class="form-group">
													<label>No. of Sub Users <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="number" name="users_cnt" value="2" required="">
												</div>                                                                                                                                                                                                                                                                                        
											</div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Address <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text" name="address" value="" required="">
                                                </div>
                                            </div>                                                          
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Mobile <span class="text-danger">*</span></label>
                                                    <input class="form-control phone_number" type="text" name="mobile" value="" required="">
                                                </div>
                                            </div>                                                
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Whatsapp <span class="text-danger">*</span></label>
                                                    <input class="form-control phone_number" type="text" name="whatsapp" value="" required="">
                                                </div>
                                            </div>    
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Email <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="email" name="email" value="" required="">
                                                </div>
                                            </div>    
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Password <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text" name="password" value="" required="">
                                                </div>
                                            </div>                                                
										</div>
									</div>
																		                                                                                                           
									<div class="service-fields mb-3">
										<div class="row">
											<div class="col-lg-12">
												<div class="service-upload">
													<i class="fas fa-cloud-upload-alt"></i>
													<span>Upload Consultant Photo <span class="text-danger">*</span></span>
													<input type="file" name="photo" id="pic_photo"  accept="image/jpeg, image/png, image/gif,">
												
												</div>	
												<div id="uploadPreview">
													<ul class="upload-wrap offset-md-4 col-md-4" style="list-style-type: none;">
														<li>
															<div class=" upload-images">
																<img alt="Profile Photo Image" id="photo_preview" class="w-100" src="{{ asset('admin/assets/img/profiles/avatar-17.jpg') }}">
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
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatables/datatables.min.js"></script>

@endsection


@section('bottom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script>
$(document).ready(function(){
	$('.phone_number').inputmask('+99999999999999');
    $('#pic_photo').change(function(e){
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

