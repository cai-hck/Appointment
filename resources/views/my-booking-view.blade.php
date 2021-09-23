@extends ('layouts.client.main')

@section('page-css')


@endsection


@section('main-content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('My Booking') }}</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
            
<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="booking-info w-100">
                        <h4><a href="javascript:void(0)">{{app()->getLocale()=='en'?$booking->mission->name:$booking->mission->name_ar}}</a></h4>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="booking-info w-100">
                        <h4>
                            <a href="javascript:void(0)">{{app()->getLocale()=='en'?$booking->section->en_name:$booking->section->ar_name}}</a>
                            <span class="badge badge-success"><i class="fa fa-check"></i> {{__('Confirmed')}}</span>
                        </h4>
                    </div>
                    <div class="booking-info w-100">
                        <?php echo app()->getLocale() == 'en'? $booking->section->en_about:$booking->section->ar_about ?>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
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
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('page-js')
    <script src="{{asset('client/assets/ezview/EZView.js') }}"></script>
    <script src="{{asset('client/assets/ezview/draggable.js') }}"></script>
    <script>
    $(document).ready(function(){
        $('.booking-file-viewer').EZView();
    })
    </script>
@endsection


@section('bottom-js')

@endsection
