@extends ('layouts.client.main')

@section('page-css')


@endsection


@section('main-content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('Booking - Enter Personal Info') }}</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
            
<!-- Page Content -->
<div class="content">
    <div class="container">
    
        <div class="row">
            <form method="POST" action="{{url('booksubmit')}}"  autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="mission" value="{{$mission->id}}" />
            <input type="hidden" name="section" value="{{$section->id}}" />
            <input type="hidden" name="sch_date" value="{{$sel_date}}" />
            <input type="hidden" name="schedule" value="{{$meeting}}" />
            <div class="col-12">         
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="booking-doc-info" style="justify-content: space-between;align-items: baseline;">
                                    <div class="booking-info w-100">
                                        <h4><a href="doctor-profile.html">{{$mission->name}}</a></h4>
                                        <h4 class="mt-3"><a href="#" class="view-section">{{$section->en_name}}</a> <span class="badge badge-primary"><i class="fa fa-check"></i>  {{__('Confirmed')}}</span></h4>
                                    </div>
                                    <div class="booking-info w-100">
                                        <h4 class="mt-3">{{__('Scheduled Time')}}
                                        <span class="text-right float-right">
                                            <a href="{{url('/booking/'.base64_encode($mission->id).'/appointment/'.base64_encode($section->id)).'?date='.$sel_date}}"><i class="fa fa-edit"></i></a></span>
                                        </h4>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar"></i> {{$sel_date}} , {{\Carbon\Carbon::parse($sel_date)->format('l')}}, {{$start}} ~ {{$end}}  
                                            <span class="badge badge-pill bg-info text-white"> {{$meeting_type}} </span>
                                        </p>
                                    </div>                                    
                                </div>                                    
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
                                                <input class="form-control" type="text" required name="fname">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Last Name') }}</label>
                                                <input class="form-control" type="text" required name="lname">
                                            </div>
                                        </div>																																						
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Email') }}</label>
                                                <input class="form-control" type="email" required name="email">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Phone') }}</label>
                                                <input class="form-control phone_number" type="text" required name="phone">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Whatsapp') }}</label>
                                                <input class="form-control phone_number" type="text" required name="whatsapp">
                                            </div>
                                        </div>		
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('Address') }}</label>
                                                <input class="form-control" type="text" required name="address">
                                            </div>
                                        </div>											
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('File')}} ({{__('Optional') }})</label>
                                                <input class="form-control" type="file" name="file" style="padding-top:15px;">
                                                <small class="form-text text-muted">{{__('Allowed JPG, GIF or PNG. Max size of 2MB')}}</small>
                                            </div>
                                        </div>											
                                    </div>
                                </div>
                                <!-- /Personal Information -->		                               							
                            </div>									

                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="submit-section proceed-btn text-right">
                    <a href="javascript:void(0)" class="booking-verify btn btn-primary submit-btn">{{__('Verify Phone')}}</a>
                </div>
                <!-- /Submit Section -->
                <div class="row final-step" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>{{ __('Sent Verification code to your phone!') }}</strong> 
                                    <a href="javascript:void(0)" id="resend_code" class="alert-link btn btn-sm btn-primary">{{__('Resend') }}</a>.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>		

                                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-sms" style="display:none">
                                    <strong>{{__('Error!')}}</strong> {{__('Sending SMS has been failed. Please check phone number.') }}
                                </div>	


                                <div class="info-widget">
                                    <h4 class="card-title">{{__('Verification') }}</h4>
                                    <div class="row">												
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>{{__('6 Digital Code') }}</label>
                                                <input class="form-control digits" name="verify_code" type="text">
                                            </div>
                                        </div>											
                                    </div>
                                    <!-- Submit Section -->
                                    <div class="submit-section proceed-btn text-center">
                                        <button class="btn btn-primary submit-btn">{{ __('Book Appointment') }}</button>
                                    </div>
                                    <!-- /Submit Section -->
                                </div>
                            </div>									
                        </div>
                    </div>				
                </div>

                
                
            </div>
            </form>
        </div>
    </div>
</div>		
<!-- /Page Content -->


<div class="modal fade" id="error_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">						
            <div class="modal-body">
                <div class="form-content p-2">
                    <input type="hidden" name="u_id" id="u_id" />
                    <h4 class="modal-title">{{__('Error')}}</h4>
                    <p class="mb-4">{{__('Phone Number is required to verify.')}}</p>											
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('OK, Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>


<script>
$(document).ready(function(){    

    $('.phone_number').inputmask('+99999999999999');
    $('.digits').inputmask('999999');


    $('.booking-verify ').click(function(){
        if ($('input[name=phone]').val()=='') {
            $('#error_modal').modal('show');
        }
        else {

            $.ajax({
                url:"{{url('verifyphone')}}",
                data: {_token:"{{csrf_token()}}",phone:$('input[name=phone]').val()},
                type:'post',
                success:function(data){                    
                    if (data == 'fail') {
                        $('#error-sms').show();
                    } 
                    if ( $( ".final-step" ).is( ":hidden" ) ) {
                        $( ".final-step" ).slideDown( "slow" );
                    } else {
                        $( ".final-step" ).hide();
                    }
                },
                failure:function() {

                }

            });
           
        }
    });

    $('#resend_code').click(function(){
        $('#error-sms').hide();
        $.ajax({
            url:"{{url('verifyphone')}}",
            data: {_token:"{{csrf_token()}}",phone:$('input[name=phone]').val()},
            type:'post',
            success:function(data){                    
                if (data == 'fail') {
                    $('#error-sms').show();
                }             
            },
            failure:function() {

            }
        });
    })
})
</script>
@endsection


@section('bottom-js')

@endsection
