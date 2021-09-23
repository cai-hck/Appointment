@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css')}}">		
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection


@section('main-content')


<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="page-title">Transactions</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transactions</li>
                    </ul>
                    <div class="">
                        <form action="{{url('admin/transactions')}}">
                            @csrf
                            <div class="bookingrange btn btn-white mb-3">
                                <i class="fa fa-calendar mr-2"></i>
                                <span></span>
                                <i class="fa fa-chevron-down ml-2"></i>
                            </div>
                            <input type="hidden" name="start_date" id="start_date" value="{{$start}}"/>
                            <input type="hidden" name="end_date" id="end_date" value="{{$end}}"/>
                            <button class="btn btn-primary mb-3"><i class="fa fa-search mr-1"></i>View</button>
                        </form>
                    </div>
                </div>          
                <div class="col-sm-2 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-warning border-warning">
									<i class="fe fe-money"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$money['total']}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{ $date_range }}</h6>
							</div>
						</div>
                    </div>
                </div>
                <div class="col-sm-2 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
                                <span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-dollar"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$money['month']}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">{{ \Carbon\Carbon::now()->format('F'). ', '. \Carbon\Carbon::now()->year}}</h6>
							</div>
						</div>
                    </div>
                </div>
                <div class="col-sm-2 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-dollar"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$money['week']}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">This Week</h6>
							</div>
						</div>
                    </div>
                </div>       
                <div class="col-sm-2 ">
                    <div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon text-success border-warning">
									<i class="fa fa-dollar"></i>
								</span>
								<div class="dash-count">
									<h3><i class="fa fa-dollar"></i> {{$money['today']}}</h3>
								</div>
							</div>
							<div class="dash-widget-info">								
								<h6 class="text-muted">Today, {{\Carbon\Carbon::now()->toDateString()}}</h6>
							</div>
						</div>
                    </div>
                </div>                                                
            </div>            
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-12">
            
                <div class="card">
                    <div class="card-body">
                        @include ('alert')
                        <div class="table-responsive">
                            <table class="datatable table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Transaction Number</th>
                                        <th>Mission</th>
                                        <th>Consultant Name</th>
                                        <th>Total Amount</th>
                                        <th>Date</th>
                                        <th>Purpose</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $one)
                                    <tr>
                                        <td><a href="javascript:void(0)">{{$one->trans_id}}</td>
                                        <td>{{$one->consultant->mission->name}}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{url('admin/consultant/edit/'.$one->consultant->id)}}" class="avatar avatar-sm mr-2">
                                                <img class="avatar-img rounded-circle" 
                                                src="{{asset(json_decode($one->consultant->user->userinfo->photo)->s)}}" alt="User Image"></a>
                                                <a href="{{url('admin/consultant/edit/'.$one->consultant->id)}}">
                                                {{ $one->consultant->user->userinfo->fname . ' ' .$one->consultant->user->userinfo->lname }}
                                                <br>
                                                <span>
                                                @ {{ $one->consultant->user->name}}
                                                </span>
                                                </a>

                                            </h2>
                                        </td>
                                        <td class="text-success"><i class="fa fa-dollar"></i>{{$one->amount}}</td>
                                        <td>{{$one->date}}</td>
                                        <td>{{$one->about}}</td>
                                        <td class="text-center">
                                            @if ($one->status )
                                            <span class="badge badge-pill bg-success inv-badge"> Paid </span>
                                            @else
                                            <span class="badge badge-pill bg-warning  py-1 px-3 inv-badge"> Pending </span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="actions">
                                                @if (!$one->status)
                                                <a class="btn btn-sm bg-info-light pay-accept-modal" href="#askconfirm_modal" data-toggle="modal" data-tid="{{$one->id}}">
                                                    <i class="fa fa-check"></i> Accept Payment
                                                </a>
                                                @endif
                                                <a class="btn btn-sm bg-success-light whatsapp-notify" data-wp="{{$one->consultant->user->userinfo->whatsapp}}" href="#whatsapp_modal" data-toggle="modal">
                                                    <i class="fa fa-whatsapp"></i> Whatsapp Notify
                                                </a>                                                
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
<!-- /Page Wrapper -->

<!-- ASK confirmation Modal -->
<div class="modal fade" id="askconfirm_modal" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-content p-2">
                    <form action="{{url('admin/paymentconfirmaction')}}" method="POST" id="pay-form">
                    @csrf
                    <input type="hidden" name="t_id" id="t_id" />
                    <h4 class="modal-title">Confirm</h4>
                    <p class="mb-4">Are you sure ?</p>											
                    <button type="submit" class="btn btn-primary">Yes, Sure </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SMS Modal -->
   <!-- Whatsapp Notify Modal -->
   <div class="modal fade" id="whatsapp_modal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Whatsapp Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-content p-2">
                        <form action="{{url('/sendwhatsappsms')}}" method="POST" >
                        @csrf
                        <div class="form-group">
                            <label>Whatsapp No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_phone" value="" id="whatsapp_no" placholder="Whatsapp Number"/>
                        </div>
                        <div class="form-group">
                            <label>Message <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="message" value="" id="message" placholder="Whatsapp Number"></textarea>
                        </div>

                        <p class="mb-4">Are you sure want to send message?</p>											
                        <button type="submit" class="btn btn-primary">Yes, Send </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Whatsapp Modal -->
@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
@endsection


@section('bottom-js')
<script>
$(document).ready(function(){
	$('.pay-accept-modal').click(function(){
		$('#pay-form').find('#t_id').val($(this).attr('data-tid'));
    });
    $('.whatsapp-notify').click(function(){
        $('#whatsapp_no').val($(this).attr('data-wp'));  
    })
})
</script>

@endsection