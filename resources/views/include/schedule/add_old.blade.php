@extends ('layouts.consul.main')

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
            <h4 class="card-title">Create new Schedule</h4>           
            <div class="submit-section submit-btn-bottom text-right">
                <button type="submit" class="btn btn-primary submit-btn" id="save-schedule">Save</button>
            </div>
            <div class="row form-row">										
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Schedule</h4></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input id="datepicker" class="form-control" name="schedule_date" value="{{$sel_date}}">
                            </div>
                            <div class="form-group">
                                <label>Weekday<span class="text-danger">*</span></label>
                                <input id="weekday_date" class="form-control" name="weekday_date" value="{{\Carbon\Carbon::parse($sel_date)->format('l')}}" disabled>
                            </div>                            
                            <div class="form-group">
                                <label>Available Slots </label>
                                <input  type="number" class="form-control" name="no_slots" id="no_slots" value="0">
                            </div>                                
                            <div class="form-group">
                                <label>Timing Slot Duration <span class="text-danger">*</span></label>
                                <select class="form-control" name="slot_duration" id="slot_duration">
                                    <option value="15">15 Mins</option>
                                    <option value="30">30 Mins</option>
                                    <option value="45">45 Mins</option>
                                    <option value="60">1 Hour</option>
                                    <option value="0">Custom</option>
                                </select>
                            </div>                                                                
                        </div>
                    </div>
                </div>	
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Slots 
                            <span class="float-right text-success">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#slot_modal" class="text-success"><i class="fa fa-plus-circle"></i></a></span></h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-hover table-center mb-0  table-bordered">
                                <thead>
                                    <tr><th>Start</th>
                                        <th>End</th>
                                        <th>Duration</th>
                                        <th>Meeting Type</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="slot-body">                                                                                                  
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>	
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Advanced Options 
                                <span class="float-right text-success">                                        
                                    <a href="#option_modal" class="text-success" data-toggle="modal" data-target="#option_modal"><i class="fa fa-plus-circle"></i></a>
                                    <a href="javascript:void(0)" class="text-danger" id="del-option"><i class="fa fa-minus-circle"></i></a>
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Rule</th><th></th></tr>
                                </thead>
                                <tbody id="option-body">
                                                                                            
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>	                    							            								                       
            </div>      
    </div>
</div>
<!-- /Basic Information -->		
<!-- Add Slot Details Modal -->
<div class="modal fade custom-modal" id="slot_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a Slot</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="clockpicker-start">
                    <label>Start Time <span class="text-danger">*</span></label>
                    <input class="form-control time-only" name="start_time" id="clock-start"/>  
                    <input class="form-control " type="hidden" id="clock-starter"/>  
                </div>
                <div class="form-group" id="clockpicker-finish">
                    <label>Finish Time <span class="text-danger">*</span></label>
                    <input class="form-control time-only" name="end_time" id="clock-end"/>  
                    <input class="form-control " type="hidden" id="clock-ender"/>  
                </div>         
                <div class="form-group">
                    <label>Duration (Minutes) <span class="text-danger">*</span></label>
                    <input class="form-control " type="number" name="duration" id="modal_slot_input" value="15"/>  
                </div>                                                
                <div class="form-group">
                    <label>Meeting Type <span class="text-danger">*</span></label>
                    <select name="m_type" class="form-control">
                        <option value="onsite">Onsite Meeting</option>
                        <option value="online">Online Meeting</option>
                    </select>
                </div>                                                                           
                <div class="form-group text-center">
                    <a href="javascript:void(0)" class="btn btn-primary btn-block" id="add_slot_popsave">Save Slot</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Slot Details Modal -->
        
