<!-- Header -->
<header class="header">
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="navbar-header">
            <a id="mobile_btn" href="javascript:void(0);">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
            <a href="{{url('/')}}" class="navbar-brand logo">
                <img src="{{ asset($config['logo'])}}" class="img-fluid" alt="Logo">
            </a>
        </div>
        <div class="main-menu-wrapper">
            <div class="menu-header">
                <a href="{{url('/')}}" class="menu-logo">
                    <img src="{{ asset($config['logo'])}}" class="img-fluid" alt="Logo">
                </a>
                <a id="menu_close" class="menu-close" href="javascript:void(0);">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <ul class="main-nav">
                <li class="login-link">
                    @if (!session()->has('mslug'))
                    <a href="{{url('/login')}}">{{__('Login')}}</a>    
                    @else
                    <a href="{{url('m/'.session()->get('mslug').'/contact-us')}}">{{__('Contact Us')}}</a>    
                    @endif
                    <select class="form-control w-50 m-auto change-language">
                        <option value="en" {{session()->get('locale')=='en'?'selected':''}}>English</option>
                        <option value="ar" {{session()->get('locale')=='ar'?'selected':''}}>Arabic</option>
                    </select>

                </li>
            </ul>		 
        </div>		 
        <ul class="nav header-navbar-rht">
            <li class="nav-item contact-item">
                <div class="header-contact-img">
                    <i class="far fa-hospital"></i>							
                </div>
                <div class="header-contact-detail">
                    <p class="contact-header">{{__('Contact')}}</p>
                    <p class="contact-info-header">{{$config['contact_number']}}</p>
                </div>
            </li>
            @if (!session()->has('mslug'))
            <li class="nav-item">
                <a class="nav-link header-login" href="{{url('/login')}}">{{__('Login')}}</a>
            </li>
            @endif
            @if (session()->has('mslug'))
            <li class="nav-item">
                <a class="nav-link header-login" href="{{url('m/'.session()->get('mslug').'/contact-us')}}">{{__('Contact Us')}}</a>
            </li>
            @endif            
            <li class="nav-item">
                <select class="form-control change-language">
                    <option value="en" {{session()->get('locale')=='en'?'selected':''}}>English</option>
                    <option value="ar" {{session()->get('locale')=='ar'?'selected':''}}>Arabic</option>
                </select>
            </li>
        </ul>
    </nav>
    <form action="{{url('/setlang')}}" method="post" id="lang_form">
        @csrf
        <input type="hidden" name="locale" id="locale"/>
    </form>
</header>
<!-- /Header -->