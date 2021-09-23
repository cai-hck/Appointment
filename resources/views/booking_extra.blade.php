@extends ('layouts.client.main')

@section('page-css')


@endsection


@section('main-content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('Booking - Upload Extra File') }}</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
            
<!-- Page Content -->
<div class="content">
    <div class="container">
    
        <div class="row">
            <form method="POST" action="{{url('extrasubmit')}}"  autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="bkid" value="{{$booking->id}}" />
            <div class="col-12">         
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="booking-doc-info" style="justify-content: space-between;align-items: baseline;">
                                    <div class="booking-info w-100">
                                        <h4><a href="doctor-profile.html">{{app()->getLocale()=='en'?$mission->name:$mission->name_ar}}</a></h4>
                                        <h4 class="mt-3"><a href="#" class="view-section">{{app()->getLocale()=='en'?$section->en_name:$section->ar_name}}</a> <span class="badge badge-primary">{{__('Confirmed') }}</span></h4>
                                    </div>
                                    <div class="booking-info w-100">
                                        <h4 class="mt-3">{{__('Scheduled Time')}}</h4>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar"></i> {{$booking->schedule_date}} , {{\Carbon\Carbon::parse($booking->schedule_date)->format('l')}},
                                             {{\Carbon\Carbon::parse($booking->start_time)->format('g:i A')}} ~ {{\Carbon\Carbon::parse($booking->end_time)->format('g:i A')}}  
                                            <span class="badge badge-pill bg-info text-white"> {{$booking->type}} {{__('Meeting')}} </span>
                                        </p>
                                    </div>  
                               </div>                                    
                               @if ($extra)
                                <p class="text-danger mt-5">{{$extra->about}}</p>
                                @endif                                                                          
                            </div>                            
                        </div>
                    </div>                  
                </div>                       
                                                    
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="info-widget">
                                    <h4 class="card-title">{{__('Personal Information')}}</h4>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('First Name') }}</label>
                                                <input class="form-control" type="text" required name="fname" readonly value="{{$client->fname}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Last Name') }}</label>
                                                <input class="form-control" type="text" required name="lname" value="{{$client->lname}}" readonly>
                                            </div>
                                        </div>																																						
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Email') }}</label>
                                                <input class="form-control" type="email" required name="email" value="{{$client->email}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Phone') }}</label>
                                                <input class="form-control phone_number" type="text" required name="phone" value="{{$client->phone}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Whatsapp') }}</label>
                                                <input class="form-control phone_number" type="text" required name="whatsapp" value="{{$client->whatsapp}}" readonly>
                                            </div>
                                        </div>		
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Address') }}</label>
                                                <input class="form-control" type="text" required name="address" value="{{$client->address}}" readonly>
                                            </div>
                                        </div>											
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('File')}} ({{__('Optional') }})</label>
                                                <input class="form-control" type="file" name="file" style="padding-top:15px;">
                                                <small class="form-text text-muted">{{__('Allowed JPG, GIF or PNG. Max size of 2MB')}}</small>
                                            </div>
                                        </div>	
                                        @include ('alert')										
                                    </div>
                                </div>
                                <!-- /Personal Information -->		                               							
                            </div>									

                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="submit-section proceed-btn text-right">
                    @if ($extra)
                    <button class="booking-verify btn btn-primary submit-btn">{{__('Submit')}}</button>
                    @endif
                </div>                                        
            </div>
            </form>
        </div>
    </div>
</div>		
<!-- /Page Content -->
@endsection


@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>


<script>
$(document).ready(function(){    

    $('.phone_number').inputmask('+99-9999999999');
    $('.digits').inputmask('999999');
})
</script>
@endsection


@section('bottom-js')

@endsection
