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

<!-- Basic Information -->
<div class="card">
    <div class="card-body">
            @include ('alert')
            <h4 class="card-title">{{__('Reschedule')}} -  {{$sel_date}} , {{\Carbon\Carbon::parse($sel_date)->format('l')}}</h4>           
            <?php if (!$schedule) { ?>
            <div class="alert alert-warning alert-block">
                <strong>{{__('There is no assigned schedules on')}} {{$sel_date}}. {{__('Please add schedule at first from')}} <a href="{{url('schedules/addsingle?date='.$sel_date)}}">HERE</a></strong>
            </div>
            <?php } else { ?>
            <div class="submit-section submit-btn-bottom text-right">
                <button type="submit" class="btn btn-primary submit-btn" id="save-schedule">{{__('Save')}}</button>
            </div>
            <div class="row form-row">										
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">{{__('Schedule')}}</h4></div>
                        <div class="card-body">
                        <form action="{{url('updateschedule')}}" method="POST" id="schedule-form">
                            @csrf
                            <input type="hidden" name="reschedule" value="1"/>
                            <div class="form-group">
                                <label>{{__('Date')}}<span class="text-danger">*</span></label>
                                <input id="datepicker" class="form-control" name="schedule_date" value="{{date('Y-m-d',strtotime($sel_date))}}" readonly>
                            </div>                      
                            <div class="form-group">
                                <label>{{__('Weekday')}}</label>
                                <input id="weekday_date" class="form-control" name="weekday_date" value="{{\Carbon\Carbon::parse($sel_date)->format('l')}}" disabled>
                            </div>                            
                            <div class="form-group">
                                <label>{{__('Available Slots')}}<span class="text-danger">*</span> </label>
                                <input  type="number" class="form-control" name="no_slots" id="no_slots" value="{{$schedule->slots}}">
                            </div>                                                                                                                    
                            <div class="form-group">
                                <label>{{__('Timing Schedules')}}<span class="text-danger">*</span></label>
                                <select class="form-control" name="slots_val_optoins"  id="slots_val_optoins">
                                    <option value="default" {{$schedule->isDefault?'selected':''}}>{{__('Use default timing schedule')}}</option>
                                    <option value="custom" {{!$schedule->isDefault?'selected':''}}>{{__('Custom timing schedule')}}</option>
                                </select>
                            </div>       
                            <div class="form-group">
                                <label>{{__('Holiday Setting')}} <span class="text-danger">*</span></label>
                                <select class="form-control" name="holiday_val_optoins"  id="holiday_val_optoins">
                                    <option value="default" {{$schedule->isUseDefault?'selected':''}}>{{__('Use Holiday Settings')}}</option>
                                    <option value="ignore" {{!$schedule->isUseDefault?'selected':''}} id="ignore_setting_option">{{__('Ignore Holiday')}}</option>
                                </select>
                            </div>      
                        </form>                                                                                                                                      
                        </div>
                    </div>
                </div>	
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">{{__('Slots')}} 
                            <span class="float-right text-success" id="slot-add-span" style="display:none">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#slot_modal" class="text-success"><i class="fa fa-plus-circle"></i></a></span></h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-hover table-center mb-0  table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{__('Start')}}</th>
                                        <th>{{__('End')}}</th>
                                        <th>{{__('Duration')}}</th>
                                        <th>{{__('Meeting Type')}}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="slot-custom-body" style="display:{{$schedule->isDefault?'none':''}}">
                                    @if ($schedule && !$schedule->isDefault && $schedule->timings != '') 
                                    <?php $timings = json_decode($schedule->timings);?>
                                    @foreach ($timings as $one)
                                    <tr>
                                        <td>{{\Carbon\Carbon::parse($one->s)->format('g:i A')}}</td>
                                        <td>{{\Carbon\Carbon::parse($one->e)->format('g:i A')}}</td>
                                        <td>{{$one->d}} Minutes</td>
                                        <td>{{$one->t=='onsite'?'Onsite':'Online'}} {{__('Meeting')}}</td>
                                        <td class="text-center" ><a href="#" id="del-slot{{$one->i}}" data-sid="{{$one->i}}" class="del-slot"><i class="fa fa-times-circle text-danger"></i></a></td>                                        
                                    </tr>
                                    @endforeach      
                                    @endif
                                </tbody>

                                <tbody id="slot-default-body"  style="display:{{!$schedule->isDefault?'none':''}}">  
                                    @if ($holiday)
                                    <tr>
                                        <td colspan="5" class="text-success text-center"><i class="fa fa-gifts"></i> {{__('Holiday')}} </td>
                                    </tr>
                                    @else
                                    @foreach ($default_timings as $one)
                                    <tr>
                                        <td>{{\Carbon\Carbon::parse($one->start)->format('g:i A')}}</td>
                                        <td>{{\Carbon\Carbon::parse($one->end)->format('g:i A')}}</td>
                                        <td>{{$one->duration}} Minutes</td>
                                        <td>{{$one->type?'Onsite':'Online'}} {{__('Meeting')}}</td>
                                        <td class="text-center" ><a style="display:none" href="#" id="del-slot{{$one->id}}" data-sid="{{$one->id}}" class="del-slot"><i class="fa fa-times-circle text-danger"></i></a></td>                                        
                                    </tr>
                                    @endforeach           
                                    @endif                                                                                   
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>	               							            								                       
            </div>      
            <?php } ?>
    </div>
