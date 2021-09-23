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
@endsection


@section('main-content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
        @include ('alert')
        <!-- Page Header -->
        <div class="page-header">
            <div class="card">                    
                <div class="card-body">
                    <form action="#" method="GET" autocomplete="off">
                    @csrf
                    <div class="row py-2 px-3">
                        <div class="col-sm-3  mb-1 text-center"><label class="center-element">{{__('Choose a Date Range')}}<span class="text-danger">*</span></label></div>
                        <div class="col-sm-3  mb-1"><input id="datepicker_start" class="form-control center-element" name="date_start" value="{{$start}}" /></div>
                        <div class="col-sm-3  mb-1"><input id="datepicker_end" class="form-control center-element" name="date_end" value="{{$end}}" /></div>
                        <div class="col-sm-3 text-center mb-1"><button class="btn btn-info  center-element"><i class="fa fa-search "></i> {{__('View Bookings') }}</button></div>                                                
                    </div>
                    </form>
                </div>
            </div>    
        </div>

        <div class="row">                   
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-info border-info">
                                <i class="fa fa-book"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{count($bookings)}}</h3>
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
                            <span class="dash-widget-icon text-success border-warning">
                                <i class="fa fa-book"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$finished_bookings}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('Finished')}} {{__('Appointments')}}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning border-warning">
                                <i class="fa fa-book"></i>
                            </span>
                            <div class="dash-count">
                            <h3>{{ count($bookings) - $finished_bookings - $declined_bookings}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted"> {{__('Upcoming')}} {{__('Appointments')}} </h6>
                        </div>
                    </div>
                </div>
            </div>       
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-primary">
                                <i class="fa fa-check-circle"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$today_bookings}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('Today')}} {{__('Appointments')}}</h6>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <div class="card">
            <div class="card-header"><strong>{{__('Appointments List') }}</strong></div>        
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>{{__('Issued Date') }}</th>
                                <th>{{__('Client') }}</th>
                                <th>{{__('Section') }}</th>
                                <th>{{__('Schedule Date') }}</th>
                                <th>{{__('Schedule Time') }}</th>
                                <th>{{__('Meeting Type') }}</th>
                                <th>{{__('Status') }}</th>
                                <th class="text-right">{{__('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $one)
                            <tr>
                                <td>{{\Carbon\Carbon::parse($one->created_at)->format('Y-m-d g:i A')}}</td>
                                <td>{{$one->client->fname. ' ' .$one->client->lname}} </td>
                                <td>
                                    {{$one->section->en_name}} 
                                    <br>
                                    {{$one->section->ar_name}}
                                </td>
                                <td>{{$one->schedule_date}}</td>
                                <td>{{\Carbon\Carbon::parse($one->start_time)->format('g:i A'). ' ~ ' . \Carbon\Carbon::parse($one->end_time)->format('g:i A')}}</td>
                                <td>
                                    <span class="badge badge-pill text-white bg-{{$one->type=='Onsite'?'primary':'info'}}">{{$one->type}} {{__('Meeting') }}</span></td>
                                <td>
                                    @if ($one->status=='finished') <a class="btn bg-success-light btn-sm btn-rounded"> {{__('Finished') }} </a> @endif
                                    @if ($one->status=='approved'  && strtotime($one->schedule_date. ' ' .$one->end_time) > strtotime(date('Y-m-d H:i:s'))) <a class="btn bg-info-light btn-sm btn-rounded"> {{__('Upcoming')}} </a> @endif
                                    @if ($one->status=='declined') <a class="btn bg-danger-light btn-sm btn-rounded"> {{__('Declined') }} </a> @endif
                                    @if ($one->status=='approved' && strtotime($one->schedule_date. ' ' .$one->end_time) < strtotime(date('Y-m-d H:i:s')) ) 
                                    <a class="btn bg-danger-light btn-sm btn-rounded"> {{__('Expired') }}
                                    @endif
                                </td>
                                <td>
                                    <div class="table-action">
                                        <a href="{{url('/appointments/viewbooking/'.$one->id)}}" class="btn btn-sm bg-info-light">
                                            <i class="far fa-eye"></i> {{__('View Detail')}}
                                        </a>																																							
<!--                                    <a data-toggle="modal"  href="#delete_modal" data-uid="" class="btn btn-sm bg-danger-light open-del-modal">
                                            <i class="fas fa-times"></i> {{__('Decline')}}
                                        </a> 
-->
                                    </div>
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
    <script>
        var lang = "<?php echo app()->getLocale() == 'ar'? 'hi':''?>";
        var ok_lang = "<?php echo app()->getLocale() == 'ar'? 'موافق':'OK'?>";
        $('#datepicker_single').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_start').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_end').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_reschedule').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
    </script>

@endsection

@section('bottom-js')

@endsection