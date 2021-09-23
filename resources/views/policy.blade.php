<?php 
if ($user && $user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else if ($user && $user->role == 'secret') {
    $layout = 'layouts.secret.main';
}
else {
    $layout = 'layouts.client.main';
}
?>
@extends ($layout)

@section('page-css')


@endsection


@section('main-content')

@if (!$user)
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('Privacy Policy') }}</h2>
            </div>
        </div>
    </div>
</div>
@endif
<!-- /Breadcrumb -->


<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="terms-content">
                    <?php if (app()->getLocale() == 'en') { 
                        echo $setting['en_policy'];
                    } else {
                        echo $setting['ar_policy'];
                    }    
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')
 
@endsection


@section('bottom-js')

@endsection
