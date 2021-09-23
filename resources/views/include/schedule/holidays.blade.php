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
@endsection

@section('main-content')



<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
            <!-- /Page Header -->
            <div class="row">
                <div class="col-md-4">
                
                    <div class="card">                    
                        <div class="card-body">
                            <form action="{{url('holidays/add')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="mb-3">Choose a Date<span class="text-danger">*</span></label>
                                <input id="datepicker" class="form-control w-100 mb-3" name="date" value="{{date('Y-m-d')}}" required/>
                                <label class="mb-3">About (English) <span class="text-danger">*</span></label>
                                <textarea class="form-control mb-3  " name="about_en" row="3"></textarea>
                                <label class="mb-3">About (Arabic) <span class="text-danger">*</span></label>
                                <textarea class="form-control mb-3  " name="about_ar" row="3"></textarea>
                                <button class="btn btn-info btn-block"><i class="fa fa-calendar-alt "></i> Set Holiday</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><strong>{{__('Holiday Lists')}} ({{count($holidays)}})</strong></div>
                        <div class="card-body">
                            @include ('alert')
                            <div class="table-responsive">
                                <table class="datatable table table-hover table-center mb-0">
                                    <thead>
                                        <th>{{__('Date or Weekday')}}</th>
                                        <th>{{__('Comment')}}</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        @foreach ($holidays as $one)
                                        <tr>
                                            <td>{{strtoupper($one->holiday_date)}}</td>
                                            <td>{{app()->getLocale()=='en'?$one->about_en:$one->about_ar}}</td>
                                            <td>
                                                <a class="btn btn-sm btn-danger" href="{{url('holidays/delete/'.$one->id)}}"><i class="fa fa-times"></i></a>
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
    <script>
        var lang = "<?php echo app()->getLocale() == 'ar'? 'hi':''?>";
        var ok_lang = "<?php echo app()->getLocale() == 'ar'? 'موافق':'OK'?>";
        $('#datepicker').datepicker({todayBtn:true,language: lang,format: "yyyy-mm-dd" });
    </script>
@endsection


@section('bottom-js')

@endsection