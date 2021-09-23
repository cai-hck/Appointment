
<!DOCTYPE html> 
<html lang="{{app()->getLocale()}}">
	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<title>{{$config['website_name']}}</title>
		
		<!-- Favicons -->
		<link type="image/x-icon" href="{{ asset($config['icon'])}}" rel="icon">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/plugins/fontawesome/css/all.min.css') }}">
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('client/assets/'. app()->getLocale() .'/css/style.css') }}">
		
		<link rel="stylesheet" href="{{ asset('client/assets/lang_responsive.css') }}">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	    @yield('page-css')
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


        @include ('layouts.meeting.header')
		<div id="app">    
       	 	@yield('main-content')
		</div>

        @include ('layouts.client.footer')

        </div>
	   <!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="{{ asset('client/assets/'. app()->getLocale() .'/js/jquery.min.js')}}"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{ asset('client/assets/'. app()->getLocale() .'/js/popper.min.js')}}"></script>
		<script src="{{ asset('client/assets/'. app()->getLocale() .'/js/bootstrap.min.js')}}"></script>
		
		<!-- Slick JS -->
		<script src="{{ asset('client/assets/'. app()->getLocale() .'/js/slick.js')}}"></script>
		
		@yield('page-js')
        <!--  Here you can change to date slider and manage attributes about it  -->
		@yield('bottom-js')
		
		<!-- Custom JS -->
		<script src="{{ asset('client/assets/'. app()->getLocale() .'/js/script.js')}}"></script>
        <script>
            $(document).ready(function(){
                $('.change-language').change(function(){
                    if( $(this).val() == 'en') {
                        $('#lang_form').find('#locale').val('en');
                    } 
					if ($(this).val() == 'ar')
					{
                        $('#lang_form').find('#locale').val('ar');
                    }
					$('#lang_form').submit();
                })
            })
        </script>
	</body>
</html>