<!-- Advanced Option Details Modal -->
<div class="modal fade custom-modal" id="option_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advanced Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Do you want repeat this schedule? <span class="text-danger">*</span></label>
                    <select class="form-control" name="repeat_ask" >
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>  
                <div class="form-group extra-option">
                    <label class="w-100">Date Range <span class="text-danger">*</span>(Default next 12 months)</label>
                    <div class="option-daterange btn btn-white mb-3 w-100">
                        <i class="far fa-calendar-alt mr-2"></i>
                        <span></span>
                        <i class="fas fa-chevron-down ml-2"></i>                                                    
                    </div>
                    <input type="hidden" name="option_range_start"/>
                    <input type="hidden" name="option_range_end"/>
                </div>
                <div class="form-group extra-option">
                    <label>Repeat Options<span class="text-danger">*</span></label>
                    <select class="form-control" name="repeat_option">
                        <option value="day">Every Day</option>
                        <option value="week">Every Week</option>
                        <option value="month">Every Month</option>
                        <option value="custom">Every Custom Days</option>
                    </select>
                    <input type="number" name="repeat_custom" class="form-control mt-1" placeholder="Custom days"/>
                </div>         
                <div class="form-group extra-option">
                    <label>Except Days?<span class="text-danger">*</span></label>
                    <select class="form-control" name="repeat_holiay">
                        <option value="nothing">Nothing</option>
                        <option value="holiday">Holiday</option>
                        <option value="weekend">Weekend</option>                                                    
                    </select>
                </div>                                                                                                                  
                <div class="form-group text-center">
                    <a class="btn btn-primary btn-block" href="javascript:void(0)" id="save_repeat_option">Save Option</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Advanced Option Details Modal -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_slot_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content text-center">                                
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">Delete</h4>
                    <p class="mb-4">Are you sure want to delete?</p>
                    <a href="javascript:void(0)" class="btn btn-primary" id="del-save-btn" data-sid=""> Yes, Sure </a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
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
                    <h4 class="modal-title">Save Changes</h4>
                    <p class="mb-4">Are you sure want to save?</p>
                    <a href="javascript:void(0)" class="btn btn-primary" id="schedule-save-btn" data-sid=""> Yes, Sure </a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No, Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->
<form action="{{url('saveschedule')}}" method="POST" style="display:none" id="schedule-form">
    @csrf   
