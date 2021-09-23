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
    <!-- Timepicker CSS -->
    <link rel="stylesheet" href="{{asset('client/assets//clockpicker/dist/bootstrap-clockpicker.css') }}">
@endsection


@section('main-content')


<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{__('Default Schedule Timings')}}
                <div class="doc-times"><div class="doc-slot-list bg-primary" style="border:unset"> {{__('Onsite Meeting')}}</div><div class="doc-slot-list bg-info" style="border:unset"> {{__('Online Meeting') }}</div></div>
                </h4>
                <div class="profile-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card schedule-widget mb-0">
                            
                                @include ('alert')
                                <!-- Schedule Header -->
                                <div class="schedule-header">
                                    <?php
                                      $week_array = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
                                    ?>
                                    <!-- Schedule Nav -->
                                    <div class="schedule-nav">
                                        <ul class="nav nav-tabs nav-justified">
                                            @foreach ($week_array as $one_weekday)
                                            <li class="nav-item">
                                                <a class="nav-link {{Session::get('sel_weekday')==$one_weekday?'active':''}}" data-toggle="tab" href="#slot_{{$one_weekday}}">{{$one_weekday}}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <!-- /Schedule Nav -->
                                    
                                </div>
                                <!-- /Schedule Header -->
                                
                                <!-- Schedule Content -->
                                <div class="tab-content schedule-cont">                                
                                    <!-- Sunday Slot -->
                                    @foreach ($week_array as $one_weekday )
                                    <div id="slot_{{$one_weekday}}" class="tab-pane fade {{Session::get('sel_weekday')==$one_weekday?'show active':''}}">
                                        <h3>{{__('Time Slots')}}</h3> 
                                        <h4 class="card-title d-flex justify-content-between">
                                            <a class="edit-link add-slot " data-toggle="modal" data-weekday="{{$one_weekday}}" href="#add_time_slot"><i class="fa fa-plus-circle"></i> {{__('Add Slot')}}</a>
                                            <a class="edit-link del-all-slot text-danger " data-weekday="{{$one_weekday}}" href="javascript:void(0)"><i class="fa fa-minus-circle"></i> {{__('Remove All')}}</a>
                                            <?php 
                                                $hd = DB::table('holidays')->where('holiday_date',$one_weekday)->get()->first();
                                            ?>
                                            @if ($hd)
                                            <a class="edit-link text-danger  remove-a-holiday"  data-weekday="{{$one_weekday}}" href="javascript:void(0)"><i class="fa fa-check-circle"></i> {{__('Remove Holiday')}}</a>
                                            @else
                                            <a class="edit-link text-warning  set-as-holiday"  data-weekday="{{$one_weekday}}" href="javascript:void(0)"><i class="fa fa-check-circle"></i> {{__('Set Holiday')}}</a>
                                            @endif
                                        </h4>
                                        @if ($hd)
                                        <p class="text-success mb-0">{{__('Holiday!, There is no schedule timings in holiday')}}</p>
                                        @else
                                            @if ( count($timings) == 0)
                                            <p class="text-muted mb-0">{{__('No Slots')}}</p>
                                            @else                                                                                
                                            <!-- Slot List -->
                                            <div class="doc-times">
                                                <?php $isSlots = false; ?>
                                                @foreach ($timings as $one)
                                                    @if ($one->weekday == $one_weekday)
                                                    <div class="doc-slot-list  {{$one->type==1?'bg-primary':'bg-info'}}" style="border:unset">
                                                        {{\Carbon\Carbon::parse($one->start)->format('g:i A')}} - {{\Carbon\Carbon::parse($one->end)->format('g:i A')}}
                                                        <a href="javascript:void(0)" class="delete_schedule" data-tid="{{$one->id}}">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    </div>  
                                                    <?php $isSlots = true;?>      
                                                    @endif
                                                @endforeach                                    
                                                @if ($isSlots == false)
                                                <p class="text-muted mb-0">{{__('No Slots')}}</p>
                                                @endif
                                            </div>
                                            <!-- /Slot List -->
                                            @endif
                                        @endif
                                    </div>
                                    <!-- /Sunday Slot -->
                                    @endforeach                                    
                                </div>
                                <!-- /Schedule Content -->                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Time Slot Modal -->
