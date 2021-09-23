@extends ('layouts.consul.main')

@section('page-css')
@endsection


@section('main-content')

        <!-- Basic Information -->
        <form action="{{url('consul/addaccount')}}" method="post" enctype="multipart/form-data">
        @csrf        
        <div class="card">
            @include('alert')
            <div class="card-body mt-1">
                <h4 class="card-title">{{ __('Information')}}</h4>
                <div class="row form-row">

                    @if ($account_limit == 0)
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="alert alert-danger alert-dismissible fade show mb-0">
                                        {{__('You have reached to the limitation of creating sub Consultant and Secretary accounts.') }}<br>
                                        {{__('You can get ability to add more sub accounts from') }} <a href="{{url('consul/payment')}}">{{ __('HERE')}}</a>.  
                                    </div>											
                                </div>
                            </div>
                        </div>
                    </div>      
                    @endif
                                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="change-avatar">
                                <div class="profile-img">
                                    <img src="{{ asset('client/assets/img/doctors/doctor-thumb-02.jpg')}}" id="photo_preview" alt="User Image">
                                </div>
                                <div class="upload-img">
                                    <div class="change-photo-btn">
                                        <span><i class="fa fa-upload"></i> {{ __('Upload Photo') }}</span>
                                        <input type="file" class="upload" name="photo" id="picture_photo" required>
                                    </div>
                                    <small class="form-text text-muted">{{ __('Allowed JPG, GIF or PNG. Max size of 2MB') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Role <span class="text-danger">*</span></label>
                            <select class="form-control" name="role" required>
                                <option value="">{{ __('Choose a Role') }}</option>
                                <option value="consul">{{ __('Consultant') }}</option>
                                <option value="secret">{{ __('Secretary') }}</option>
                            </select>
                        </div>
                    </div>                                        
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Username') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required name="username">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required name="fname">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required name="lname">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control phone_number" required name="mobile">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Whatsapp Number') }}</label>
                            <input type="text" class="form-control phone_number" required name="whatsapp">
                        </div>
                    </div>    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" required name="email">
                        </div>
                    </div>                                    																				
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Password') }}</label>
                            <input type="text" class="form-control" required name="password"> 
                        </div>
                    </div>         
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('Address') }}</label>
                            <input type="text" class="form-control" required name="address">
                        </div>
                    </div>                                     																				                                        
                </div>
                <div class="submit-section submit-btn-bottom">
                    @if ($account_limit != 0)
                    <button type="submit" class="btn btn-primary submit-btn">{{ __('Save Changes') }}</button>
                    @endif
                </div>
            </div>
        </div>
        </form>
        <!-- /Basic Information -->		


@endsection


@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>

@endsection


@section('bottom-js')
<script>

$(document).ready(function(){
    $('.phone_number').inputmask('+99999999999999');
    $('.digits').inputmask('999999');
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

