<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.meeting';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)
@section('page-css')

@endsection

@section('main-content')

<!-- Page Content -->
<div class="content" style="padding-top:0">
    <div class="row">
        <div class="col-xl-12">
            <div class="chat-window" style="min-height:800px;background:white">            
                <!-- Chat Left -->
                <div class="chat-cont-left" >
                    <div class="chat-header">
                        <span>Clients</span>
                    </div>								
                    <div class="chat-users-list">
                        <div class="chat-scroll">

                            @foreach ($bookings as $one_booking)
                            <!-- 
                                Useful Class : read-chat active
                             -->
                            <a href="{{url('meetings/single/'.$one_booking->id)}}" class="media {{$one_booking->id==$booking->id?'read-chat active':''}}">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-away">
                                        <img src="{{ asset('client/assets/img/client_avatar.png')}}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="user-name">{{$one_booking->client->fname. ' ' . $one_booking->client->lname}}</div>
<!--                                         <div class="user-last-chat">Hey, How are you?</div> -->
                                    </div>
                                    <div>
<!--                                         <div class="last-chat-time block">2 min</div> -->
<!--                                         <div class="badge badge-success badge-pill">15</div> -->
                                    </div>
                                </div>
                            </a>
                            @endforeach                         									
                        </div>
                    </div>
                </div>
                <!-- /Chat Left -->
            
                <!-- Chat Right -->
                <div class="chat-cont-right" id="app">
                    <chat-component
                        :bk-id='{{ json_encode($booking->id) }}'
                        :avatar-Client='{{ json_encode(asset("client/assets/img/client_avatar.png")) }}'
                        :client-User='{{ json_encode($client) }}'
                    ></chat-component>
                </div>
                <!-- /Chat Right -->
                <!-- :mainavatar="{{ asset(json_decode($user->userinfo->photo)->s) }}"                 -->
            </div>
        </div>
    </div>
</div>		
<!-- /Page Content -->
		<!-- Voice Call Modal -->
		<div class="modal fade call-modal" id="voice_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<!-- Outgoing Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img alt="User Image" src="{{ asset('client/assets/img/client_avatar.png') }}" class="call-avatar">
										<h4>Darren Elder</h4>
										<span>Connecting...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="voice-call.html" class="btn call-item call-start"><i class="material-icons">call</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- Outgoing Call -->

					</div>
				</div>
			</div>
		</div>
		<!-- /Voice Call Modal -->
		
		<!-- Video Call Modal -->
		<div class="modal fade call-modal" id="video_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
					
						<!-- Incoming Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img class="call-avatar" src="{{ asset('client/assets/img/client_avatar.png') }}" alt="User Image">
										<h4>Darren Elder</h4>
										<span>Calling ...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="video-call.html" class="btn call-item call-start"><i class="material-icons">videocam</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Incoming Call -->
						
					</div>
				</div>
			</div>
		</div>
		<!-- Video Call Modal -->



@endsection

@section('page-js')

@endsection
@section('bottom-js')
@endsection