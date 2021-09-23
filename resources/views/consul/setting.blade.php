@extends ('layouts.consul.main')

@section('page-css')
@endsection


@section('main-content')
<!-- Basic Information -->
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{url('consul/msetting/save')}}" enctype="multipart/form-data">
            @csrf
            <h4 class="card-title">{{__('Information')}}</h4>
            <div class="row form-row">
                <div class="col-md-12">
                    @include ('alert')
                    <div class="form-group">
                        <div class="change-avatar">
                            <div class="profile-img">
                                <img style="width:100%;height:auto" src="{{$mission_setting? asset($mission_setting->logo) :''}}" alt="User Image" id="photo_preview">
                            </div>
                            <div class="upload-img">
                                <div class="change-photo-btn">
                                    <span><i class="fa fa-upload"></i> {{ __('Mission Logo') }}</span>
                                    <input type="file" class="upload" id="picture_photo" name="logo">
                                </div>
                                <small class="form-text text-muted">{{__('Allowed JPG, GIF or PNG. Max size of 2MB')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Mission Name')}} (English)<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mname_en" value="{{$mission->name}}" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Mission Name')}} (Arabic)<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mname_ar" value="{{$mission->name_ar}}" >
                    </div>
                </div>    
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" value="{{$mission_setting?$mission_setting->contact_no:''}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="text" class="form-control" name="contact_email" value="{{$mission_setting?$mission_setting->contact_email:''}}">
                    </div>                
                </div>   
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="contact_address" value="{{$mission_setting?$mission_setting->contact_address:''}}">
                    </div>            
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Description (English) </label>
                        <textarea type="text" class="form-control" rows="5" name="description_en">{!! $mission_setting?$mission_setting->description_en:'' !!}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Description (Arabic) </label>
                        <textarea type="text" class="form-control" rows="5" name="description_ar">{!! $mission_setting?$mission_setting->description_ar:'' !!}</textarea>
                    </div>
                </div>    
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Notification Mission Email Subject (English)</label>
                        <input type="text" class="form-control" name="email_subject_en" value="{{$mission_setting?$mission_setting->email_subject_en:''}}" >
                    </div>
                </div>    
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Notification Mission Email Subject (Arabic)</label>
                        <input type="text" class="form-control" name="email_subject_ar"  value="{{$mission_setting?$mission_setting->email_subject_ar:''}}" >
                    </div>
                </div>                            
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Page Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" required value="{{$mission_setting && $mission_setting->slug?$mission_setting->slug: str_replace(' ' ,'-',strtolower($mission->name)) }}" >
                    </div>
                </div>                                            
            </div>
            <div class="submit-section submit-btn-bottom">
                <button type="submit" class="btn btn-primary submit-btn">{{ __('Save Changes')}}</button>
            </div>
        </form>        
    </div>
</div>

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
        if (file) { 
          let reader = new FileReader(); 
          reader.onload = function(event){
            $('#photo_preview').attr('src', event.target.result); 
          } 
          reader.readAsDataURL(file); 
        } 
    });
});
</script>
@endsection
