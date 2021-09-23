<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>{{$config['website_name']}} - Admin Panel</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{asset($config['icon'])}}">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/font-awesome.min.css') }}">
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/feathericon.min.css') }}">
		
		<link rel="stylesheet" href="{{ asset('admin/assets/plugins/morris/morris.css') }}">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
		
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

        @yield('page-css')
    </head>
    <body>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
            @include ('layouts.admin.header')
			
            @include ('admin.sidemenu')
			
            @yield ('main-content')
            

         

            <!-- SMS Notify Modal -->
            <div class="modal fade" id="sms_modal" aria-hidden="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">SMS Message</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <form action="{{url('admin/sendsmsmessage')}}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Whatsapp No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone_no" value="" id="whatsapp_no" placholder="Whatsapp Number"/>
                                </div>
                                <div class="form-group">
                                    <label>Message <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" name="message" value="" id="message" placholder="Whatsapp Number"></textarea>
                                </div>

                                <p class="mb-4">Are you sure want to send message?</p>											
                                <button type="submit" class="btn btn-primary">Yes, Send Message </button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /SMS Modal -->




        </div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
        <script src="{{ asset('admin/assets/js/jquery-3.2.1.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="{{ asset('admin/assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap.min.js') }}"></script>
		
		<!-- Slimscroll JS -->
        <script src="{{ asset('admin/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
		
<!-- 	<script src="{{ asset('admin/assets/plugins/raphael/raphael.min.js') }}"></script>    
		<script src="{{ asset('admin/assets/plugins/morris/morris.min.js') }}"></script>  
		<script src="{{ asset('admin/assets/js/chart.morris.js') }}"></script> -->
		
		<!-- Custom JS -->			
        @yield('page-js')
        @yield('bottom-js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>        
        <script  src="{{ asset('admin/assets/js/script.js') }}"></script>
        <script>
            $(document).ready(function(){
                $('.phone_number').inputmask('+99-9999999999');
            });
        </script>        
    </body>
</html>