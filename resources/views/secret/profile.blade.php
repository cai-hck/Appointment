@extends ('layouts.secret.main')

@section('page-css')
@endsection


@section('main-content')

<!-- Basic Information -->
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{url('secret/profile')}}" enctype="multipart/form-data">
        @csrf
        <h4 class="card-title">{{__('Information')}}</h4>
        <div class="row form-row">
            <div class="col-md-12">
                @include ('alert')
                <div class="form-group">
                    <div class="change-avatar">
                        <div class="profile-img">
                            <img src="{{ asset(json_decode($user->userinfo->photo)->m) }}" alt="User Image" id="photo_preview">
                        </div>
                        <div class="upload-img">
                            <div class="change-photo-btn">
                                <span><i class="fa fa-upload"></i> {{ __('Upload Photo') }}</span>
                                <input type="file" class="upload" id="picture_photo" name="photo">
                            </div>
                            <small class="form-text text-muted">{{__('Allowed JPG, GIF or PNG. Max size of 2MB')}}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{__('Username')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="username" value="{{$user->name}}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('First Name')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="fname" value="{{$user->userinfo->fname}}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('Last Name')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lname" value="{{$user->userinfo->lname}}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Phone Number')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control phone_number" name="mobile" required value="{{$user->userinfo->mobile}}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Whatsapp Number')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control phone_number" name="whatsapp" required value="{{$user->userinfo->whatsapp}}">
                </div>
            </div>    
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Email')}} <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required value="{{$user->email}}">
                </div>
            </div>                                    																				
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Password')}}</label>
                    <input type="text" class="form-control" name="password">
                </div>
            </div>         
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __('Address')}}</label>
                    <input type="text" class="form-control" name="address" required value="{{$user->userinfo->address}}">
                </div>
            </div>              
            <div class="col-md-4">
                <div class="form-group d-flex" style="justify-content:flex-start">
                    <input id="email_notify" type="checkbox" class="mr-2" style="width:20px;height:20px" name="email_notify" {{$user->userinfo->notify_email?'checked':''}}/>
                    <label for="email_notify">{{ __('Notification via Email')}}</label>
                </div>
            </div>            
            <div class="col-md-4">
                <div class="form-group d-flex" style="justify-content:flex-start">
                    <input id="phone_notify" type="checkbox" class="mr-2" style="width:20px;height:20px" name="phone_notify" {{$user->userinfo->notify_phone?'checked':''}}/>
                    <label for="phone_notify">{{ __('Notification via Phone SMS')}}</label>
                </div>
            </div>                             																				                                        
            <div class="col-md-4">
                <div class="form-group d-flex" style="justify-content:flex-start">
                    <input id="whatsapp_notify" type="checkbox" class="mr-2" style="width:20px;height:20px" name="whatsapp_notify" {{$user->userinfo->notify_whatsapp?'checked':''}}/>
                    <label for="whatsapp_notify">{{ __('Notification via Whatsapp SMS')}}</label>
                </div>
            </div>                                       																				                                        
        </div>
        <div class="submit-section submit-btn-bottom">
            <button type="submit" class="btn btn-primary submit-btn">{{ __('Save Changes')}}</button>
        </div>
        </form>
    </div>
</div>
<!-- /Basic Information -->																																		                            

@endsection


@section('page-js')

@endsection


@section('bottom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script>

$(document).ready(function(){
    $('.phone_number').inputmask('+99999999999999');
    $('#picture_photo').change(function(e){
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

