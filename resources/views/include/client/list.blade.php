<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.css')}}">		
    <link rel="stylesheet" href="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{asset('client/assets/datepicker/css/bootstrap-datepicker3.css') }}">    
    <style>
        .dash-widget-header {
            align-items: center;
            display: flex;
            margin-bottom: 15px;
        }
        .dash-widget-icon {
            align-items: center;
            display: inline-flex;
            font-size: 1.875rem;
            height: 50px;
            justify-content: center;
            line-height: 48px;
            text-align: center;
            width: 50px;
            border: 3px solid;
            border-radius: 50px;
            padding: 28px;
        }     
        .dash-count {
            font-size: 18px;
            margin-left: auto;
        }       
    </style>
@endsection


@section('main-content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
        @include ('alert')
        <!-- Page Header -->
        <div class="page-header">
        <div class="row">                   
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-success">
                                <i class="fa fa-users"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$all_clients}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('All')}} {{__('Clients')}} </h6>
                        </div>
                    </div>
                </div>
            </div>                      
            <div class="col-sm-3 ">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fa fa-users"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{$today_clients}}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">								
                            <h6 class="text-muted">{{__('Today')}}, {{__('Clients')}}</h6>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
        <div class="row">
            <div class="col-md-12">   
                <div class="card">          
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>{{__('Full Name')}}</th>                               
                                    <th>{{__('Email')}}</th>                               
                                    <th>{{__('Phone')}}</th>                               
                                    <th>{{__('Whatsapp')}}</th>                               
                                    <th>{{__('Address')}}</th>  
                                    <th class="text-right">{{__('Actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($clients as $one)
                                <tr>
                                    <td>{{$one->fname . ' ' .$one->lname}}</td>
                                    <td>{{$one->email}}</td>
                                    <td>{{$one->phone}}</td>
                                    <td>{{$one->whatsapp}}</td>
                                    <td>{{$one->address}}</td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="{{url('clients/view/'.$one->id)}}" class="btn btn-sm bg-info-light">{{__('View Detail') }}</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{asset('client/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="{{asset('client/assets/datepicker/locales/bootstrap-datepicker.hi.min.js') }}"></script>
    @endif   
@endsection


@section('bottom-js')

@endsection