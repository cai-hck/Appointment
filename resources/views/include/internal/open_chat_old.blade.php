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
                        <span>{{__('Mission Users')}}</span>
                    </div>								
                    <div class="chat-users-list">
                        <div class="chat-scroll">

                           @foreach ($consuls as $one)
                           @if ($one->user->id != $user->id)
                            <div class="media {{$selected_member == $one->user->id?'bg-success-light':''}}">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-away">
                                        <img src="{{ asset(json_decode($one->user->userinfo->photo)->s)}}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body d-flex align-center" style="justify-content:space-between;align-items:center">
                                    <div class="text-left">
                                        <div class="user-name ml-3">{{$one->user->userinfo->fname. ' ' . $one->user->userinfo->lname}}</div>
                                        <div class="text-muted ml-3 text-italic"><mark><i>@ {{$user->name}} {{$one->user->role=='consul'?__('Consultant'):__('Secretary')}}</i></mark></div>
                                    </div>
                                    <div> 
                                        @if ($selected_member != $one->user->id)
                                        <div class="badge badge-success badge-pill py-2 px-2">
                                            <a href="{{url('/internal-chat/open/'. $one->user->id)}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Open')}}</a>                                            
                                        </div>     
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach      
                            @foreach ($secrets as $one)
                            @if ($one->user->id != $user->id)
                            <div class="media {{$selected_member == $one->user->id?'bg-success-light':''}}" >
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-away">
                                        <img src="{{ asset(json_decode($one->user->userinfo->photo)->s)}}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body d-flex align-center" style="justify-content:space-between;align-items:center">
                                    <div class="text-left">
                                        <div class="user-name ml-3">{{$one->user->userinfo->fname. ' ' . $one->user->userinfo->lname}}</div>
                                        <div class="text-muted ml-3 text-italic"><mark><i>@ {{$user->name}} {{$one->user->role=='consul'?__('Consultant'):__('Secretary')}}</i></mark></div>
                                    </div>
                                    <div> 
                                        @if ($selected_member != $one->user->id)
                                        <div class="badge badge-success badge-pill py-2 px-2">
                                            <a href="{{url('/internal-chat/open/'. $one->user->id)}}" class="text-white py-2 px-3 ml-2 mr-2">{{__('Open')}}</a>                                            
                                        </div>     
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach                                                     									
                        </div>
                    </div>
                </div>
                <!-- /Chat Left -->
            
                <!-- Chat Right -->
                <div class="chat-cont-right" id="app">
                    <div>
                       
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