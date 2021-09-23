@extends ('layouts.client.main')

@section('page-css')
@endsection


@section('main-content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('Booking')}} - {{__('Choose a Mission')}} </h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">
    
        <div class="row">
            @if (count($missions) > 0 )
            @foreach ($missions as $one)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="booking-doc-info" style="justify-content: space-between;align-items: baseline;">
                            <a href="javascript:void(0)" class="booking-doc-img">
                                <img src="{{asset($one->cover_image)}}" alt="User Image">
                            </a>
                            <div class="booking-info">
                                <h4 style="word-break: break-all;">
                                    @if (app()->getLocale()=='en')
                                    <a href="{{url('booking/'.base64_encode($one->id))}}">{{$one->name}}</a>
                                    @else
                                    <a href="{{url('booking/'.base64_encode($one->id))}}">{{$one->name_ar}}</a>
                                    @endif
                                </h4>                                                                                    
                            </div>
                            <a href="{{url('booking/'.base64_encode($one->id))}}" class="btn btn-primary submit-btn ml-1"> {{__('Next Step') }} </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach     
            @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="booking-doc-info" style="justify-content: space-between;align-items: baseline;">
                            <a href="{{url('/')}}" class="booking-doc-img">
                                <img src="{{asset($config['icon'])}}" alt="None">
                            </a>
                            <div class="booking-info">
                                <h4 style="word-break: break-all;"><a href="#">{{ __('No available Missions') }}</a></h4>                                                                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
 
        </div>
    </div>

</div>		
<!-- /Page Content -->

@endsection


@section('page-js')
@endsection


@section('bottom-js')
@endsection

