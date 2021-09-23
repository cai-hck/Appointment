
<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.meeting.consul';
} elseif ($user->role == 'client') {
    $layout = 'layouts.meeting.client';
}
?>

@extends ($layout)
@section('page-css')
<style>


</style>
@endsection

@section('main-content')
<div class="col-xl-12" >
<div class="chat-window mt-5 mb-5" id="app">                               
    <!-- Chat Right -->
    @if ($user->role == 'consul')
    <!-- Chat Left -->
    <div class="chat-cont-left">
        <div class="chat-header">
            <span>{{__('Clients')}}</span>
        </div>								
        <div class="chat-users-list">
            <div class="chat-scroll">
                @foreach ($bookings as $one_booking)             
                <div class="media {{$one_booking->chatroom&&$one_booking->chatroom->room_id==$room?'bg-info-light':''}}">
                    <div class="media-img-wrap">
                        <div class="avatar avatar-away">
                            <img src="{{ asset('client/assets/img/client_avatar.png')}}" alt="User Image" class="avatar-img rounded-circle">
                        </div>
                    </div>
                    <div class="media-body d-flex align-center" style="justify-content:space-between;align-items:center">
                        <div>
                            <div class="user-name ml-3">{{$one_booking->client->fname. ' ' . $one_booking->client->lname}}</div>
                            @if ($one_booking->chatroom)
                            <div class="text-muted ml-3 text-italic">ID <mark><i>#{{$one_booking->chatroom->room_id}}</i></mark></div>
                            @else
                            <div class="text-muted ml-3"><mark><i>{{__('Not assigned meeting room') }}</i></mark></div>
                            @endif
                        </div>
                        <div>                           
                            @if ($one_booking->chatroom && $one_booking->chatroom->room_id!=$room)                                                   
                                @if ($one_booking->chatroom->status == 'pending')                            
                                <div class="badge badge-success badge-pill py-2 px-2">
                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Start Meeting')}}</a>                                            
                                </div>
                                @elseif ($one_booking->chatroom->status == 'air')
                                <div class="badge badge-warning badge-pill py-2 px-2">
                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Meeting')}}</a>                                            
                                </div>
                                @else
                                <div class="badge badge-success badge-pill py-2 px-2">
                                    <a href="javascript:void(0);" class="text-white py-2 px-3 ml-2 mr-2">{{__('Finished') }}</a>                                            
                                </div>
                                @endif
                            @endif
                            @if ($one_booking->chatroom && $one_booking->chatroom->room_id==$room)
                                @if ($chatroom->status == 'finished')
                                <div class="badge badge-success badge-pill py-2 px-2">                                            
                                    <a href="javascript:void(0);" class="text-white py-2 px-3 ml-2 mr-2">{{__('Finished') }}</a>                                            
                                </div>
                                @elseif ($chatroom->status == 'air')
                                <div class="badge badge-warning badge-pill py-2 px-2">                                            
                                    <a href="javascript:void(0);" class="text-white py-2 px-3 ml-2 mr-2">{{__('On Air') }}</a>                                            
                                </div>
                                @else
                                <div class="badge badge-danger badge-pill py-2 px-2">                                            
                                    <a href="{{url('open-meeting/'. $one_booking->chatroom->room_id )}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Open') }}</a>                                            
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
    @endif

    @if ($user->role=='client' && $chatroom->status == 'finished')
        <!-- Success Card -->
        <div class="card success-card m-auto ">
            <div class="card-body">
                <div class="success-cont">
                    <i class="fas fa-check"></i>
                    <h3 class="mb-5">{{__('Meeting Finished Successfully')}}</h3>                    
                    <a href="{{url('/')}}" class="btn btn-primary view-inv-btn">{{__('Home Page')}}</a>
                </div>
            </div>
        </div>
        <!-- /Success Card -->
    @else
    <div class="chat-cont-right m-auto">

        @if ($user->role=='consul' && $chatroom->status == 'air')
        <div class="card success-card m-auto ">
            <div class="card-body">
                <div class="success-cont">
                    <i class="fas fa-comments"></i>
                    <h3 class="mb-5">{{__('Meeting already started')}}</h3>                    
                </div>
            </div>
        </div>
        @elseif ($user->role=='consul' && $chatroom->status == 'finished')
        <!-- Success Card -->
        <div class="card success-card m-auto ">
            <div class="card-body">
                <div class="success-cont">
                    <i class="fas fa-check"></i>
                    <h3 class="mb-5">{{__('Meeting Finished Successfully')}}</h3>                    
                </div>
            </div>
        </div>
        <!-- /Success Card -->
        @else

        <chat-component 
            :user ="{{ $user }}"
            :bk-Id = "{{ $book }}" 
            :recepiant="{{$user->role=='client'?$recepiant->user->userinfo:$recepiant}}"
            :avatar-Image = "{{ $user->role=='client'?json_encode(asset(json_decode($recepiant->user->userinfo->photo)->s)):json_encode(asset('client/assets/img/client_avatar.png')) }}"
            :total="{{ json_encode($user->role=='client'?$total_users:0 )}}"
            :pending="{{ json_encode( $user->role=='client'?$total_pending:0 )}}"
            :done="{{ json_encode( $user->role=='client'?$total_finished:0 )}}"
            :booking = "{{$booking}}"
            :lang="{{json_encode(app()->getLocale())}}">
        </chat-component>      
        @endif
    </div>
    @endif
</div>
</div>

@if ($user->role == 'consul')
        <div class="modal fade call-modal" id="close_meeting">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<!-- Outgoing Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img alt="User Image" src="{{ asset('client/assets/img/client_avatar.png') }}" class="call-avatar">
										<h4>{{$recepiant->fname . ' ' . $recepiant->lname}}</h4>
										<span>{{__('Do you want to end Meeting')}}?</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-dismiss="modal" aria-label="Close">
                                            {{__('NO')}}
                                        </a>
										<a href="javascript:void(0);" class="btn call-item call-start" id="finish_meeting">
                                            {{__('YES')}}
                                        </a>
									</div>
								</div>
							</div>
						</div>
						<!-- Outgoing Call -->
                        <form id="endmeeting_form" method="post" action="{{url('/endmeeting')}}">
                            @csrf
                            <input type="hidden" name="room_id" value="{{$room}}" />                            
                        </form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Voice Call Modal -->
@endif


@endsection 

@section('page-js')
<script src="{{ mix('js/app.js') }}" defer></script>
<script>
    $(document).ready(function(){
        $('#finish_meeting').click(function(){
            $('#endmeeting_form').submit();
        })    
    })

</script>
@endsection