			<!-- Footer -->
			<footer class="footer">
				
				<!-- Footer Top -->
				<div class="footer-top">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-md-6">
							
								<!-- Footer Widget -->
								<div class="footer-widget footer-about">
									<div class="footer-logo">
										<!-- <img src="{{ asset('client/assets/img/footer-logo.png')}}" alt="logo"> -->
										<img src="{{ asset($config['logo'])}}" alt="logo">
									</div>
									<div class="footer-about-content">
										<p>{{$config['description']}}</p>
										<div class="social-icon">
											<ul>
												@if ($config['facebook']!='#')<li><a href="{{$config['facebook']}}" target="_blank"><i class="fab fa-facebook-f"></i> </a></li>@endif
												@if ($config['twitter']!='#')<li><a href="{{$config['twitter']}}" target="_blank"><i class="fab fa-twitter"></i> </a></li>@endif
												@if ($config['linkedin']!='#')<li><a href="{{$config['linkedin']}}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>@endif
												@if ($config['instagram']!='#')<li><a href="{{$config['instagram']}}" target="_blank"><i class="fab fa-instagram"></i></a></li>@endif
												@if ($config['dribble']!='#')<li><a href="{{$config['dribble']}}" target="_blank"><i class="fab fa-dribbble"></i> </a></li>@endif
											</ul>
										</div>
									</div>
								</div>
								<!-- /Footer Widget -->
								
							</div>
							
<!-- 							<div class="col-lg- col-md-6">
							
								<div class="footer-widget footer-menu">
									<h2 class="footer-title">{{__('For Client')}}</h2>
									<ul>
										<li><a href="{{url('/booking')}}">{{__('Booking')}}</a></li>
										<li><a href="{{url('/checking')}}">{{__('Check')}}</a></li>
									</ul>
								</div>
								
							</div>
							
							<div class="col-lg-3 col-md-6">
							
								<div class="footer-widget footer-menu">
									<h2 class="footer-title">{{ __('For Consultant')}}</h2>
									<ul>
										<li><a href="{{url('/login')}}">{{__('Login')}}</a></li>
										<li><a href="">{{__('Schedule')}}</a></li>
										<li><a href="">{{__('Appointment')}}</a></li>
										<li><a href="">{{__('Chat')}}</a></li>
										<li><a href="">{{__('Dashboard')}}</a></li>
									</ul>
								</div>
							
							</div> -->
							
							<div class="col-lg-6 col-md-6">
							
								<!-- Footer Widget -->
								<div class="footer-widget footer-contact">
									<h2 class="footer-title">{{__('Contact Us')}}</h2>
									<div class="footer-contact-info">
										<div class="footer-address">
											<span><i class="fas fa-map-marker-alt"></i></span>
											<p> {{$config['address']}}</p>
										</div>
										<p>
											<i class="fas fa-phone-alt"></i>
											{{$config['contact_number']}}
										</p>
										<p class="mb-0">
											<i class="fas fa-envelope"></i>
											{{$config['contact_email']}}
										</p>
									</div>
								</div>
								<!-- /Footer Widget -->
								
							</div>
							
						</div>
					</div>
				</div>
				<!-- /Footer Top -->
				
				<!-- Footer Bottom -->
                <div class="footer-bottom">
					<div class="container-fluid">
					
						<!-- Copyright -->
						<div class="copyright">
							<div class="row">
								<div class="col-md-6 col-lg-6">
									<div class="copyright-text">
										<p class="mb-0">&copy; {{__('Copyright')}}.</p>
									</div>
								</div>
								<div class="col-md-6 col-lg-6">
								
									<!-- Copyright Menu -->
									<div class="copyright-menu">
										<ul class="policy-menu">
											<li><a href="{{url('/terms-conditions')}}">{{__('Terms and Conditions')}}</a></li>
											<li><a href="{{url('/privacy-policy')}}">{{__('Policy')}}</a></li>
										</ul>
									</div>
									<!-- /Copyright Menu -->
									
								</div>
							</div>
						</div>
						<!-- /Copyright -->
						
					</div>
				</div>
				<!-- /Footer Bottom -->
				
			</footer>
			<!-- /Footer -->