@extends ('layouts.client.main')

@section('page-css')

@endsection

@section('main-content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('Contact Us') }} </h2>                
            </div>
        </div>
    </div>   
</div>
<!-- /Breadcrumb -->


			<!-- Page Content -->
			<div class="content">
				<div class="container">

					<div class="row">
						<div class="col-md-8 col-lg-8 m-auto">
							<div class="card">
								<div class="card-body">
								
									<!-- Checkout Form -->
									<form action="{{url('/contact-us/submit')}}" method="POST">									
                                    @csrf
                                        <input type="hidden" name="m_id" value="{{$mission->id}}"/>
                                        <div class="info-widget text-center">
                                            <img src="{{ asset($mission->setting->logo)}}" class="m-auto "/>
                                        </div>
										<!-- Personal Information -->
										<div class="info-widget">
											<h4 class="card-title">Contact Us</h4>
											<div class="row">                                                
												<div class="col-md-6 col-sm-12">
													<div class="form-group card-label">
														<label>First Name</label>
														<input class="form-control" type="text" name="fname">
													</div>
												</div>
												<div class="col-md-6 col-sm-12">
													<div class="form-group card-label">
														<label>Last Name</label>
														<input class="form-control" type="text" name="lname">
													</div>
												</div>
												<div class="col-md-6 col-sm-12">
													<div class="form-group card-label">
														<label>Email</label>
														<input class="form-control" type="email" name="email">
													</div>
												</div>
												<div class="col-md-6 col-sm-12">
													<div class="form-group card-label">
														<label>Phone (optional)</label>
														<input class="form-control" type="text" name="phone">
													</div>
												</div>
											</div>
										</div>
										<!-- /Personal Information -->
										
										<div class="payment-widget">
											<!-- Credit Card Payment -->
											<div class="payment-list">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group card-label">
															<label for="card_name">Subject</label>
															<input class="form-control" id="card_name" type="text" name="subject">
														</div>
													</div>
													<div class="col-md-12">
														<div class="form-group card-label">
                                                            <textarea class="form-control" rows="7" name="message"></textarea>
														</div>
													</div>												
												</div>
											</div>
											<!-- /Credit Card Payment -->
																																																					
											<!-- Submit Section -->
											<div class="submit-section mt-4">
                                                @include('alert')
												<button type="submit" class="btn btn-primary submit-btn">{{ __('Send Message') }}</button>
											</div>
											<!-- /Submit Section -->
											
										</div>
									</form>
									<!-- /Checkout Form -->
									
								</div>
							</div>
							
						</div>										
					</div>
				</div>

			</div>		
			<!-- /Page Content -->
@endsection


@section('page-js')

@endsection


@section('bottom-js')

@endsection