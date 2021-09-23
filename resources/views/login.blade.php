@extends ('layouts.client.main')

@section('page-css')
@endsection


@section('main-content')


<!-- Page Content -->
<div class="content bg-white">
    <div class="container-fluid ">
        
        <div class="row">
            <div class="col-md-8 offset-md-2">
                
                <!-- Login Tab Content -->
                <div class="account-content" >
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-7 col-lg-6 login-left">
                            <img src="{{ asset('client/assets/img/login-banner.png')}}" class="img-fluid" alt="Doccure Login">	
                        </div>
                        <div class="col-md-12 col-lg-6 login-right">
                            <div class="login-header">
                                <h3>{{__('Login  Consultant & Secretary')}}</h3>
                            </div>
                            @include ('alert')
                            <form action="{{url('/login') }}" method="POST">
                            @csrf
                                <div class="form-group form-focus">
                                    <input type="text" class="form-control floating" name="username">
                                    <label class="focus-label">{{__('Username')}}</label>
                                </div>
                                <div class="form-group form-focus">
                                    <input type="password" class="form-control floating" name="password">
                                    <label class="focus-label">{{__('Password')}}</label>
                                </div>
<!--                                 <div class="text-right">
                                    <a class="forgot-link" href="forgot-password.html">Forgot Password ?</a>
                                </div> -->
                                <button class="btn btn-primary btn-block btn-lg login-btn" type="submit">{{__('Login')}}</button>																				
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Login Tab Content -->
                    
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

