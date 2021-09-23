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
        /* Customization Style of SyoTimer */
        .syotimer{
            text-align: center;
            margin: 30px auto 0;
            padding: 0 0 10px;
        }
        .syotimer-cell{
            display: inline-block;
            margin: 0 5px;

            width: 79px;
            background: url(../../client/assets/countdown/images/timer.png) no-repeat 0 0;
        }
        .syotimer-cell__value{
            font-size: 35px;
            color: lightgreen;

            height: 81px;
            line-height: 81px;

            margin: 0 0 5px;
        }
        .syotimer-cell__unit{
            font-family: Arial, serif;
            font-size: 12px;
            text-transform: uppercase;
        }

        #simple-timer .syotimer-cell_type_day,
        #periodic-timer_period_days .syotimer-cell_type_hour,
        #layout-timer_only-seconds .syotimer-cell_type_second,
        #layout-timer_mixed-units .syotimer-cell_type_minute{
            width: 120px;
            background-image: url(../../client/assets/countdown//images/timer_long.png);
        }        
    </style>
@endsection


@section('main-content')


<!-- Basic Information -->
<div class="card">
    <h4 class="card-header">{{__('Booking Detail')}}</h4>    
    <div class="card-body">
        @include ('alert')
        <div class="row">
            <div class="col-md-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>{{__('QRCode')}}</strong></td>
                                <td><img src="{{asset($booking->qrcode)}}"/></td>
                            </tr>
                            <tr><td><strong>{{__('Booking Status')}}</strong></td>
                                <td>
                                    @If ($booking->status == 'finished')
                                        <span class='badge badge-pill text-white bg-success badge-lg py-2 px-3'>Finished Meeting</span>
                                    @endif
                                    @If ($booking->status == 'approved')
                                        <span class='badge badge-pill text-white bg-info badge-large py-2 px-3'>Waiting Meeting</span>
                                    @endif
                                    @If ($booking->status == 'declined')
                                        <span class='badge badge-pill text-white bg-danger'>Declined</span>
                                    @endif  
                                </td>
                            </tr>                            
                            <tr><td><strong>{{__('Mission')}}</strong></td><td>{{$booking->mission->name}}<br>{{$booking->mission->name_ar}}</td></tr>
                            <tr><td><strong>{{__('Section')}}</strong></td><td>{{$booking->section->en_name}}<br>{{$booking->section->ar_name}}</td></tr>
                            <tr><td><strong>{{__('Client')}}</strong></td><td>{{$booking->client->fname. ' ' .$booking->client->lname}}</td></tr>
                            <tr><td><strong>{{__('Schedule Date')}}</strong></td><td>{{\Carbon\Carbon::parse($booking->schedule_date)->format('Y-m-d, l')}}</td></tr>
                            <tr><td><strong>{{__('Schedule Time')}}</strong></td><td>{{\Carbon\Carbon::parse($booking->start_time)->format('g:i A').'~'.\Carbon\Carbon::parse($booking->end_time)->format('g:i A')}}</td></tr>
                            <tr><td><strong>{{__('Meeting Type')}}</strong></td><td>{{$booking->type}} {{__('Meeting')}}</td></tr>
                            <tr><td><strong>{{__('File (Optional)')}}</strong></td>
                            <td>
                            <table class="table">
                            @foreach ($booking->files as $one) 
                                <tr>
                                    <td>{{ explode('/',$one->file)[count(explode('/',$one->file))-1]}}</td>
                                    <td>
                                        <img class="booking-file-viewer"  href="{{asset($one->file)}}" src="{{asset('client/assets/img/file-view.png')}}" title="" width="30px"> View                                         
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                            </td>
                            </tr>
                            <tr><td><strong>{{__('Issued Date')}}</strong></td><td>{{\Carbon\Carbon::parse($booking->created_at)->format('Y-m-d g:i A')}}</td></tr>
                            <tr><td><strong>{{__('Phone No')}}</strong></td><td>{{$booking->client->phone}} <span class="badge badge-pill text-white bg-success">Verified</span></td></tr>
                            <tr><td><strong>{{__('Whatsapp No')}}</strong></td><td>{{$booking->client->whatsapp}}</td></tr>
                            <tr><td><strong>{{__('Email')}}</strong> </td><td>{{$booking->client->email}}</td></tr>
                            <tr><td><strong>{{__('Address')}} </strong></td><td>{{$booking->client->address}}</td></tr>
                            <tr><td><strong>{{__('Reason not choosing onsite meeting')}} </strong></td><td>{{$booking->reason}}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>           
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h5>{{__('Countdown Timer')}}</h5></div>
                    <div class="card-body">
                        <div class="form-group text-center">
                            @if ($booking->status =='declined')
                            <p class="text-danger">{{ __('This book appointment has already been declined.')}}</p>
                            @endif
                            @if ($booking->status =='finished')
                            <p class="text-success"> {{__('This book appointment has already been finished on') }} {{ date("Y-m-d H:i:s", strtotime($booking->finish_time))}}.</p>
                            @endif
                            @if ($booking->status =='approved' &&   strtotime($booking->schedule_date. ' ' .$booking->end_time) > strtotime(date('Y-m-d H:i:s')) )
                            <p class="text-info"> {{__('This book appointment will be opened after') }} </p>
                            <div id="simple-timer"></div>
                            @endif
                            @if ($booking->status=='approved' && strtotime($booking->schedule_date. ' ' .$booking->end_time) < strtotime(date('Y-m-d H:i:s')) ) 
                            <p class="text-danger"> {{__('This book appointment already expired') }}</p>
                            @endif                            
                            <p></p>
                        </div>                                                                                                                                                                                   
                    </div>
                </div>

                @if ($booking->status=='approved' && strtotime($booking->schedule_date. ' ' .$booking->end_time) > strtotime(date('Y-m-d H:i:s')) ) 
                <div class="card">
                    <div class="card-header">
                            <h5>{{__('Action')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            @if ($booking->type == 'Online' )
                                @if ($meeting )
                                <p class="text-success border-bottom">
                                    <strong class="text-info">{{__('Meeting Room ID')}}:</strong> 
                                    {{ $meeting->room_id }}
                                </p> 
                                <p class="text-success border-bottom">
                                    <strong class="text-info">{{__('Meeting Room URL')}}:</strong> 
                                    {{ url('/room/').'/'.base64_encode($meeting->room_id) }}
                                </p>                                 
                                @else
                                @if ($user->role=='consul')
                                <form method="post" action="{{url('/appointment/generate_meetin_url')}}">
                                    @csrf
                                    <input type="hidden" name="bk_id" value="{{$booking->id}}" />
                                    <input type="hidden" name="c_id" value="{{$booking->client->id}}" />
                                    <button class="btn btn-primary btn-block">{{__('Generate Meeting ROOM ID')}}</button>
                                </form>
                                @endif
                                @endif
                            @endif
                        </div>
                        <div class="form-group">
                            
                            @if ($booking->status == 'declined' )
                            <p class="text-danger">{{__('Meeting Declined.')}}</p>
                            @elseif ($booking->status == 'finished')
                            <p class="text-success">{{__('Meeting finished successfully.')}}</p>
                            @else
                                    @if ($booking->type == 'Online' && $user->role == 'consul')
                                    <a class="btn btn-success btn-block" href="#open_meeting_modal" data-toggle="modal">{{__('Open Meeting')}}</a>
                                    @endif
                                    @if ($meeting && $user->role == 'consul')
                                    <a class="btn btn-info btn-block" href="#finish_meeting_modal" data-toggle="modal">{{__('Finish Meeting Manually')}}</a>
                                    @endif
                                    <a class="btn btn-danger btn-block" href="#decline_modal" data-toggle="modal">{{__('Decline')}}</a>
                            @endif
                        </div>                                                                                                                                                                                   
                    </div>
                </div>
                @endif

                @if ($booking->status=='approved' && strtotime($booking->schedule_date. ' ' .$booking->end_time) > strtotime(date('Y-m-d H:i:s')) ) 
                <div class="card">
                    <div class="card-header"><h5>{{__('Extra Requested Links')}}</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <table class="table table-bordered">
                                <tbody>
                                @foreach ($extra_links as $one)
                                <tr>
                                    <td style="word-break:break-all">{{ url($one->url) }}</td>
                                    <td>
                                        @if ($one->status == 'done') 
                                            <a class="text-success"><i class="fa fa-check"></i></a>
                                        @else
                                            <a class="text-danger"><i class="fa fa-minus"></i></a>
                                        @endif
                                    </td>
                                </tr>                                
                                @endforeach
                                <tr>
                                    <td colspan="2">
                                        <a class="btn bg-info-light btn-block" href="#ask_file_modal" data-toggle="modal"><i class="fa fa-plus mr-2"></i>{{__('Generate Extra Link')}}</a>
                                    </td>
                                </tr>                             
                                </tbody>
                            </table>
                        </div>                                                                                                                                                                                   
                    </div>
                </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h5>{{__('Sending Email')}}</h5></div>
                    <div class="card-body">
                    <form action="{{url('/sendsingleemail')}}" method="Post">
                        @csrf                        
                        <div class="form-group">
                            <label>{{__('Receiver')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_email" value="{{$booking->client->email}}" readonly />
                        </div>                            
                        <div class="form-group">
                            <label>{{__('Subject')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="subject" required />
                        </div>                                                    
                        <div class="form-group">
                            <label>{{__('Content')}} <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="about" rows="3" required></textarea>
                        </div>   
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-sm" value="{{__('Send')}}" />
                        </div>     
                        </form>                                                                                                                                                
                    </div>
                </div>                
                <div class="card">
                    <div class="card-header"><h5>{{__('Sending SMS')}}</h5></div>
                    <div class="card-body">
                        <form action="{{url('/sendsinglesms')}}" method="Post">
                        @csrf
                        <div class="form-group">
                            <label>{{__('Receiver Phone. No')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_phone" value="{{$booking->client->phone}}" readonly />
                        </div>                            
                        <div class="form-group">
                            <label>{{__('Message')}} <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="message" rows="3" required></textarea>
                        </div>   
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-sm" value="{{__('Send')}}" />
                        </div> 
                        </form>                                                                                                                                                    
                    </div>
                </div>            
                <div class="card">
                    <div class="card-header"><h5>{{__('Sending Whatsapp SMS')}}</h5></div>
                    <div class="card-body">
                    <form action="{{url('/sendwhatsappsms')}}" method="Post">
                        @csrf
                        <div class="form-group">
                            <label>{{__('Receiver Whatsapp. No')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_phone" value="{{$booking->client->whatsapp}}" readonly />
                        </div>                            
                        <div class="form-group">
                            <label>{{__('Message')}} <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="message" rows="3" required></textarea>
                        </div>   
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-sm" value="{{__('Send')}}" />
                        </div>           
                    </form>                                                                                                                                          
                    </div>
                </div>                            
            </div>
        </div>
    </div>
</div>
<!-- /Basic Information -->		
<!-- ASK confirmation Modal -->
<div class="modal fade" id="ask_file_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-left">
                <div class="form-content p-2">
                    <form action="{{url('appointments/askfile')}}" method="POST">
                        @csrf
                        <input type="hidden" name="bk_id" value="{{$booking->id}}" />
                        <p class="mb-4">{{__('Are you sure want to ask extra somethings ?')}}</p>											
                        <label class="text-left">{{__('About')}} <span class="text-danger">*</span></label>
                        <textarea class="form-control mb-3" name="about" rows="3" required></textarea>
                        <label class="text-left">{{__('Send via')}} <span class="text-danger">*</span></label>
                        <select class="form-control mb-3" name="via">
                            <option value="email">{{__('Email') }}</option>
                            <option value="sms">{{__('SMS') }}</option>
                            <option value="whatsapp">{{__('Whatsapp') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Sure') }}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->

<!-- ASK confirmation Modal -->
<div class="modal fade" id="decline_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <form action="{{url('appointments/decline')}}" method="POST">
                        @csrf
                        <input type="hidden" name="bk_id" value="{{$booking->id}}" />                        
                        <p class="mb-4">{{ __('Are you sure want to decline this booking appointment ?') }}</p>											
                        <div class="form-group text-left">
                            <label>Decline Reason</label>
                            <textarea class="form-control" rows="3" name="reason"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Sure') }} </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->


<!-- ASK confirmation Modal -->
<div class="modal fade" id="open_meeting_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <form action="{{url('meetings/room/'.$booking->schedule_date)}}" method="GET">
                        @csrf
                        <input type="hidden" name="st" value="{{$booking->start_time}}" />
                        <input type="hidden" name="dt" value="{{$booking->end_time}}" />
                        <p class="mb-4">{{ __('Are you sure want to open meeting with') }} <strong>{{$booking->client->fname. ' '. $booking->client->lname}}</strong>{{' now?'}}</p>											
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Sure') }} </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->


@if ($meeting)
<!-- ASK confirmation Modal -->
<div class="modal fade" id="finish_meeting_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <form action="{{url('/endmeeting')}}" method="POST">
                        @csrf
                        <input type="hidden" name="room_id" value="{{$meeting->room_id}}" />
                        <p class="mb-4">{{ __('Are you sure want to finsish meeting with') }} <strong>{{$booking->client->fname. ' '. $booking->client->lname}}</strong>{{' now?'}}</p>											
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Sure') }} </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- /SMS Modal -->
@endsection

@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{asset('client/assets/countdown/js/jquery.syotimer.js') }}"></script>
    <script src="{{asset('client/assets/ezview/EZView.js') }}"></script>
    <script src="{{asset('client/assets/ezview/draggable.js') }}"></script>

    <script src="{{asset('client/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>    
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="{{asset('client/assets/datepicker/locales/bootstrap-datepicker.hi.min.js') }}"></script>
    @endif
    <script>
      $(document).ready(function() {
        /* Simple Timer. The countdown to 20:30 2035.05.09 */
        <?php if ($booking->status == 'approved') { ?>
        $('#simple-timer').syotimer({
            year: {{\Carbon\Carbon::parse($booking->schedule_date)->year}},
            month: {{\Carbon\Carbon::parse($booking->schedule_date)->month}},
            day: {{\Carbon\Carbon::parse($booking->schedule_date)->day}},
            hour: {{\Carbon\Carbon::parse($booking->start_time)->hour}},
            minute: {{\Carbon\Carbon::parse($booking->start_time)->minute}},
        });
        <?php } ?>
        $('.booking-file-viewer').EZView();

    });
    </script>

@endsection

@section('bottom-js')

@endsection