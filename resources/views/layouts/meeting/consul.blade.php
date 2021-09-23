<!DOCTYPE html> 
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{$config['website_name']}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		
		<!-- Favicons -->
		<link href="{{ asset($config['icon']) }}" rel="icon">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/css/bootstrap.css') }}">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/plugins/fontawesome/css/all.min.css') }}">
		
        <!-- Datatables CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.css') }}">
				
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/css/style.css') }}">

		<link rel="stylesheet" href="{{ asset('client/assets/lang_responsive.css') }}">
		

		@yield ('page-css')
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->	
		@if (app()->getLocale() == 'ar')
		<style>
			html, body {
				font-family: "IRANSansWeb";
			}

		</style>
		@endif
	</head>
	<body>

		<!-- Main Wrapper -->
		<div class="main-wrapper">
		
            @include ('layouts.consul.header')
			
			<!-- Breadcrumb -->
			<div class="breadcrumb-bar">
				<div class="container-fluid">
					<div class="row align-items-center">
						<div class="col-md-12 col-12">
							<h2 class="breadcrumb-title">{{$page_title}}</h2>
						</div>
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->
			
			<!-- Page Content -->
			<div class="content">
				<div class="container-fluid">

					<div class="row">
						<div class="col-md-2 col-lg-2 col-xl-2 theiaStickySidebar">
							
                            @include ('consul.sidemenu')
							
						</div>
						
						<div class="col-md-10 col-lg-10 col-xl-10">
						   
                            @yield('main-content')

						</div>
					</div>

				</div>

			</div>		
			<!-- /Page Content -->
			@include ('layouts.client.footer')			
		   			<!-- Delete Modal -->
			<div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
				<div class="modal-dialog modal-dialog-centered" role="document" >
					<div class="modal-content text-center">						
						<div class="modal-body">
							<div class="form-content p-2">
								<form action="{{url('consul/deleteaccount')}}" method="POST" id="del-form">
								@csrf
									<input type="hidden" name="u_id" id="u_id" />
									<h4 class="modal-title">Delete</h4>
									<p class="mb-4">{{__('Are you sure want to delete?') }}</p>											
									<button type="submit" class="btn btn-primary">{{ __('Yes, Sure') }} </button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close') }}</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Delete Modal -->


		</div>
		<!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="{{ asset('client/assets/' .app()->getLocale(). '/js/jquery.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{ asset('client/assets/' .app()->getLocale(). '/js/popper.min.js') }}"></script>
		<script src="{{ asset('client/assets/' .app()->getLocale(). '/js/bootstrap.min.js') }}"></script>
		
		<!-- Sticky Sidebar JS -->
        <script src="{{ asset('client/assets/' .app()->getLocale(). '/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
        <script src="{{ asset('client/assets/' .app()->getLocale(). '/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
		
		<!-- Circle Progress JS -->
		<script src="{{ asset('client/assets/' .app()->getLocale(). '/js/circle-progress.min.js') }}"></script>
		

		<!-- Datatables JS -->
		<script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.js') }}"></script>	

		@yield('page-js')
        <!--  Here you can change to date slider and manage attributes about it  -->
		@yield('bottom-js')		
    	<!-- Custom JS -->
		<script src="{{ asset('client/assets/' .app()->getLocale(). '/js/script.js') }}"></script>

		
		<script>
            $(document).ready(function(){

				$('.open-del-modal').click(function(){
					$('#del-form').find('#u_id').val($(this).attr('data-uid'));
				});
                $('.change-language').change(function(){
                    if( $(this).val() == 'en') {
                        $('#lang_form').find('#locale').val('en');
                    } 
					if ($(this).val() == 'ar')
					{
                        $('#lang_form').find('#locale').val('ar');
                    }
					$('#lang_form').submit();
                });


            });
		</script>
		

	</body>
</html>