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
								<ul class="breadcrumb">
                                    <li class="breadcrumb-item">Dashboard</li>
                                    <li class="breadcrumb-item active">Settings</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					<div class="row">
                        <div class="col-md-12">
                            @include ('alert')
                        </div>                 
						<div class="col-md-12">
							<div class="card">
									<div class="card-header">
										<h4 class="card-title">Social Medias</h4>
									</div>
									<div class="card-body">
										<form action="{{url('admin/setting/social')}}" method="post">
                                            @csrf
                                            <div class="form-group">
												<label>Facebook</label>
												<input type="text" class="form-control" name="facebook" value="{{$setting['facebook']!=false?$setting['facebook']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Twitter</label>
												<input type="text" class="form-control" name="twitter" value="{{$setting['twitter']!=false?$setting['twitter']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Linkedin</label>
												<input type="text" class="form-control" name="linkedin" value="{{$setting['linkedin']!=false?$setting['linkedin']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Instagram</label>
												<input type="text" class="form-control" name="instagram" value="{{$setting['instagram']!=false?$setting['instagram']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Dribble</label>
												<input type="text" class="form-control" name="dribble" value="{{$setting['dribble']!=false?$setting['dribble']:''}}">
                                            </div>		
                                            <div class="form-group">
												<label>Youtube</label>
												<input type="text" class="form-control" name="youtube" value="{{$setting['youtube']!=false?$setting['youtube']:''}}">
                                            </div>																																																							
											<div class="form-group mb-0">
                                                <button class="btn btn-primary btn-lg">Save Changes</button>
                                            </div>
										</form>
									</div>
							</div>

						</div>       
						<div class="col-md-6">
							
							<!-- General -->
							
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">General (English)</h4>
									</div>
									<div class="card-body">
                                       
										<form action="{{url('admin/setting/en')}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
												<label>Contact Number</label>
												<input type="text" class="form-control" name="contact_number" value="{{$setting['en_contact_number']!=false?$setting['en_contact_number']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Contact Email</label>
												<input type="text" class="form-control" name="contact_email" value="{{$setting['en_contact_email']!=false?$setting['en_contact_email']:''}}">
                                            </div>                   
                                            <div class="form-group">
												<label>Address</label>
												<input type="text" class="form-control" name="address" value="{{$setting['en_address']!=false?$setting['en_address']:''}}">
                                            </div>            
											<div class="form-group">
												<label>Website Name</label>
												<input type="text" class="form-control" name="website_name" value="{{$setting['en_website_name']!=false?$setting['en_website_name']:''}}">
											</div>
											<div class="form-group">
												<label>Website Logo</label>
												<input type="file" class="form-control" name="logo">
												<small class="text-secondary">Recommended image size is <b>150px x 150px</b></small>
                                                <div class="form-group">
                                                    <img src="{{$setting['en_logo']!=false? asset($setting['en_logo']):asset('upload/logo/logo.png')}}"/>                                                     
                                                </div>
                                            </div>
                                            <div class="form-group">
												<label>Description</label>
												<textarea type="text" class="form-control" rows="5" name="description">{{$setting['en_description']!=false?$setting['en_description']:''}}</textarea>
											</div>                                                                                                                                                                                
											<div class="form-group mb-0">
												<label>Favicon</label>
												<input type="file" class="form-control" name="icon">
												<small class="text-secondary">Recommended image size is <b>16px x 16px</b> or <b>32px x 32px</b></small><br>
												<small class="text-secondary">Accepted formats : only png and ico</small>
                                                <div class="form-group">
                                                    <img src="{{$setting['en_icon']!=false? asset($setting['en_icon']):asset('upload/logo/favicon.png')}}"/>                                                     
                                                </div>
											</div>
											<div class="form-group mb-0">
                                                <button class="btn btn-primary btn-lg">Save Changes</button>
                                            </div>
										</form>
									</div>
								</div>
							
							<!-- /General -->
								
						</div>
						<div class="col-md-6">
							
							<!-- General -->
							
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">General (Arabic)</h4>
									</div>
									<div class="card-body">
                                        
										<form action="{{url('admin/setting/ar')}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
												<label>Contact Number</label>
												<input type="text" class="form-control" name="contact_number" value="{{$setting['ar_contact_number']!=false?$setting['ar_contact_number']:''}}">
                                            </div>
                                            <div class="form-group">
												<label>Contact Email</label>
												<input type="text" class="form-control" name="contact_email" value="{{$setting['ar_contact_email']!=false?$setting['ar_contact_email']:''}}">
                                            </div>                   
                                            <div class="form-group">
												<label>Address</label>
												<input type="text" class="form-control" name="address" value="{{$setting['ar_address']!=false?$setting['ar_address']:''}}">
                                            </div>            
											<div class="form-group">
												<label>Website Name</label>
												<input type="text" class="form-control" name="website_name" value="{{$setting['ar_address']!=false?$setting['ar_address']:''}}">
											</div>
											<div class="form-group">
												<label>Website Logo</label>
												<input type="file" class="form-control" name="logo">
												<small class="text-secondary">Recommended image size is <b>150px x 150px</b></small>
                                                <div class="form-group">
                                                    <img src="{{$setting['ar_logo']!=false? asset($setting['ar_logo']):asset('upload/logo/logo.png')}}"/>                                                     
                                                </div>
                                            </div>
                                            <div class="form-group">
												<label>Description</label>
												<textarea type="text" class="form-control" rows="5" name="description">{{$setting['ar_description']!=false?$setting['ar_description']:''}}</textarea>
											</div>                                                                                                                                                                                
											<div class="form-group mb-0">
												<label>Favicon</label>
												<input type="file" class="form-control" name="icon">
												<small class="text-secondary">Recommended image size is <b>16px x 16px</b> or <b>32px x 32px</b></small><br>
												<small class="text-secondary">Accepted formats : only png and ico</small>
                                                <div class="form-group">
                                                    <img src="{{$setting['ar_icon']!=false? asset($setting['ar_icon']):asset('upload/logo/favicon.png')}}"/>                                                     
                                                </div>
											</div>
                                            <div class="form-group mb-0">
                                                <button class="btn btn-primary btn-lg">Save Changes</button>
                                            </div>
										</form>
									</div>
								</div>
							
							<!-- /General -->
								
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

