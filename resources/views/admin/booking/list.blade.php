@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">		
@endsection


@section('main-content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="page-title">Appointments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>    
                        <li class="breadcrumb-item active">Book Appointments</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-12">            
                @include('alert')
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Issue Date</th>
                                        <th>Client</th>
                                        <th>Contact</th>
                                        <th>Mission</th>
                                        <th>Section</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Meeting</th>
                                        <th>Status</th>
                                        <th class="right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($bookings as $one)
                                <tr>        
                                    <td>{{$one->created_at}}</td>
                                    <td>{{$one->client->fname. ' ' .$one->client->lname}}</td>
                                    <td>
                                        <i class="fa fa-phone"></i> {{$one->client->phone}} <br>
                                        <i class="fa fa-whatsapp"></i> {{$one->client->whatsapp}} <br>
                                        <i class="fa fa-inbox"></i> {{$one->client->email}} <br>
                                    </td>
                                    <td>
                                        {{$one->mission->name}}<br>
                                        {{$one->mission->name_ar}}<br>
                                    </td>
                                    <td>
                                        {{$one->section->en_name}}<br>
                                        {{$one->section->ar_name}}<br>
                                    </td>
                                    <td>{{$one->schedule_date}}</td>
                                    <td>{{ \Carbon\Carbon::parse($one->start_time)->format('g:i A') .' ~ '. \Carbon\Carbon::parse($one->end_time)->format('g:i A') }}</td>
                                    <td>
                                        <span class="badge badge-pill py-2 px-3 text-white bg-{{$one->type=='Onsite'?'primary':'info'}}">
                                         {{$one->type}} Meeting
                                        </span>
                                    </td>
                                    <td>
                                        @if ($one->status == 'approved') <a class="btn btn-sm bg-warning-light text-white">Upcoming</a> @endif
                                        @if ($one->status == 'finished') <a class="btn btn-sm bg-success-light text-white">Finished</a> @endif
                                        @if ($one->status == 'declined') <a class="btn btn-sm bg-danger-light text-white">Declined</a> @endif
                                        
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Recent Orders -->									
            <!-- Delete Modal -->
            </div>
        </div>
    </div>			
</div>
<!-- /Page Wrapper -->



@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js')}}"></script>
@endsection


@section('bottom-js')
<script>
$(document).ready(function(){
	$('.open-del-modal').click(function(){
		$('#del-form').find('#m_id').val($(this).attr('data-mid'));
	});
})
</script>
@endsection
