@extends ('layouts.client.main')

@section('page-css')
    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{asset('client/assets/'. app()->getLocale().'/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{asset('client/assets/datepicker/css/bootstrap-datepicker3.css') }}">

@endsection


@section('main-content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('Booking') }} - {{ __('Choose Date & Time') }}</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">
    
        <div class="row">
            <div class="col-md-6">
                
                <div class="card">
                    <div class="card-body">
                        <div class="booking-doc-info" style="justify-content: space-between;align-items: baseline;">
                            <div class="booking-info w-100">                           
                                <h4><a href="javascript:void(0)">
                                    @if(app()->getLocale() == 'en')
                                    {{$mission->name}}
                                    @else
                                    {{$mission->name_ar}}
                                    @endif
                                </a>
                                </h4>
                                <h4 class="mt-3"><a href="javascript:void(0)" class="view-section">
                                @if(app()->getLocale() == 'en')
                                    {{$section->en_name}}
                                    @else
                                    {{$section->ar_name}}
                                    @endif
                                </a>
                                <span class="badge badge-success"> <i class="fa fa-check "></i> {{__('Confirmed')}}</span></h4>
                            </div>
                            
                        </div>                                    
                    </div>
                    <div class="card-body mt-0">
                        <div class="section-info">
                            <div class="form-group ">
                                @if (app()->getLocale() == 'en')
                                <?php echo $section->en_about ?>
                                @else
                                <?php echo $section->ar_about ?>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="col-md-6">             
                <div class="card">
                    <div class="card-body">
                    <div class="col-12">
                        @if (app()->getLocale() == 'en')
                        <h4 class="mb-1">{{\Carbon\Carbon::parse($sel_date)->format('Y-m-d ')}}</h4>
                        @else
                        <h4 class="mb-1">{{\Carbon\Carbon::parse($sel_date)->format('Y-m-d ')}}</h4>
                        @endif
                    </div>
                    <form action="#" method="GET">                   
                        @csrf
                        <label> {{ __('Choose Schedule Date')}} <span class="text-danger">*</span></label>
                        <input id="datepicker" class="form-control" name="date" value="{{$sel_date}}">
                        <button class="btn btn-sm btn-success mt-2 btn-block"> <i class="fa fa-check"></i> {{ __('Check Schedules') }}</button>
                    </form>
                    </div>
                    <div class="card-body">
                        @include ('alert')
                        <h4>{{ __('Timing Schedules') }}</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{__('Time') }}</th>
                                    <th>{{__('Current Slots') }}</th>
                                    <th>{{__('Type') }}</th>
                                    <th></th>
                                </tr>                                
                            </thead>
                            <tbody>
                                @foreach ($onsite as $one)
                                <tr>     
                                    <td>{{$one['start']}}~{{$one['end']}}</td>                                                                   
                                    <td>{{$one['slots']}}</td>
                                    <td><span class="badge badge-pill bg-warning text-white"> {{__('Onsite Meeting') }} </span></td>
                                    <td class="text-center">
                                        <input type="checkbox"  data-index="{{$one['index']}}" class="checkbox-meeting"/>
                                    </td>
                                </tr>
                                @endforeach 
                                @foreach ($online as $one)
                                <tr>     
                                    <td>{{$one['start']}}~{{$one['end']}}</td>                                                                   
                                    <td>{{$one['slots']}}</td>
                                    <td><span class="badge badge-pill bg-info text-white"> {{__('Online Meeting') }}</span></td>
                                    <td  class="text-center">
                                        <input type="checkbox" data-index="{{$one['index']}}"  class="checkbox-meeting"/>
                                    </td>
                                </tr>
                                @endforeach        
                                @if (!$holiday && count($online) == 0 && count($onsite) == 0) 
                                <tr>
                                    <td colspan="3">{{ __('No Schedules') }}</td>
                                </tr>                                
                                @endif
                                @if ($holiday) 
                                <tr>
                                    <td colspan="3">{{__('Holiday, No Schedules')}}</td>
                                </tr>                                
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div> 

                <!-- /Schedule Widget -->
                
                @if (count($online) != 0 || count($onsite) != 0 && !$holiday )
                <!-- Submit Section -->
                <div class="submit-section proceed-btn text-right">
                    <form method="get" action="{{url('bookinginfo')}}">
                        @csrf
                    <input type="hidden" name="D" value="{{$sel_date}}" />
                    <input type="hidden" name="MV" id="meeting_value" />
                    <input type="hidden" name="SID" value="{{$section->id}}" />
                    <button class="btn btn-primary submit-btn">{{ __('Next Step')}} </button>
                    </form>
                </div>

                @endif
                <!-- /Submit Section -->
                
            </div>
        </div>
    </div>

</div>		
<!-- /Page Content -->


@endsection


@section('page-js')
		<!-- Daterangepikcer JS -->
		<script src="{{asset('client/assets/'.app()->getLocale() .'/js/moment.min.js') }}"></script>
        <script src="{{asset('client/assets/'.app()->getLocale() .'/plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{asset('client/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>
        @if (app()->getLocale() == 'ar')
            <script type="text/javascript" src="{{asset('client/assets/datepicker/locales/bootstrap-datepicker.ar.min.js') }}"></script>
        @endif
        <script>
            $(document).ready(function(){
                var lang = "<?php echo app()->getLocale() == 'ar'? 'ar':''?>";
                $('#datepicker').datepicker({
                    todayBtn:true,                    
                    language: lang,
                    format: "yyyy-mm-dd"
                });
                $('.checkbox-meeting').change(function () {
                    if ($(this).is(':checked')) {
                        $('.checkbox-meeting').not(this).prop('checked',false);
                        $('#meeting_value').val($(this).attr('data-index'));
                    };
                });
            })
        </script>
@endsection


@section('bottom-js')

@endsection