</form>
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
            form_submit_data['options'] = new Array();
            form_submit_data['schedule_date'] = $('#datepicker').val();
            form_submit_data['available_slots'] = $('#no_slots').val();

            var lang = "<?php echo app()->getLocale() == 'ar'? 'hi':''?>";
            var ok_lang = "<?php echo app()->getLocale() == 'ar'? 'موافق':'OK'?>";
            $('#datepicker').datepicker({todayBtn:true,language: lang,format: "yyyy-m-d" });
            $('#datepicker').change(function(){ 
                form_submit_data['schedule_date'] = $(this).val(); 
                $('#weekday_date').val(moment(form_submit_data['schedule_date']).format('dddd'));
            });
            $('#no_slots').change(function(){ form_submit_data['available_slots'] = $(this).val(); });
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
                //form_submit_data['slots'][$(this).attr('data-sid')] = [];
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
            //Basic Schedule Info
            $('#slot_duration').change(function(){ $('#slot_modal').find('input[name=duration]').val($(this).val());});

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
                $('#slot-body').append(tr);
                var start = $('#slot_modal').find('input[name=start_time]').val('');var end = $('#slot_modal').find('input[name=end_time]').val('');
                $('#slot_modal').modal('hide');
                form_submit_data['slots'].push([slot_index, start_time, end_time, dur,type ]); 
            });
            $('#option_modal').find('select[name=repeat_ask]').change(function(){if ($(this).val() == 1) { $('.extra-option').show(); } else { $('.extra-option').hide();}});            //Option Modal

            // Date Range Picker
            if($('.option-daterange').length > 0) {
                var start = moment(); var end = moment().month(12);
                function booking_range(start, end) {
                    $('#option_modal').find('input[name=option_range_start]').val(start.format('YYYY-MM-D'));                    
                    $('#option_modal').find('input[name=option_range_end]').val(end.format('YYYY-MM-D'));
                    $('.option-daterange span').html(start.format('YYYY-MM-D') + ' - ' + end.format('YYYY-MM-D'));
                }
                $('.option-daterange').daterangepicker({ startDate: start, endDate: end,}, booking_range); booking_range(start, end);
            };

            //Save Repeat option
            $('#save_repeat_option').click(function() {               
                $('#option-body').empty();
                var repeat_ok = $('#option_modal').find('select[name=repeat_ask]').val();
                var repeat_range_start = $('#option_modal').find('input[name=option_range_start]').val();
                var repeat_range_end = $('#option_modal').find('input[name=option_range_end]').val();
                var repeat_option =  $('#option_modal').find('select[name=repeat_option]').val();
                var repeat_option_custom_days =  $('#option_modal').find('input[name=repeat_custom]').val()==''?0:$('#option_modal').find('input[name=repeat_custom]').val();
                var except_holiday = $('#option_modal').find('select[name=repeat_holiay]').val();
                var tr = '';
                if (repeat_ok == 1) {
                    tr+='<tr>';
                    tr +='<td><i class="fa fa-clock"></i> Repeat?</td>';
                    tr +='<td class="text-center text-success"><i class="fa fa-check"></i></td>';
                    tr +='</tr>';
                    //Range start ~ finish
                    tr+='<tr>';
                    tr +='<td><i class="fa fa-calendar"></i> Start Date </td>';
                    tr +='<td class="text-center text-success">'+ repeat_range_start +'</td>';
                    tr +='</tr>';

                    tr+='<tr>';
                    tr +='<td><i class="fa fa-calendar"></i> Finish Date </td>';
                    tr +='<td class="text-center text-success">'+ repeat_range_end +'</td>';
                    tr +='</tr>';
                    // Repeat Option
                    var str_repeat_option = 'Day';
                    if (repeat_option == 1) str_repeat_option = ' Day';
                    if (repeat_option == 7) str_repeat_option = 'Week';
                    if (repeat_option == 30) str_repeat_option = 'Month';
                    if (repeat_option == 0) str_repeat_option = repeat_option_custom_days + ' Days';
                    tr+='<tr>';
                    tr +='<td>Repeat every </td>';
                    tr +='<td class="text-center text-success">'+ str_repeat_option +'</td>';
                    tr +='</tr>';       
                    //except holiday
                    var str_except_option = 'No Holidays';
                    
                    if (except_holiday == 0) str_except_option = 'No Holidays';
                    if (except_holiday == 1) str_except_option = 'Holidays';
                    if (except_holiday == 7) str_except_option = 'Weekends';

                    tr+='<tr>';
                    tr +='<td>Except Days?</td>';
                    tr +='<td class="text-center text-success">'+ str_except_option +'</td>';
                    tr +='</tr>';       
                }
                else{ tr +='<tr>'; tr +='<td>Repeat?</td>'; tr +='<td class="text-center text-danger"><i class="fa fa-minus"></i></td>'; tr +='</tr>';}                                
                $('#option-body').append(tr);$('#option_modal').modal('hide');
                form_submit_data['options'] = [repeat_ok,repeat_range_start,repeat_range_end,repeat_option,repeat_option_custom_days,except_holiday];
            });
            $('#del-option').click(function(){ $('#option-body').empty();})
            $('#save-schedule').click(function(){$('#save_schedule_modal').modal('show');})
            $('#schedule-save-btn').click(function(){ create_form(form_submit_data); $('#schedule-form').submit();})
            function create_form(data) {
                var form_html = '';
                form_html +='<input name="date" value="'+ data['schedule_date'] +'" />';
                form_html +='<input name="no_slots" value="'+ data['available_slots'] +'" />';
                form_submit_data['slots'].forEach(function (val) {
                    var input_val = val[0] +'|'+ val[1] +'|'+ val[2] +'|'+ val[3] +'|'+ val[4];
                    form_html +='<input name="slots[]" value="'+ input_val+ '" />';               
                });
                var option_val = '';
                form_submit_data['options'].forEach(function(val){
                    option_val += val + '|';
                });                
                form_html +='<input name="options" value="'+ option_val+'"/>';
                $('#schedule-form').append(form_html);
            }
        })
    </script>
@endsection


@section('bottom-js')

@endsection