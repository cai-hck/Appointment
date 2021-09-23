@extends ('layouts.admin.main')

@section('page-css')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css')}}">		
@endsection


@section('main-content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">
    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="page-title">Consultants</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Consultants</li>
                    </ul>
                </div>
                <div class="col-sm-4 text-right">
                    <a class="btn btn-primary mt-2" href="{{url('admin/consultant/add')}}">
                        <i class="fe fe-plus"></i> Add Main Consultant
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
                                        <th>Full Name</th>
                                        <th>Mission</th>
                                        <th>Sub Consultant</th>
                                        <th>Sub Secretary</th>
                                        <th>Activate Date</th>
                                        <th>Expire Date</th>
                                        <th>Status</th>
                                        <th class="right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($consuls as $one)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded-circle" 
                                                        src="{{asset(json_decode($one->user->userinfo->photo)->s)}}" alt="{{$one->user->userinfo->fname. ' '. $one->user->userinfo->lname}}">
                                                </a>
                                                <a href="#">
                                                    {{$one->user->userinfo->fname. ' '. $one->user->userinfo->lname}}
                                                    <span>@ {{$one->user->name}}</span>
                                                </a>                                                
                                            </h2>
                                        </td>
                                        <td>
                                            @if ($one->mission_id == 0) 
                                                <a class="btn btn-sm bg-warning-light font-bold" >
                                                    <i class="fa fa-circle"></i> Not Assigned
                                                </a>
                                            @else
                                                {{$one->mission->name}}
                                            @endif
                                        </td>
                                        <td> 
                                            <?php 
												$sub_consuls = DB::table('consultants')->where('type','sub')->where('mission_id', $one->mission->id)->get();
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
                                                $sub_secrets = DB::table('secretaries')->where('mission_id',$one->mission->id)->get();
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
                                        <td>{{$one->active_date}}</td>                                        
                                        <td>{{$one->expire_date}}</td>                                        
                                        <td>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="status_1" class="check" {{$one->status?'checked':''}}>
                                                <label for="status_1" class="checktoggle">checkbox</label>
                                            </div>
                                        </td>                                                                  
                                        <td class="text-right">
                                            <div class="actions">
<!--                                                 <a class="btn btn-sm bg-info-light" href="#">
                                                    <i class="fa fa-comments"></i> Chat
                                                </a>
                                                <a class="btn btn-sm bg-info-light" href="#">
                                                    <i class="fa fa-whatsapp"></i> Whatsapp
                                                </a>
                                                <a class="btn btn-sm bg-info-light" href="#">
                                                    <i class="fa fa-phone"></i> Phone Call
                                                </a>                                                                                               
-->
                                                <a class="btn btn-sm bg-success-light" href="{{url('admin/consultant/edit/'.$one->id)}}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <a  data-toggle="modal" href="#delete_modal" class="btn btn-sm bg-danger-light open-del-modal" data-cid="{{$one->id}}">
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
                                    <form action="{{url('admin/deleteconsultant')}}" method="POST" id="del-form">
                                    @csrf
                                    <input type="hidden" name="c_id" id="c_id" />
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
            </div>
        </div>
    </div>			
                    
</div>
<!-- /Page Wrapper -->

@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js') }}"></script>
@endsection


@section('bottom-js')
<script>
$(document).ready(function(){
	$('.open-del-modal').click(function(){
		$('#del-form').find('#m_id').val($(this).attr('data-cid'));
	});
})
</script>
@endsection

