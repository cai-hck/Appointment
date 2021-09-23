<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.css')}}">		
    <link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{asset('client/assets/datepicker/css/bootstrap-datepicker3.css') }}">    
    <style>
        .dash-widget-header {
            align-items: center;
            display: flex;
            margin-bottom: 15px;
        }
        .dash-widget-icon {
            align-items: center;
            display: inline-flex;
            font-size: 1.875rem;
            height: 50px;
            justify-content: center;
            line-height: 48px;
            text-align: center;
            width: 50px;
            border: 3px solid;
            border-radius: 50px;
            padding: 28px;
        }     
        .dash-count {
            font-size: 18px;
            margin-left: auto;
        }       
    </style>
@endsection


@section('main-content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
        @include ('alert')
        <!-- Page Header -->
        <div class="page-header">
        <div class="row">                   
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-success">
                                <i class="fa fa-calendar-check"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$all_apps}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('All')}} {{__('Appointments')}} </h6>
                        </div>
                    </div>
                </div>
            </div>                      
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fa fa-calendar-check"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$today_apps}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('Today')}}, {{__('Appointments')}}</h6>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
        <div class="row">
            <div class="col-md-12">   
                <div class="card">          
                <div class="card-header"><strong>{{__('Appointments') }}</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>{{__('Schedule Date') }}</th>                               
                                    <th>{{__('Schedule Time') }}</th>                               
                                    <th>{{__('Meeting Type') }}</th>                               
                                    <th>{{__('Status') }}</th>                               
                                    <th></th>                               
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($bookings as $one)
                            <tr>
                                <td>{{$one->schedule_date}}</td>
                                <td>{{ \Carbon\Carbon::parse($one->start_time)->format('g:i A')}} ~ {{ \Carbon\Carbon::parse($one->end_time)->format('g:i A')}}</td>
                                <td>{{$one->type}} Meeting</td>
                                <td>
                                    @if ($one->status == 'approved' && strtotime($one->schedule_date. ' ' .$one->end_time) >= strtotime(date('Y-m-d H:i:s')))
                                    <a class="btn btn-sm bg-warning-light text-white">{{__('Upcoming')}} </a>
                                    @endif
                                    @if ($one->status == 'declined')
                                    <a class="btn btn-sm bg-danger-light text-white">{{__('Declined')}} </a>
                                    @endif                                    
                                    @if ($one->status == 'finished')
                                    <a class="btn btn-sm bg-success-light text-white">{{__('Finished')}} </a>
                                    @endif                                           
                                    @if ($one->status == 'approved' && strtotime($one->schedule_date. ' ' .$one->end_time) < strtotime(date('Y-m-d H:i:s')))
                                    <a class="btn btn-sm bg-danger-light text-white">{{__('Expired')}} </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($one->status == 'approved')
                                    <a class="btn btn-sm bg-info-light text-white" href="{{url('/appointments/viewbooking/'. $one->id) }}"> <i class="fa fa-eye"></i> View  </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{asset('client/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="{{asset('client/assets/datepicker/locales/bootstrap-datepicker.hi.min.js') }}"></script>
    @endif   
@endsection


@section('bottom-js')

@endsection