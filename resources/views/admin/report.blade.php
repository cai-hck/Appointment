@extends ('layouts.admin.main')

@section('page-css')
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection


@section('main-content')			
    
			
<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content container-fluid">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <div class="row">
            
        
            <div class="col-12">
                <div class="">
<!--                     <form action="#">
                        @csrf
                        <div class="bookingrange btn btn-white mb-3">
                            <i class="fa fa-calendar mr-2"></i>
                            <span></span>
                            <i class="fa fa-chevron-down ml-2"></i>
                        </div>
                        <input type="hidden" name="start_date" id="start_date" value="{{$start}}"/>
                        <input type="hidden" name="end_date" id="end_date" value="{{$end}}"/>
                        <button class="btn btn-primary mb-3"><i class="fa fa-search mr-1"></i>Report</button>
                    </form> -->
                </div>
                                            

                <div class="card">
                    <div class="card-body pt-0">
                    
                        <!-- Tab Menu -->
                        <nav class="user-tabs mb-4">
                            <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link" href="#pat_missions" data-toggle="tab">Mission</a>
                                </li>											
                                <li class="nav-item">
                                    <a class="nav-link active" href="#pat_appointments" data-toggle="tab">Book Appointments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pat_client" data-toggle="tab"><span class="med-records">Client</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pat_payments" data-toggle="tab"><span class="med-records">Transactions</span></a>
                                </li>											
                            </ul>
                        </nav>
                        <!-- /Tab Menu -->
                        
                        <!-- Tab Content -->
                        <div class="tab-content pt-0">

                            <!-- Appointment Tab -->
                            <div id="pat_missions" class="tab-pane fade">										
                                <h4>Jan 20, 2021 ~ Jan 26, 2021 </h4>
                                <div class="card card-table mb-0">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Missions</th>
                                                        <th>No. of Consultant</th>
                                                        <th>No. of Secretary</th>
                                                        <th>Appointments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($mission as $one)
                                                    <tr>
                                                        <td><strong>{{$one['name'][0]}}<br>{{$one['name'][1]}}</strong></td>
                                                        <td class="text-info">{{$one['no_consultant']}}</td>
                                                        <td class="text-info">{{$one['no_secretary']}}</td>
                                                        <td class="text-primary">{{$one['no_bookings']}}</td>
                                                    </tr>                      
                                                    @endforeach                                              
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>											
                            </div>
                            <!-- /Appointment Tab -->

                            <!-- Appointment Tab -->
                            <div id="pat_appointments" class="tab-pane fade show active">
                                <h4>Today </h4>
                                <div class="card card-table mb-0">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Mission name</th>
                                                        <th>Appointments</th>
                                                        <th>Finished</th>
                                                        <th>Upcoming</th>
                                                        <th>Declined</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($today_bookings as $one)
                                                    <tr>
                                                        <td>
                                                            <strong>{{$one['name'][0]}}</strong><br>
                                                            <strong>{{$one['name'][1]}}</strong><br>
                                                        </td>
                                                        <td>{{$one['all']}}</td>
                                                        <td class="text-success">{{$one['finished']}}</td>
                                                        <td class="text-info">{{$one['upcoming']}}</td>
                                                        <td class="text-danger">{{$one['declined']}}</td>
                                                    </tr>                       
                                                    @endforeach                                             
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4"></div>
                                <h4>Jan 20, 2021 ~ Jan 26, 2021 </h4>
                                <div class="card card-table mb-0">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Mission name</th>
                                                            <th>Appointments</th>
                                                            <th>Finished</th>
                                                            <th>Upcoming</th>
                                                            <th>Declined</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($bookings as $one)
                                                        <tr>
                                                            <td>
                                                                <strong>{{$one['name'][0]}}</strong><br>
                                                                <strong>{{$one['name'][1]}}</strong><br>
                                                            </td>
                                                            <td>{{$one['all']}}</td>
                                                            <td class="text-success">{{$one['finished']}}</td>
                                                            <td class="text-info">{{$one['upcoming']}}</td>
                                                            <td class="text-danger">{{$one['declined']}}</td>
                                                        </tr>                       
                                                        @endforeach                                             
                                                    </tbody>
                                                </table>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /Appointment Tab -->
                                                                            
                            <!-- Medical Records Tab -->
                            <div id="pat_client" class="tab-pane fade">                              
                                <h4>Jan 20, 2021 ~ Jan 26, 2021 </h4>
                                <div class="card card-table mb-0">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Mission Name</th>
                                                            <th>All</th>                                                
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($clients as $one)
                                                        <tr>
                                                            <td>
                                                                <strong>{{$one['name'][0]}}</strong><br>
                                                                <strong>{{$one['name'][1]}}</strong><br>                                                                
                                                            </td>
                                                            <td>{{$one['all']}}</td>
                                                        </tr>                      
                                                        @endforeach                                              
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /Medical Records Tab -->
                            
                            <!-- Medical Records Tab -->
                            <div id="pat_payments" class="tab-pane fade">										
                                <h4>Jan 20, 2021 ~ Jan 26, 2021 

                                    <span class="text-right float-right mr-3"><strong><i class="fa fa-dollar"></i> {{$total_earn}}</strong></span>
                                </h4>
                                <div class="card card-table mb-0">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Mission Name</th>
                                                            <th>Total Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($transactions as $one)
                                                        <tr>
                                                            <td>
                                                                <strong>{{$one['name'][0]}}</strong><br>
                                                                <strong>{{$one['name'][1]}}</strong><br>
                                                            </td>
                                                            <td class="text-success"><i class="fa fa-dollar"></i> {{$one['earn']}}</td>                                                            
                                                        </tr>                           
                                                        @endforeach                                         
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /Medical Records Tab -->


                        </div>
                        <!-- Tab Content -->
                        
                    </div>
                </div>


            </div>
            
            
        </div>
        
    </div>			
</div>
<!-- /Page Wrapper -->


@endsection


@section('page-js')

<script src="{{ asset('admin/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection


@section('bottom-js')


@endsection

