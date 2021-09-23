<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)

@section('page-css')
<link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{asset('client/assets/datepicker/css/bootstrap-datepicker3.css') }}"> 
<link rel="stylesheet" href="{{asset('client/assets/timeline/sample1.css') }}"> 
<style>


</style>
@endsection

@section('main-content')

<div class="card">
<div class="card-body">
<div class="doc-review review-listing">
    <div class="">
        <form action="#" method="GET" autocomplete="off">
        @csrf
        <div class="row py-2 px-3">
            <div class="col-sm-3  mb-1 text-center"><label class="center-element">{{__('Choose a Date Range')}}<span class="text-danger">*</span></label></div>
            <div class="col-sm-3  mb-1"><input id="datepicker_start" class="form-control center-element" name="date_start" placeholder="From" value="{{$start}}" /></div>
            <div class="col-sm-3  mb-1"><input id="datepicker_end" class="form-control center-element" name="date_end" placeholder="To" value="{{$end}}" /></div>
            <div class="col-sm-3 text-center mb-1"><button class="btn btn-info  center-element"><i class="fa fa-search "></i> {{__('View Meetings') }}</button></div>                                                
        </div>
        </form>
    </div>
    

    <div class="container-fluid" style="min-height:500px;max-height:1200px;overflow:auto;">
        <div class="timeline">
            <div class="timeline-month">
            @if ($start !='' && $end != '')
                {{$start}} ~ {{$end}}
            @else
                {{'Today'}},{{\Carbon\Carbon::now()->format('Y-m-d, l')}}
            @endif
            </div>           
            @foreach ($meetings as $one)
            @if ($one['is_meetingday'])
            <div class="timeline-section">
                <div class="timeline-date">
                    {{ \Carbon\Carbon::parse($one['schedule']->date)->format('Y-m-d, l')}}
                </div>
                <div class="row">
                    @foreach ($one['slots'] as $slot_one)
                    @if (count($slot_one['clients']))
                    <div class="col-sm-4">
                        <div class="timeline-box">
                            <div class="box-title">
                                <i class="fa fa-asterisk text-success" aria-hidden="true"></i>
                                {{$slot_one['range']}}
                                <span class="text-right float-right"><small class="badge badge-pill bg-success text-white">{{$slot_one['type']}}</small></span>
                            </div>
                            <div class="box-content text-black">
                                <div class="d-flex" style="flex-wrap:wrap">
                                    @foreach ($slot_one['clients'] as $cli)
                                    <div class="people d-flex mt-1 mb-1 mr-1">
                                        <img class="avatar avatar-sm rounded-circle mr-1" alt="User Image" src="{{  asset('client/assets/img/client_avatar.png')  }} ">
                                        <div class="meta-data">
                                            <span class="comment-author text-muted">{{$cli->client->fname. ' '. $cli->client->lname}}</span>
                                            <br>
                                            @if ($cli->status == 'approved' && strtotime($cli->schedule_date. ' ' .$cli->end_time) <= strtotime(date('Y-m-d H:i:s')) )
                                            <a class="badge badge-danger text-white">{{__('Expired') }}</a>
                                            @endif
                                            @if ($cli->status == 'approved' && strtotime($cli->schedule_date. ' ' .$cli->end_time) > strtotime(date('Y-m-d H:i:s')))<a class="badge badge-warning text-white">{{__('Upcoming') }}</a>@endif
                                            @if ($cli->status == 'finished')<a class="badge badge-success text-white">{{__('Finished') }}</a>@endif
                                            @if ($cli->status == 'declined')<a class="badge badge-danger text-white">{{__('Declined') }}</a>@endif
                                        </div>														
                                    </div>  
                                    @endforeach
                                                                                                                                         
                                </div> 
                            </div>
                            <div class="box-footer"> <a href="{{url('meetings/room/'. $one['schedule']->date).'?st='.$slot_one['start'].'&dt='.$slot_one['end']}}" class="btn btn-sm bg-success btn-rounded text-white"><i class="fa fa-users"></i> {{__('Start Meeting') }}</a></div>
                        </div>
                    </div>     
                    @endif      
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>


</div>
</div>
</div>



@endsection

@section('page-js')
<script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
<script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{asset('client/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="{{asset('client/assets/datepicker/locales/bootstrap-datepicker.hi.min.js') }}"></script>
    @endif
    <script>
        var lang = "<?php echo app()->getLocale() == 'ar'? 'hi':''?>";
        var ok_lang = "<?php echo app()->getLocale() == 'ar'? 'موافق':'OK'?>";
        $('#datepicker_start').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
        $('#datepicker_end').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
    </script>
@endsection

@section('bottom-js')

@endsection