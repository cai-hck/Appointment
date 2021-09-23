@extends ('layouts.client.main')

@section('page-css')


@endsection


@section('main-content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">							
                <h2 class="breadcrumb-title">{{__('Booking - Success')}}</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content success-page-cont">
    <div class="container-fluid">
    
        <div class="row justify-content-center">
            <div class="col-lg-6">
            
                <!-- Success Card -->
                <div class="card success-card">
                    <div class="card-body">
                        <div class="success-cont">
                            <i class="fas fa-check"></i>
                            <h4>{{__('Appointment booked Successfully!') }}</h4>
                            @if (app()->getLocale() == 'en')
                            <h3>{{$booking->mission->name}}</h3>
                            @else
                            <h3>{{$booking->mission->name_ar}}</h3>
                            @endif
                            @If (app()->getLocale() == 'en')
                            <h3>{{$booking->section->en_name}}</h3>
                            @else
                            <h3>{{$booking->section->ar_name}}</h3>
                            @endif
                            <p>Appointment booked with <strong>{{$booking->type}} {{__('Meeting on')}}</strong><br>  
                            <strong>{{\Carbon\Carbon::parse($booking->schedule_date)->format('Y-m-d , l ')}}  {{\Carbon\Carbon::parse($booking->start_time)->format('g:i A')}} {{__('to')}} {{\Carbon\Carbon::parse($booking->end_time)->format('g:i A')}}</strong></p>

                            <p class="text-danger">{{__('*You can not book another appointments in 1 week. Wait until you get approvals from Consultant')}}</p>
                            <a href="{{url('/')}}" class="btn btn-primary view-inv-btn">{{__('Go Home Page')}}</a>
                        </div>
                    </div>
                </div>
                <!-- /Success Card -->
                
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