<div class="modal fade custom-modal" id="add_time_slot">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Add Time Slot')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{url('timingslots/addslot')}}" autocomplete="off" method="POST" id="add_slot_modal">                                 
                    @csrf
                    <input type="hidden" name="weekday" id="modal_weekday" />
                    <div class="hours-info">
                        <div class="row form-row hours-cont">
                            <div class="col-12 col-md-">

                                <div class="row form-row">                                    
                                    <div class="form-group col-md-6" id="clockpicker-start">
                                        <label>{{__('Start Time')}} <span class="text-danger">*</span></label>
                                        <input class="form-control time-only" name="start_time" id="clock-start" required/>  
                                        <input class="form-control " type="hidden" id="clock-starter"/>  
                                    </div>
                                    <div class="form-group col-md-6" id="clockpicker-finish">
                                        <label>{{__('Finish Time')}} <span class="text-danger">*</span></label>
                                        <input class="form-control time-only" name="end_time" id="clock-end" required/>  
                                        <input class="form-control " type="hidden" id="clock-ender"/>  
                                    </div>         
                                    <div class="form-group col-md-6">
                                        <label>{{__('Duration (Minutes)')}} <span class="text-danger">*</span></label>
                                        <input class="form-control " type="number" name="duration" id="modal_slot_input" value="15" required/>  
                                    </div>                                                
                                    <div class="form-group col-md-6">
                                        <label>{{__('Meeting Type')}} <span class="text-danger">*</span></label>
                                        <select name="m_type" class="form-control" required>
                                            <option value="onsite">{{__('Onsite Meeting')}}</option>
                                            <option value="online">{{__('Online Meeting')}}</option>
                                        </select>
                                    </div>                                                                                                                                                                           
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="submit-section text-center">
                        <button type="submit" class="btn btn-primary submit-btn">{{__('Save Changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Time Slot Modal -->

<!-- Edit Time Slot Modal -->
<div class="modal fade custom-modal" id="edit_time_slot">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Time Slots</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="hours-info">
                        <div class="row form-row hours-cont">
                            <div class="col-12 col-md-10">
                                <div class="row form-row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Start Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option selected>12.00 am</option>
                                                <option>12.30 am</option>  
                                                <option>1.00 am</option>
                                                <option>1.30 am</option>
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>End Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option>12.00 am</option>
                                                <option selected>12.30 am</option>  
                                                <option>1.00 am</option>
                                                <option>1.30 am</option>
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row form-row hours-cont">
                            <div class="col-12 col-md-10">
                                <div class="row form-row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Start Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option>12.00 am</option>
                                                <option selected>12.30 am</option>
                                                <option>1.00 am</option>
                                                <option>1.30 am</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>End Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option>12.00 am</option>
                                                <option>12.30 am</option>
                                                <option selected>1.00 am</option>
                                                <option>1.30 am</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>
                        </div>
                        
                        <div class="row form-row hours-cont">
                            <div class="col-12 col-md-10">
                                <div class="row form-row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Start Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option>12.00 am</option>
                                                <option>12.30 am</option>
                                                <option selected>1.00 am</option>
                                                <option>1.30 am</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>End Time</label>
                                            <select class="form-control">
                                                <option>-</option>
                                                <option>12.00 am</option>
                                                <option>12.30 am</option>
                                                <option>1.00 am</option>
                                                <option selected>1.30 am</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>
                        </div>

                    </div>
                    
                    <div class="add-more mb-3">
                        <a href="javascript:void(0);" class="add-hours"><i class="fa fa-plus-circle"></i> Add More</a>
                    </div>
                    <div class="submit-section text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Time Slot Modal -->


<!-- Del Slot Modal -->
<div class="modal fade" id="delete_slot_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                    <form action="{{url('timingslots/delete')}}" method="POST" id="slot_del_form">
                    @csrf
                    <input type="hidden" name="t_id" id="t_id" />
                    <input type="hidden" name="weekday" id="del_weekday" />
                    <h4 class="modal-title">{{__('Delete Slot')}}</h4>
                    <p class="mb-4">{{__('Are you sure want to delete?')}}</p>                    
                    <button type="submit" class="btn btn-primary" data-sid=""> {{__('Yes, Sure') }}</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Del Slot Modal -->

<!-- Confirm Modal -->
<div class="modal fade" id="confirm_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                <form action="{{url('timingslots/setholiday')}}" method="POST" id="slot_holiday_form">  
                @csrf
                    <input type="hidden" name="weekday" id="holiday_weekday" />
                    <h4 class="modal-title">{{__('Save Changes') }}</h4>
                    <p class="mb-4">{{ __('Are you sure want to save?') }}</p>
                    <button type="submit" class="btn btn-primary" data-sid="">{{__('Yes, Sure') }} </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Confirm Modal -->

<!-- Remove Holiday Modal -->
<div class="modal fade" id="remove_confirm_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                <form action="{{url('timingslots/removeholiday')}}" method="POST" id="remove_holiday_form">  
                @csrf
                    <input type="hidden" name="weekday" id="holiday_weekday" />
                    <h4 class="modal-title">{{__('Save Changes') }}</h4>
                    <p class="mb-4">{{ __('Are you sure want to remove?') }}</p>
                    <button type="submit" class="btn btn-primary" data-sid=""> {{__('Yes, Sure') }} </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('No, Close') }}</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Confirm Modal -->
@endsection

@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <script src="{{ asset('client/assets/'.app()->getLocale().'/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Timepicker Js -->
    <script src="{{ asset('client/assets/clockpicker/dist/jquery-clockpicker.js') }}"></script>
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
<script>
$(document).ready(function(){
    $('#clockpicker-start').clockpicker({ autoclose: true });
    $('#clockpicker-finish').clockpicker({ autoclose: true });
    $("#clock-start").change(function(){
        var dur =  $('#modal_slot_input').val();
        var st = $(this).val();
        var default_end_time = moment(moment().format('YYYY-MM-DD') + ' ' + st).add(dur,'minutes').format('HH:mm');$('#clock-end').val(default_end_time);                
        $('#clockpicker-finish').clockpicker({autoclose: true,default:default_end_time});
    })
    $("#clock-end").change(function(){
        var dur =  $('#modal_slot_input').val();
        var dst = $(this).val();
        var default_start_time = moment(moment().format('YYYY-MM-DD') + ' ' + dst).subtract(dur,'minutes').format('HH:mm');$('#clock-start').val(default_start_time);                
        $('#clockpicker-start').clockpicker({autoclose: true,default:default_start_time});
    })    

    $('#modal_slot_input').change(function(){
        var dur =  $(this).val();
        var st = $('#clock-start').val();
        var default_end_time = moment(moment().format('YYYY-MM-DD') + ' ' + st).add(dur,'minutes').format('HH:mm');$('#clock-end').val(default_end_time);                
        $('#clockpicker-finish').clockpicker({autoclose: true,default:default_end_time});
    });
    $('.set-as-holiday').click(function(){
        $('#slot_holiday_form').find('#holiday_weekday').val($(this).attr('data-weekday'));
        $('#confirm_modal').modal('show');
    });
    $('.remove-a-holiday').click(function(){
        $('#remove_holiday_form').find('#holiday_weekday').val($(this).attr('data-weekday'));
        $('#remove_confirm_modal').modal('show');
    })
    $('.add-slot').click(function(){
        $('#add_slot_modal').find('#modal_weekday').val($(this).attr('data-weekday'));
    });
    $('.delete_schedule').click(function(){
        $('#slot_del_form').find('#t_id').val('');
        $('#slot_del_form').find('#del_weekday').val('');
        $("#delete_slot_modal").modal('show');
        $('#slot_del_form').find('#t_id').val( $(this).attr('data-tid'));
    });

    $('.del-all-slot').click(function(){
        $('#slot_del_form').find('#del_weekday').val($(this).attr('data-weekday'));
        $("#delete_slot_modal").modal('show');
    })

})
</script>

@endsection