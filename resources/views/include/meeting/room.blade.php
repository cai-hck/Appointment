<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)
@section('page-css')
<style>
    .chat-cont-left .chat-users-list div.media {
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 15px;
        transition: all 0.2s ease 0s;
    }
</style>
@endsection

@section('main-content')

<!-- Page Content -->
<div class="content" style="padding-top:0">
    <div class="row">
        <div class="col-xl-12">
            <div class="chat-window" style="min-height:800px;background:white">
            
                <!-- Chat Left -->
                <div class="chat-cont-left">
                    <div class="chat-header">
                        <span>{{__('Clients')}}</span>
                    </div>								
                    <div class="chat-users-list">
                        <div class="chat-scroll">

                            @foreach ($bookings as $one_booking)
                            <!-- 
                                Useful Class : read-chat active
                             -->
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-away">
                                        <img src="{{ asset('client/assets/img/client_avatar.png')}}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body d-flex align-center" style="justify-content:space-between;align-items:center">
                                    <div>
                                        <div class="user-name ml-3">{{$one_booking->client->fname. ' ' . $one_booking->client->lname}}</div>
                                        @if ($one_booking->chatroom)
                                        <div class="text-muted ml-3 text-italic">{{__('ID')}} <mark><i>#{{$one_booking->chatroom->room_id}}</i></mark></div>
                                        @else
                                        <div class="text-muted ml-3">
                                            <mark><i>{{__('Not assigned meeting room')}}. </i><a href="{{url('/appointments/viewbooking/'. $one_booking->id)}}"> {{__('Create Room')}}</a></mark>
                                        </div>
                                        @endif
                                    </div>
                                    <div>                           
                                        @if ($one_booking->chatroom)                                        
                                            @if ($one_booking->chatroom->status=='pending')                                                   
                                                @if ( strtotime($one_booking->schedule_date. ' ' .$one_booking->end_time) <= strtotime(date('Y-m-d H:i:s')) )
                                                <div class="badge badge-danger badge-pill py-2 px-2">
                                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Expired')}}</a>                                            
                                                </div>
                                                @else
                                                <div class="badge badge-success badge-pill py-2 px-2">
                                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Start Meeting')}}</a>                                            
                                                </div>
                                                @endif
                                            @elseif ($one_booking->chatroom->status=='finished')
                                            <div class="badge badge-success badge-pill py-2 px-2">
                                                <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Finished')}}</a>                                            
                                            </div>
                                            @elseif ($one_booking->chatroom->status=='air')
                                                @if ( strtotime($one_booking->schedule_date. ' ' .$one_booking->end_time) <= strtotime(date('Y-m-d H:i:s')) )
                                                <div class="badge badge-danger badge-pill py-2 px-2">
                                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Expired')}}</a>                                            
                                                </div>
                                                @else                                            
                                                <div class="badge badge-warning badge-pill py-2 px-2">
                                                    <a href="javascript:void(0)" class="text-white py-2 px-3 ml-2 mr-2">{{__('On Air')}}</a>                                            
                                                </div>
                                                @endif
                                            @endif
                                        @else
                                            @if ($one_booking->chatroom && strtotime($one_booking->schedule_date. ' ' .$one_booking->end_time) <= strtotime(date('Y-m-d H:i:s')) )
                                            <div class="badge badge-danger badge-pill py-2 px-2">
                                                <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Expired')}}</a>                                            
                                            </div>
                                            @endif
                                        @endif                                      
                                    </div>
                                </div>
                            </div>
                            @endforeach                         									
                        </div>
                    </div>
                </div>
                <!-- /Chat Left -->
            
                <!-- Chat Right -->
                <div class="chat-cont-right" id="app">
                    <div>

                        <div class="card-body text-center">
                            <img src="{{asset('client/assets/img/meeting-bg.svg')}}"/>
                        </div>
                       
                    </div>
                </div>
                <!-- /Chat Right -->    
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