

@extends ('layouts.consul.main')

@section('page-css')
@endsection


@section('main-content')

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-4">{{ __('Today Schedules') }} {{\Carbon\Carbon::now()->format('Y-m-d , l')}}</h4>
        <div class="appointment-tab">																											
            <div class="">								   
                <!-- Today Appointment Tab -->
                <div class="tab-pane" id="today-appointments">
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Client Name') }}</th>
                                            <th>{{ __('Time') }}</th>
                                            <th>{{ __('Meeting Type') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th ></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($today_bookings) == 0)
                                        <tr><td colspan="5">No Appointments</td></tr>
                                        @else
                                        @foreach ($today_bookings as $one)
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="javascript:void(0)">{{$one->client->fname. ' ' . $one->client->lname}}</a>
                                                </h2>
                                            </td>
                                            <td>{{\Carbon\Carbon::parse($one->start_time)->format('g:i A')}} ~ {{\Carbon\Carbon::parse($one->end_time)->format('g:i A')}}</td>
                                            <td>{{$one->type}} Meeting</td>
                                            <td>
                                                @if ($one->status == 'finished')
                                                <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                                {{ __('Finished') }}
                                                </a>
                                                @endif
                                                @if ($one->status == 'approved')
                                                <a href="javascript:void(0);" class="btn btn-sm bg-warning-light">
                                                {{ __('Upcoming') }}
                                                </a>
                                                @endif
                                                @if ($one->status == 'declined')
                                                <a href="javascript:void(0);" class="btn btn-sm bg-danger-light">
                                                {{ __('Declined') }}
                                                </a>
                                                @endif                                                                                                
                                            </td>                                           
                                            <td class="text-right">
                                                <div class="table-action">                                                  																			
                                                    <a href="{{url('appointments/viewbooking/'.$one->id)}}" class="btn btn-sm bg-info-light">
                                                        <i class="fa fa-eye"></i> {{ __('View') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>	 
                                        @endforeach                                                                   														
                                        @endif
                                    </tbody>
                                </table>		
                            </div>	
                        </div>	
                    </div>	
                </div>
                <!-- /Today Appointment Tab -->
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-4">
        {{ __('Sub Accounts')}}
        @if ($user->consultant->type != 'sub')
            <a href="{{url('consul/addaccount')}}" class="btn btn-primary float-right"><i class="fa fa-plus mr-1"></i>{{ __('Add an Account')}}</a>                                        
        @endif
        </h4>
        <div class="appointment-tab">									
            <div class="tab-content">
            
                <!-- Upcoming Appointment Tab -->
                <div class="">
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Role')}}</th>
                                            <th>{{ __('Full Name')}}</th>
                                            <th>{{ __('Email')}}</th>
                                            <th>{{ __('Phone')}}</th>
                                            <th>{{ __('Whatsapp')}}</th>
                                            <th class="text-center">{{ __('Active Date')}}</th>
                                            <th class="text-center">{{ __('Expire Date')}}</th>
                                            <th>{{ __('Status')}}</th>
                                            @if ($user->consultant->type!='sub')
                                            <th></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($sub_consuls as $one)
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                                {{ __('Consultant')}}
                                                </a>                                                                           
                                            </td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}" class="avatar avatar-sm mr-2">
                                                        <img class="avatar-img rounded-circle" 
                                                        src="{{ asset(json_decode($one->user->userinfo->photo)->s) }}" alt="{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}">
                                                    </a>
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}">{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}<span>@ {{$one->user->name}}</span></a>
                                                </h2>
                                            </td>
                                            <td>{{$one->user->email}}</td>
                                            <td>{{$one->user->userinfo->mobile}}</td>
                                            <td>{{$one->user->userinfo->whatsapp}}</td>
                                            <td class="text-center">{{$one->active_date}}</td>
                                            <td class="text-center">{{$one->expire_date}}</td>
                                            <td class="text-center">
                                                @if ($one->status)         
                                                <a href="javascript:void(0);" class="btn btn-sm bg-success text-white">Active</a>
                                                @else                                                
                                                <a href="javascript:void(0);" class="btn btn-sm bg-dark  text-white">Inactive</a>
                                                @endif
                                            </td>
                                            @if ($user->consultant->type!='sub')
                                            <td class="text-right">
                                                <div class="table-action">
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}" class="btn btn-sm bg-info-light">
                                                        <i class="far fa-eye"></i> {{__('Edit')}}
                                                    </a>																																							
                                                    <a data-toggle="modal"  href="#delete_modal" data-uid="{{$one->user->id}}" class="btn btn-sm bg-danger-light open-del-modal">
                                                        <i class="fas fa-times"></i> {{__('Remove')}}
                                                    </a>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>	
                                        @endforeach
                                        @foreach ($sub_secrets as $one)													
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm bg-warning-light">
                                                {{ __('Secretary')}}
                                                </a>                                                                           
                                            </td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}" class="avatar avatar-sm mr-2">
                                                        <img class="avatar-img rounded-circle" 
                                                        src="{{ asset(json_decode($one->user->userinfo->photo)->s) }}" alt="{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}">
                                                    </a>
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}">{{$one->user->userinfo->fname. ' ' .$one->user->userinfo->lname}}<span>@ {{$one->user->name}}</span></a>
                                                </h2>
                                            </td>
                                            <td>{{$one->user->email}}</td>
                                            <td>{{$one->user->userinfo->mobile}}</td>
                                            <td>{{$one->user->userinfo->whatsapp}}</td>
                                            <td class="text-center">{{$one->active_date}}</td>
                                            <td class="text-center">{{$one->expire_date}}</td>
                                            <td class="text-center">
                                                @if ($one->status)         
                                                <a href="javascript:void(0);" class="btn btn-sm bg-success text-white">Active</a>
                                                @else                                                
                                                <a href="javascript:void(0);" class="btn btn-sm bg-dark  text-white">Inactive</a>
                                                @endif
                                            </td>
                                            @if ($user->consultant->type!='sub')
                                            <td class="text-right">
                                                <div class="table-action">
                                                    <a href="{{url('consul/profile/'.$one->user->id)}}" class="btn btn-sm bg-info-light">
                                                        <i class="far fa-eye"></i> {{__('Edit')}}
                                                    </a>																																							
                                                    <a data-toggle="modal"  data-target="#delete_modal" href="javascript:void(0)" data-uid="{{$one->user->id}}" class="btn btn-sm bg-danger-light open-del-modal">
                                                        <i class="fas fa-times"></i> {{__('Remove')}}
                                                    </a>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>	
                                        @endforeach															                                        
                                    </tbody>
                                </table>		
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Upcoming Appointment Tab -->																				
            </div>
        </div>
    </div>
</div>



@endsection


@section('page-js')
@endsection


@section('bottom-js')
@endsection