</div>
<!-- /Basic Information -->		
<!-- Add Slot Details Modal -->
<div class="modal fade custom-modal" id="slot_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Add a Slot')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="clockpicker-start">
                    <label>{{__('Start Time')}} <span class="text-danger">*</span></label>
                    <input class="form-control time-only" autocomplete="off" name="start_time" id="clock-start"/>  
                    <input class="form-control " type="hidden" id="clock-starter"/>  
                </div>
                <div class="form-group" id="clockpicker-finish">
                    <label>{{__('Finish Time')}} <span class="text-danger">*</span></label>
                    <input class="form-control time-only" autocomplete="off" name="end_time" id="clock-end"/>  
                    <input class="form-control " type="hidden" id="clock-ender"/>  
                </div>         
                <div class="form-group">
                    <label>{{__('Duration (Minutes)')}} <span class="text-danger">*</span></label>
                    <input class="form-control " type="number" name="duration" id="modal_slot_input" value="15"/>  
                </div>                                                
                <div class="form-group">
                    <label>{{__('Meeting Type')}} <span class="text-danger">*</span></label>
                    <select name="m_type" class="form-control">
                        <option value="onsite">{{__('Onsite Meeting')}}</option>
                        <option value="online">{{__('Online Meeting')}}</option>
                    </select>
                </div>                                                                           
                <div class="form-group text-center">
                    <a href="javascript:void(0)" class="btn btn-primary btn-block" id="add_slot_popsave">{{__('Save Slot')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Slot Details Modal -->
   
<!-- Delete Modal -->
<div class="modal fade" id="delete_slot_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">{{__('Delete')}}</h4>
                    <p class="mb-4">{{__('Are you sure want to delete?')}}</p>
                    <a href="javascript:void(0)" class="btn btn-primary" id="del-save-btn" data-sid=""> {{__('Yes, Sure')}} </a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

<!-- Confirm Modal -->
<div class="modal fade" id="save_schedule_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">{{__('Save Changes')}}</h4>
                    <p class="mb-4">{{__('Are you sure want to save?')}}</p>
                    <a href="javascript:void(0)" class="btn btn-primary" id="schedule-save-btn" data-sid=""> {{__('Yes, Sure')}} </a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->
    @csrf   
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
        $(document).ready(function(){

            var form_submit_data = new Array();
            form_submit_data['slots'] = new Array();
            <?php
                if ($schedule && $schedule->timings!='') {
                    foreach (json_decode($schedule->timings) as $one)
                        echo "form_submit_data['slots'].push([".$one->i.", '".$one->s. "', '".$one->e."', ".$one->d.",'".$one->t."' ]);";
                }                
            ?>
            var lang = "<?php echo app()->getLocale() == 'ar'? 'hi':''?>";
            var ok_lang = "<?php echo app()->getLocale() == 'ar'? 'موافق':'OK'?>";                 
            /* Slot Modal */
            $('#clockpicker-start').clockpicker({ autoclose: true ,  afterDone: function(val) {  //console.log($('#clock-start').val()) } });
            $('#clockpicker-finish').clockpicker({ autoclose: true  });
            $("#clock-start").change(function(){
                var dur =  $('#slot_modal').find('input[name=duration]').val(); var st = $(this).val();
                var default_end_time = moment(moment().format('YYYY-MM-DD') + ' ' + st).add(dur,'minutes').format('H:m');$('#clock-end').val(default_end_time);                
                $('#clockpicker-finish').clockpicker({autoclose: true,default:default_end_time});
            })
            $(document).on('click','.del-slot', function(){ $("#delete_slot_modal").modal('show'); $('#del-save-btn').attr('data-sid',$(this).attr('data-sid'));});             //Delete Slot
            //Remove slot 
            $('#del-save-btn').click(function() {
                var td_id = $(this).attr('data-sid');
                form_submit_data['slots'].forEach(
                    function(val) {
                        if (val[0] ==  td_id) {
                            val[1] = '';
                            val[2] = '';
                            val[3] = '';
                            val[4] = '';
                        }
                    });
                $('#del-slot'+$(this).attr('data-sid')).closest('tr').remove();   
                $('#delete_slot_modal').modal('hide');             
            });

            $('#modal_slot_input').change(function(){
                var dur =  $(this).val();
                var st = $('#clock-start').val();
                var default_end_time = moment(moment().format('YYYY-MM-DD') + ' ' + st).add(dur,'minutes').format('HH:mm');$('#clock-end').val(default_end_time);                
                $('#clockpicker-finish').clockpicker({autoclose: true,default:default_end_time});
            })
            //Add Slot popup save
            $('#add_slot_popsave').click(function(){
                var slot_index ="<?php echo time() ?>";
                var start_time = $('#clock-start').val();
                var end_time = $('#clock-end').val();
                var dur = $('#slot_modal').find('input[name=duration]').val();
                var type = $('#slot_modal').find('select[name=m_type]').val();var tr ='';
                tr+='<tr>'; tr+='<td>' + start_time + '</td>';tr+='<td>' + end_time  + '</td>';tr+='<td>' + dur + ' Minutes </td>'; tr+='<td>' + (type=='onsite'?'Onsite Meeting':'Online Meeting') + '</td>';
                tr+='<td class="text-center"><a href="#" id="del-slot'+slot_index +'" data-sid="' + slot_index+ '" class="del-slot"><i class="fa fa-times-circle text-danger"></i></a></td>';tr+='</tr>';
                $('#slot-custom-body').append(tr);
                var start = $('#slot_modal').find('input[name=start_time]').val('');var end = $('#slot_modal').find('input[name=end_time]').val('');
                $('#slot_modal').modal('hide');
                form_submit_data['slots'].push([slot_index, start_time, end_time, dur,type ]); 
            });

            // Date Range Picker
            if($('.option-daterange').length > 0) {
                var start = moment(); var end = moment().month(12);
                function booking_range(start, end) {
                    $('input[name=range_start]').val(start.format('YYYY-MM-D'));                    
                    $('input[name=range_end]').val(end.format('YYYY-MM-D'));
                    $('.option-daterange span').html(start.format('YYYY-MM-D') + ' - ' + end.format('YYYY-MM-D'));
                }
                $('.option-daterange').daterangepicker({ startDate: start, endDate: end,}, booking_range); booking_range(start, end);
            };        
            $('#save-schedule').click(function(){$('#save_schedule_modal').modal('show');})             

            $('#schedule-save-btn').click(function(){ create_form(form_submit_data); $('#schedule-form').submit();})
            function create_form(data) {
                var form_html = '';
                form_submit_data['slots'].forEach(function (val) {
                    var input_val = val[0] +'|'+ val[1] +'|'+ val[2] +'|'+ val[3] +'|'+ val[4];
                    form_html +='<input name="slots[]" type="hidden" value="'+ input_val+ '" />';               
                });
                $('#schedule-form').append(form_html);
            }         
        })
    </script>
@endsection


@section('bottom-js')

<script>
    $(document).ready(function(){
        <?php if ($schedule && $schedule->isDefault ==  true) {?>
            $('#ignore_setting_option').hide();
        <?php } else { ?>
            $('#slot-add-span').show();
        <?php }?>
        $('#slots_val_optoins').change(function(){
            if ($(this).val() == 'default') {
                $('#slot-custom-body').hide();
                $('#slot-default-body').show();
                $('#slot-add-span').hide();
                $('#ignore_setting_option').hide();
            } else {
                $('#slot-custom-body').show();
                $('#slot-default-body').hide();
                $('#slot-add-span').show();
                $('#ignore_setting_option').show();
            }
        })
    })
</script>
@endsection