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
        <!--             <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('schedules')}}" class='form-group'>
                            @csrf
                            <div class="bookingrange btn btn-white mb-3">
                                <i class="fa fa-calendar mr-2"></i>
                                <span></span>
                                <i class="fa fa-chevron-down ml-2"></i>
                            </div>
                            <input type="hidden" name="start_date" id="start_date" />
                            <input type="hidden" name="end_date" id="end_date" />
                            <button class="btn btn-info mb-3"><i class="fa fa-search mr-1"></i>View</button>
                        </form>
                    </div>                 
            </div> -->
            <div class="row">                   
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-info border-info">
									<i class="fa fa-calendar"></i>
								</span>
								<div class="dash-count">
									<h3>{{count($schedules)}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('All Schedules Days')}} </h6>
							</div>
						</div>
                    </div>
                </div>
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
                                <span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-calendar-check"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> 0</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Finished Scedules Days')}}</h6>
							</div>
						</div>
                    </div>
                </div>
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-warning border-warning">
									<i class="fa fa-calendar"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{count($upcoming)}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Upcoming Schedules Days')}}</h6>
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
									<h3><i class="fa fa-dollar"></i> {{count($rescheduled)}} </h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Rescheduled Days')}}</h6>
							</div>
						</div>
                    </div>
                </div>       

                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-success">
									<i class="fa fa-gifts"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{count($holidays)}} </h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Holiday')}}</h6>
							</div>
						</div>
                    </div>
                </div>        
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-sitemap"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$today_onsite_meetings}} </h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Today')}} {{__('Onsite Meeting')}}</h6>
							</div>
						</div>
                    </div>
                </div>            
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-network-wired"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$today_online_meetings}} </h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Today')}} {{__('Online Meeting')}}</h6>
							</div>
						</div>
                    </div>
                </div>      
                <div class="col-sm-3 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-calendar-day"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$today_meetings}} </h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{__('Today')}} {{__('Meetings')}}</h6>
							</div>
						</div>
                    </div>
                </div>                                                            
            </div>            
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-12">           
                <div class="card">                    
                    <div class="card-body">
                        <form action="{{url('schedules/addsingle')}}" method="GET">
                        @csrf
                        <div class="row py-2 px-3">
                            <div class="col-sm-3 text-center mb-1"><label class="center-element">{{__('Choose a Date')}}<span class="text-danger">*</span></label></div>
                            <div class="col-sm-6  mb-1"><input id="datepicker_single" class="form-control center-element" name="date" value="{{date('Y-m-d')}}" /></div>
                            <div class="col-sm-3 text-center"><button class="btn btn-info  center-element"><i class="fa fa-plus "></i> {{__('Single Schedule')}}</button></div>                                                
                        </div>
                        <div class="row text-danger text-center"><span class="m-auto">*{{__('Single schedule can be done on only single date.')}}</span></div>
                        </form>
                    </div>
                </div>
                <div class="card">                    
                    <div class="card-body">
                        <form action="{{url('schedules/reschedulesingle')}}" method="GET">
                        @csrf
                        <div class="row py-2 px-3">
                            <div class="col-sm-3 text-center mb-1"><label class="center-element">{{__('Choose a Date')}}<span class="text-danger">*</span></label></div>
                            <div class="col-sm-6  mb-1"><input id="datepicker_reschedule" class="form-control center-element" name="date" value="{{date('Y-m-d')}}" /></div>
                            <div class="col-sm-3 text-center"><button class="btn btn-info  center-element"><i class="fa fa-plus "></i> {{__('Single Reschedule')}}</button></div>                                                
                        </div>
                        <div class="row text-danger text-center"><span class="m-auto">*{{__('Reschedule changes can be done on only single date.')}}</span></div>
                        </form>

                    </div>
                </div>                
                <div class="card">                    
                    <div class="card-body">
                        <form action="{{url('schedules/addrange')}}" method="GET">
                        @csrf
                        <div class="row py-2 px-3">
                            <div class="col-sm-3  mb-1 text-center"><label class="center-element">{{__('Choose a Date Range')}}<span class="text-danger">*</span></label></div>
                            <div class="col-sm-3  mb-1"><input id="datepicker_start" class="form-control center-element" name="date_start" value="{{date('Y-m-d')}}" /></div>
                            <div class="col-sm-3  mb-1"><input id="datepicker_end" class="form-control center-element" name="date_end" value="{{date('Y-m-d')}}" /></div>
                            <div class="col-sm-3 text-center mb-1"><button class="btn btn-info  center-element"><i class="fa fa-plus "></i> {{__('Range Schedule')}}</button></div>                                                
                        </div>
                        </form>
                    </div>
                </div>                

                <div class="card">         
                    <div class="card-header"><strong>{{__('Reset Schedules') }}</strong></div>
                    <div class="card-body">
                        <form action="{{url('schedules/removeschedule')}}" method="Post" id="reset_sch_form">
                        @csrf
                        <input type="hidden" name="reset_option" id="reset_option" value="0"/>
                        <div class="row py-2 px-3">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input id="datepicker_remove_start" class="form-control center-element" name="date_start" value="{{date('Y-m-d')}}" />
                                    </div>
                                    <div class="col-md-4">
                                        <input id="datepicker_remove_end" class="form-control center-element" name="date_end" value="{{date('Y-m-d')}}" />
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" id="range_remove" data-toggle="modal" data-target="#ask-reset-modal" class="btn btn-danger  center-element"><i class="fa fa-minus "></i> {{__('Reset selected Schedule')}}</a>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:void(0)" id="all_remove" data-toggle="modal" data-target="#ask-reset-modal" class="btn btn-danger  center-element"><i class="fa fa-minus "></i> {{__('Reset all Schedule')}}</a>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header"><strong>{{__('Main Schedules') }}</strong></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>{{__('Date') }}</th>
                                        <th>{{__('Weekday') }}</th>
                                        <th width="3%">{{__('Max Clients') }}</th>
                                        <th>{{__('Defalut/Custom') }}</th>
                                        <th class="text-center">{{__('Timing Schedules') }} <br> 
                                        <a href="javascrip:void(0)" class="badge badge-pill bg-primary text-white w-100">{{__('Onsite meeting') }}</a> 
                                        <a href="javascrip:void(0)" class="badge badge-pill bg-info text-white w-100">{{__('Online meeting') }}</a></th>
                                        <th>{{__('Created By') }}</th>
                                        <th>{{__('Creator') }}</th>
                                        <th>{{__('Rescheduled') }}?</th>
                                        <th class="text-right">{{__('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $one)
                                    <tr>
                                        <td>{{\Carbon\Carbon::parse($one->date)->format('Y-m-d')}}</td>
                                        <td>{{\Carbon\Carbon::parse($one->date)->format('l')}}</td>
                                        <td>{{$one->slots}}</td>
                                        <td>
                                            @if ($one->isDefault)
                                            <a class="btn bg-info-light btn-sm btn-rounded" > {{__('Default Timings') }}</a>
                                            @else
                                            <a class="btn bg-warning-light btn-sm btn-rounded" > {{__('Custom Timings') }}</a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($one->isHoliday)
                                            <a class="text-success text-center"><i class="fa fa-gifts"></i> {{__('Holiday') }} </a>
                                            @else
                                            <?php
                                                if (!$one->isDefault) {
                                                    $times = $one->timings!=''?json_decode($one->timings):'';
                                                    if ($times == '')
                                                        echo '<a class="text-danger text-center"> No schedules </a>';
                                                    else {
                                                        foreach ($times as $t_one) {
                                                            echo '<span class=" text-white w-100 badge badge-pill bg-'. ($t_one->t=='onsite'?'primary':'info') .'">' . \Carbon\Carbon::parse($t_one->s)->format('g:i A').'~'.  \Carbon\Carbon::parse($t_one->e)->format('g:i A')  .'</span>';
                                                        }
                                                    }
                                                } else {
                                                    $times = DB::table('schedule_timings')->where('weekday', $one->weekday)->get();
                                                    foreach ($times as $t_one) {
                                                        echo '<span class=" text-white w-100 badge badge-pill bg-'. ($t_one->type?'primary':'info') .'">' . \Carbon\Carbon::parse($t_one->start)->format('g:i A').'~'.  \Carbon\Carbon::parse($t_one->end)->format('g:i A')  .'</span>';
                                                    }
                                                }
                                            ?>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($one->user->role == 'consul')
                                            <a class="btn bg-info-light btn-sm btn-rounded" > {{__('Consultant') }} </a>
                                            @else
                                            <a class="btn bg-warning-light btn-sm btn-rounded" > {{__('Secretary') }} </a>
                                            @endif
                                        </td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded-circle" 
                                                    src="{{ asset(json_decode($one->user->userinfo->photo)->s) }}" alt="{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}">
                                                </a>
                                                <a href="#">{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}<span>@ {{$one->user->name}}</span></a>
                                            </h2>
                                        </td>
                                        <td>
                                            @if($one->isReschedule)
                                                <a class="btn bg-info-light btn-sm btn-rounded" > {{__('Rescheduled') }} </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-warning btn-sm text-white mb-1" href="{{url('schedules/edit?date='.$one->date)}}"> {{__('Edit') }} </a>
                                            <a class="btn btn-danger btn-sm text-white mb-1 del-sch-modal" href="javascript:void(0)" data-sdate="{{$one->date}}">   {{__('Remove')}} </a>
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
<!-- /Page Wrapper -->

<!-- ASK confirmation Modal -->
<div class="modal fade" id="del_sch_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <form action="{{url('schedules/delete')}}" method="POST" id="del-sch-form">
                    @csrf
                    <input type="hidden" name="s_date" id="s_date" />
                    <h4 class="modal-title">{{__('Delete')}}</h4>
                    <p class="mb-4">{{__('Are you sure want to delete?')}}</p>											
                    <button type="submit" class="btn btn-primary">{{__('Yes, Sure')}} </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->

<!-- ASK confirmation Modal -->
<div class="modal fade" id="ask-reset-modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <h4 class="modal-title">{{__('Delete')}}</h4>
                    <p class="mb-4">{{__('Are you sure want to delete?')}}</p>											
                    <a href="javascript:void(0);" id="reset_ok" type="submit" class="btn btn-primary">{{__('Yes, Sure')}} </a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->
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
        $('#datepicker_remove_start').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_remove_end').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_reschedule').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
    </script>
@endsection


@section('bottom-js')
<script>
$(document).ready(function(){
	$('.del-sch-modal').click(function(){        
        $("#del_sch_modal").modal('show');
        $('#del-sch-form').find('#s_date').val($(this).attr('data-sdate'));
	});
    $('#reset_ok').click(function(){
      $('#reset_sch_form').submit();  
    })
    $('#range_remove').click(function(){
        $('#reset_option').val(0);
    })
    $('#all_remove').click(function(){
        $('#reset_option').val(1);
    })    
})
</script>

@endsection
