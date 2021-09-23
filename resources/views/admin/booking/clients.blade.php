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
                    <h3 class="page-title">Clients</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>    
                        <li class="breadcrumb-item active">Clients</li>
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
                                        <th>Client</th>
                                        <th>Contact</th>
                                        <th>Appointments</th>                                        
                                        <th class="right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($clients as $one)
                                <tr>        
                                    <td>{{$one->fname. ' ' .$one->lname}}</td>
                                    <td>
                                        <i class="fa fa-phone"></i> {{$one->phone}} <br>
                                        <i class="fa fa-whatsapp"></i> {{$one->whatsapp}} <br>
                                        <i class="fa fa-inbox"></i> {{$one->email}} <br>
                                        <i class="fa fa-map"></i> {{$one->address}} <br>
                                    </td>
                                    <td>
                                        @if ($one->bookings)
                                        <table class="table no-border">
                                            <tbody>
                                        @foreach ($one->bookings as $book)
                                            <tr>
                                                <td>
                                                    {{$book->mission->name}}<br>
                                                    {{$book->mission->name_ar}}
                                                </td>
                                                <td>
                                                    {{$book->section->en_name}}<br>
                                                    {{$book->section->ar_name}}
                                                </td>
                                                <td>{{$book->schedule_date}}</td>
                                                <td>{{\Carbon\Carbon::parse($book->start_time)->format('g:i A'). ' '.\Carbon\Carbon::parse($book->end_time)->format('g:i A')}}</td>
                                                <td>
                                                    <span class="badge badge-pill text-white bg-{{$book->type=='Onsite'?'primary':'info'}}" > {{$book->type}} Meeting</span>                                                    
                                                </td>
                                                <td >
                                                    @if ($book->status == 'approved') <span class="badge badge-pill text-white bg-warning">Upcoming</span>@endif
                                                    @if ($book->status == 'finished') <span class="badge badge-pill text-white bg-success">Finished</span>@endif
                                                    @if ($book->status == 'declined') <span class="badge badge-pill text-white bg-danger">Declined</span>@endif
                                                </td>
                                            </tr>
                                        @endforeach
                                            </tbody> 
                                        </table>
                                        @endif
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
