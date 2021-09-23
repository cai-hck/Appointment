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
                        No Users
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