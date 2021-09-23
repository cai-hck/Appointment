

@extends ('layouts.secret.main')

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

@endsection


@section('page-js')
@endsection


@section('bottom-js')
@endsection




