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
								<h3 class="page-title">Missions</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>    
									<li class="breadcrumb-item active">Missions</li>
								</ul>
                            </div>
                            <div class="col-sm-4 text-right">
                                <a class="btn btn-primary mt-2" href="{{url('admin/mission/add')}}">
                                    <i class="fe fe-plus"></i> Add Mission
                                </a>
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
													<th>ID</th>
													<th>Mission Name</th>
													<th>Main Consultant</th>
													<th>No. of Consultant</th>
													<th>No. of Secretary</th>
													<th>Cost per Account</th>
													<th>Mission Status</th>
													<th>Consul Status</th>
													<th class="right"></th>
												</tr>
											</thead>
											<tbody>
												@foreach ($consuls as $one)
												<tr>
													<td>#MIS{{$one->id}}</td>
													<td>
														{{$one->name}}
														<br>
														{{$one->name_ar}}
													</td>
													<td>
														@if ($one->consultant_id != 0)
                                                        <h2 class="table-avatar">
															<a href="{{url('admin/consultant/edit/'.$one->consultant->id)}}" class="avatar avatar-sm mr-2">
																<img class="avatar-img rounded-circle" src="{{ asset(json_decode($one->consultant->user->userinfo->photo)->s)}}" alt="{{$one->consultant->user->userinfo->fname. ' ' .$one->consultant->user->userinfo->lname}}">
															</a>
															<a href="{{url('admin/consultant/edit/'.$one->consultant->id)}}">{{$one->consultant->user->userinfo->fname. ' ' .$one->consultant->user->userinfo->lname}}</a>
                                                        </h2>
														@else
														<a class="btn btn-sm bg-warning-light font-bold" >
															<i class="fa fa-circle"></i> Not Assigned
														</a>
														@endif
                                                    </td>
													<td> 
														<?php 
															$sub_consuls = DB::table('consultants')->where('type','sub')->where('mission_id', $one->id)->get();
														?>
														<div class="avatar-group">
															@foreach ($sub_consuls as $sub_one)
															<?php 
																$sub_user = DB::table('user_infos')->where('user_id', $sub_one->user_id)->get()->first();
															?>
															<div class="avatar">
																<a href="{{url('admin/consultant/edit/sub/'.$sub_one->user_id)}}" class="avatar">
																<img class="avatar-img rounded-circle border border-white"
																alt="{{$sub_user->fname. ' ' .$sub_user->lname}}" 
																src="{{asset(json_decode($sub_user->photo)->s)}}">
																</a>
															</div>								
															@endforeach						
														</div>
													</td>
													<td> 
														<?php 
															$sub_secrets = DB::table('secretaries')->where('mission_id', $one->id)->get();
														?>
														<div class="avatar-group">
															@foreach ($sub_secrets as $sub_one)
															<?php 
																$sub_user = DB::table('user_infos')->where('user_id', $sub_one->user_id)->get()->first();
															?>
															<div class="avatar">
																<a href="{{url('admin/consultant/edit/sub/'.$sub_one->user_id)}}" class="avatar">
																	<img class="avatar-img rounded-circle border border-white"
																	alt="{{$sub_user->fname. ' ' .$sub_user->lname}}" 
																	src="{{asset(json_decode($sub_user->photo)->s)}}">
																</a>
															</div>								
															@endforeach						
														</div>
													</td>
													<td class="text-left text-success font-bold"> 
														<i class="fa fa-dollar"></i> {{$one->cost_per_user}}
													</td>
													<td>
                                                        <div class="status-toggle">
															<input type="checkbox" id="status_1" class="check" {{$one->status?'checked':''}}>
															<label for="status_1" class="checktoggle">checkbox</label>
														</div>
													</td>
													<td>
														@if ($one->consultant->status)
															@if ($one->consultant->expire_date != '' && (strtotime($one->consultant->expire_date) < strtotime(date('Y-m-d'))))
																<a class="btn btn-sm bg-danger btn-rounded text-white" >Expired</a>
															@else
																<a class="btn btn-sm bg-success btn-rounded text-white" >Active </a>
															@endif
														@else
															<a class="btn btn-sm bg-dark btn-rounded text-white" >Inctivate</a>
														@endif
													</td>
                                                    <td class="text-right">
														<div class="actions">
															<a class="btn btn-sm bg-success-light" href="{{url('admin/mission/edit/'.$one->id)}}">
																<i class="fe fe-pencil"></i> Edit
															</a>
															<a  data-toggle="modal"  href="#delete_modal" class="btn btn-sm bg-danger-light open-del-modal" data-mid="{{$one->id}}">
																<i class="fe fe-trash"></i> Delete
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
							<!-- /Recent Orders -->									
						<!-- Delete Modal -->
						</div>
					</div>
				</div>			
			</div>
			<!-- /Page Wrapper -->
			<div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
				<div class="modal-dialog modal-dialog-centered" role="document" >
					<div class="modal-content">
					<!--	<div class="modal-header">
							<h5 class="modal-title">Delete</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>-->
						<div class="modal-body">
							<div class="form-content p-2">
								<form action="{{url('admin/deletemission')}}" method="POST" id="del-form">
								@csrf
								<input type="hidden" name="m_id" id="m_id" />
								<h4 class="modal-title">Delete</h4>
								<p class="mb-4">Are you sure want to delete?</p>											
								<button type="submit" class="btn btn-primary">Yes, Sure </button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- /Delete Modal -->
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
});
</script>
@endsection

