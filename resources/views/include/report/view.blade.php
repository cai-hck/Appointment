<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)

@section('page-css')
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.css') }}">

@endsection


@section('main-content')

<div class="doc-review review-listing">
    <div class="">
        <form action="{{url('/reports')}}" method="GET">
            @csrf
            <div class="reportrange btn btn-white mb-3">
                <i class="fa fa-calendar mr-2"></i>
                <span></span>
                <i class="fa fa-chevron-down ml-2"></i>
            </div>
            <input type="hidden" name="start_date" id="start_date" value="{{$start==''?date('Y-m-d'):$start}}"/>
            <input type="hidden" name="end_date" id="end_date" value="{{$end==''?date('Y-m-d'):$end}}"/>
            <button class="btn btn-primary mb-3"><i class="fa fa-search mr-1"></i>{{ __('Report') }}</button>
        </form>
    </div>
                                

    <div class="card">
        <div class="card-body pt-0">
        
            <!-- Tab Menu -->
            <nav class="user-tabs mb-4">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" href="#pat_appointments" data-toggle="tab">{{ __('Appointments') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pat_client" data-toggle="tab"><span class="med-records">{{ __('Clients') }}</span></a>
                    </li>
                </ul>
            </nav>
            <!-- /Tab Menu -->
            
            <!-- Tab Content -->
            <div class="tab-content pt-0">
                
                <!-- Appointment Tab -->
                <div id="pat_appointments" class="tab-pane fade show active">
                    <h4>{{ __('Today') }} </h4>
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{__('Appointments') }}</th>
                                            <th>{{__('Finished')}}</th>
                                            <th>{{__('Upcoming')}}</th>
                                            <th>{{__('Declined')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$today_bookings['all']}}</td>
                                            <td class="text-success">{{$today_bookings['finished']}}</td>
                                            <td class="text-info">{{$today_bookings['upcoming']}}</td>
                                            <td class="text-danger">{{$today_bookings['declined']}}</td>
                                        </tr>                                                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4"></div>
                    <h4>{{$start==''?date('Y-m-d'):$start}} ~ {{$end==''?date('Y-m-d'):$end}} </h4>
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{__('Appointments') }}</th>
                                            <th>{{__('Finished')}}</th>
                                            <th>{{__('Upcoming')}}</th>
                                            <th>{{__('Declined')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$bookings['all']}}</td>
                                            <td class="text-success">{{$bookings['finished']}}</td>
                                            <td class="text-info">{{$bookings['upcoming']}}</td>
                                            <td class="text-danger">{{$bookings['declined']}}</td>
                                        </tr>                                                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /Appointment Tab -->
                                                                
                <!-- Medical Records Tab -->
                <div id="pat_client" class="tab-pane fade">

                    <h4>{{ __('Today') }} </h4>
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{__('All') }}</th>
                                            <th>{{__('Onsite Meeting') }}</th>
                                            <th>{{__('Online Meeting') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$today_clients['all']}}</td>
                                            <td class="text-success">{{$today_clients['onsite']}}</td>
                                            <td class="text-info">{{$today_clients['online']}}</td>
                                        </tr>                                                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4"></div>
                    <h4>{{$start==''?date('Y-m-d'):$start}} ~ {{$end==''?date('Y-m-d'):$end}}  </h4>
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{__('All') }}</th>
                                                <th>{{__('Onsite Meeting') }}</th>
                                                <th>{{__('Online Meeting') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$clients['all']}}</td>
                                                <td class="text-success">{{$clients['onsite']}}</td>
                                                <td class="text-info">{{$clients['online']}}</td>
                                            </tr>                                                                    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /Medical Records Tab -->
            </div>
            <!-- Tab Content -->
            
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
	// Date Range Picker
	if($('.reportrange').length > 0) {
		var start = moment($('#start_date').val());
		var end = moment($('#end_date').val());

		function booking_range(start, end) {
            $('.reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
		}

		$('.reportrange').daterangepicker({
			startDate: start,
			endDate: end,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, booking_range);

		booking_range(start, end);
	}        
    </script>
@endsection


@section('bottom-js')

@endsection