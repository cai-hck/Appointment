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
    @media only screen and (max-width: 991.98px) {
        .chat-cont-right {
            position: absolute;
            right: -100%;
            top: 0;
            opacity: 0;
            visibility: hidden;
        }
    }
</style>
@endsection


@section('main-content')

<!-- Page Content -->
<div class="content" style="padding-top:0">
<div class="container-fluid">
    <div class="row" id="app">
        <internal-chat-component
            :consuls="{{$consuls}}"
            :secrets="{{$secrets}}"
            :selected_user="{{$selected_user}}"
            :user="{{$current_user}}"
            :url="{{json_encode(url('/'))}}"
            :internalroom="{{json_encode($internalroom)}}"
            :mission="{{json_encode($mission_id)}}"
            :lang="{{json_encode(app()->getLocale())}}"
            ></internal-chat-component>
    </div>
</div>
</div>		
<!-- /Page Content -->
@endsection

@section('page-js')
<script src="{{ mix('js/app.js') }}" defer></script>
<script>
    $(document).ready(function(){
        var chatAppTarget = $('.chat-window');
		if ($(window).width() > 991)
			chatAppTarget.removeClass('chat-slide');
		
		$(document).on("click",".chat-window .chat-users-list a.media",function () {
			if ($(window).width() <= 991) {
				chatAppTarget.addClass('chat-slide');
			}
			return false;
		});
		$(document).on("click","#back_user_list",function () {
			if ($(window).width() <= 991) {
				chatAppTarget.removeClass('chat-slide');
			}	
			return false;
		});

        $(document).on('click',".chat-users-list a.media", function(e){
            //alert($(this).attr('data-uid'));
            e.preventDefault();
            if ($(this).attr('data-uid') != {{$selected_member}})    {        
                location.href="{{url('/internal-chat/open/')}}/" + $(this).attr('data-uid');
            }
        })
    });
	
</script>
@endsection
@section('bottom-js')
@endsection