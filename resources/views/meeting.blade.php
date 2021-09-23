@extends ('layouts.client.main')

@section('page-css')
<style>
    .chat-cont-right {
        position: unset;
        right: unset;
        top: unset;
        opacity: 1;
        visibility: unset;
        margin:auto;
    }
</style>
@endsection


@section('main-content')

<!-- Page Content -->
<div class="content mb-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="chat-window">                               
                    <!-- Chat Right -->
                    <div class="chat-cont-right" id="app">
                        <client-chat-component
                            :bk-id='{{ json_encode($booking->id) }}'
                            :avatar-Consul='{{ json_encode(asset(json_decode($consul->user->userinfo->photo)->s)) }}'
                            :consul-User='{{ json_encode($consul->user->userinfo) }}'
                        ></client-chat-component>
                     </div>
                    <!-- /Chat Right -->                    
                </div>
            </div>
        </div>
        <!-- /Row -->

    </div>

</div>		
<!-- /Page Content -->


@endsection
@section('page-js')
<script src="{{ mix('js/app.js') }}" defer></script>
@endsection
@section('bottom-js')

@endsection