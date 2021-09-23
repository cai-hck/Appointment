<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\Consultant;
use App\Secretary;
use App\Schedule;
use App\ScheduleTiming;
use App\Holiday;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Notification;
use App\Notifications\ScheduleCreated;
use App\Notifications\ScheduleTimingCreated;
use App\Notifications\HolidayCreated;
use App\Events\MissionNotify;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Auth::check())
            {
                $user = Auth::user();
                if ($user->role == 'consul' ||  $user->role == 'secret' )
                {
                    return $next($request);
                }
            }
            return redirect('login');
        });
    }

    public function timingslots()
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               
        $page_title = __('Schedule Timings');            

        $timings = ScheduleTiming::where('mission_id', $mission_id)->get();

        return view('include.schedule.timingslots',compact('user','page_title','timings'));
    }   

    public function timingslots_add_weekday(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               
        $consultant_id = $user->role == 'consul'?$user->consultant->id:$user->secretary->consultant->id;

        $start = $request['start_time'];
        $end = $request['end_time'];
        $duration = $request['duration'];
        $type = $request['m_type'];

        ScheduleTiming::create([
            'mission_id'=>$mission_id,
            'consultant_id'=>$consultant_id,
            'creator'=>$user->id,
            'weekday' => $request['weekday'],
            'start' => $start,
            'end' => $end,
            'duration' => $duration,
            'type' => $type=='onsite'?true:false
        ]);

        return back()->with('success',__('Added slot successfully'))->with('sel_weekday',$request['weekday']);
    }

    public function timingslots_delete(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               

        $tid = $request['t_id'];
        $weekday = $request['weekday'];
        if ($weekday != null) {
            ScheduleTiming::where('weekday', $weekday)->where('mission_id', $mission_id)->delete();
            return back()->with('success','Deleted all slots successfully')->with('sel_weekday',$request['weekday']);
        } else {
            ScheduleTiming::find($tid)->delete();
            return back()->with('success','Deleted slot successfully')->with('sel_weekday',$request['weekday']);
        }        
    }
    public function timingslots_holiday(Request $request)
    {
        $weekday = $request['weekday'];
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               
        $consultant_id = $user->role == 'consul'?$user->consultant->id:$user->secretary->consultant->id;

        $hd = Holiday::create([
            'mission_id'=>$mission_id,
            'consultant_id'=>$consultant_id,
            'creator'=>$user->id,
            'holiday_date'=>$weekday,
        ]);

        ScheduleTiming::where('weekday', $weekday)->where('mission_id', $mission_id)->delete();

        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en') 
            $msg = 'New Holiday created by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'Holiday Date '. $weekday ."\r\n";
        else
            $msg = 'عطلة جديدة تم إنشاؤها بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ العيد '. $weekday ."\r\n";
    
        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $hd,  $msg,'holiday');
        foreach ($secrets as $one)  $this->notification_open($one, $hd,  $msg,'holiday');

        $event_msg = '';
        if (app()->getLocale() == 'en') 
            $event_msg = 'New Holiday created by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Holiday Date '. $weekday ."<br>";                  
        else
            $event_msg = 'عطلة جديدة تم إنشاؤها بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ العيد '. $weekday ."\r\n";                
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        return back()->with('success',__('Holiday Set up successfully'))->with('sel_weekday',$request['weekday']);
    }

    public function timingslots_remove_holiday(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;          
        $weekday = $request['weekday'];
        $hd = Holiday::where('holiday_date',$weekday)->where('mission_id', $mission_id)->get()->first();        
        //notify to users
        $msg ='' ;
        if (app()->getLocale() == 'en')
            $msg = 'Holiday deleted by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'Holiday Date '. $weekday ."\r\n";
        else
            $msg = 'تم حذف العطلة بواسطة ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'تاريخ العيد '. $weekday ."\r\n";
        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $hd,  $msg,'holiday');
        foreach ($secrets as $one)  $this->notification_open($one, $hd,  $msg,'holiday');

        $event_msg ='' ;
        if (app()->getLocale() == 'en')
        $event_msg = 'Holiday deleted by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Holiday Date '. $weekday ."<br>";                  
        else
        $event_msg = 'تم حذف العطلة بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ العيد '. $weekday ."\r\n";
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        Holiday::where('holiday_date',$weekday)->where('mission_id', $mission_id)->delete();        

        return back()->with('success',__('Holiday removed successfully'))->with('sel_weekday',$request['weekday']);
    }

    /* Holiday Set */
    public function holidays()
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;  
        $page_title = __('Holidays');    
        $holidays = Holiday::where('mission_id', $mission_id)->get();    
        return view('include.schedule.holidays',compact('user','page_title','holidays'));
    }
    public function holidays_add(Request $request)
    {
        $date = $request['date'];

        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               
        $consultant_id = $user->role == 'consul'?$user->consultant->id:$user->secretary->consultant->id;
        $hd = Holiday::create([
            'mission_id'=>$mission_id,
            'consultant_id'=>$consultant_id,
            'creator'=>$user->id,
            'holiday_date'=>$date,
            'about_en' => $request['about_en'],
            'about_ar' => $request['about_ar'],
        ]);

        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en')
            $msg = 'New Holiday created by ' 
              . $user->userinfo->fname. ' '
              . $user->userinfo->lname. "\r\n"
              . 'Holiday Date '. $date ."\r\n";
        else
            $msg = 'عطلة جديدة تم إنشاؤها بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ '. $date ."\r\n";
  
        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $hd,  $msg,'holiday');
        foreach ($secrets as $one)  $this->notification_open($one, $hd,  $msg,'holiday');

        $event_msg = '';
        if (app()->getLocale() == 'en')
            $event_msg = 'New Holiday created by ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'Holiday Date '. $date ."\r\n";
        else
            $event_msg = 'عطلة جديدة تم إنشاؤها بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ '. $date ."\r\n";            
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        return back()->with('success',__('Holiday Set up successfully'));
    }
    public function holidays_delete($id)
    {
        Holiday::find($id)->delete();
        return back()->with('success',__('Holiday removed successfully'));
    }


    /* Schedule Page route, actions, notifies,  */
    public function index()
    {
        $user = Auth::user();
        $page_title = __('Schedules');

        /*      
        $start_view_date = isset($_GET['start_date'])?$_GET['start_date']:'';
        $end_view_date = isset($_GET['end_date'])?$_GET['end_date']:'';
        */        
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;

        $schedules = Schedule::where('mission_id', $mission_id)->get();
        $holidays = Holiday::where('mission_id', $mission_id)->get();
        $rescheduled = Schedule::where('isReschedule', true)->where('mission_id', $mission_id)->get();        
        $today = Schedule::where('date', date('Y-m-d'))->where('mission_id', $mission_id)->get()->first();
        $today_meetings = 0;
        $today_onsite_meetings = 0;
        $today_online_meetings = 0;
        if ($today) {
            if ($today->isDefault)  {
                $today_meetings = ScheduleTiming::where('weekday', $today->weekday)->where('mission_id', $mission_id)->get()->count();
                $today_onsite_meetings = ScheduleTiming::where('weekday', $today->weekday)->where('mission_id', $mission_id)->where('type',true)->get()->count();
                $today_online_meetings = ScheduleTiming::where('weekday', $today->weekday)->where('mission_id', $mission_id)->where('type',false)->get()->count();
            }
            else {
                $today_meetings = $today->timing!=''?count(json_decode($today->timing)):0;
                if ($today_meetings > 0 ) 
                foreach (json_decode($today->timing)  as $one) {
                    if ($one->t == 'onsite') $today_onsite_meetings++;
                    if ($one->t == 'online') $today_online_meetings++;
                }
            }
        }

        $upcoming = Schedule::where('date',">=", date('Y-m-d'))->where('mission_id', $mission_id)->get();
        return view('include.schedule.list',compact('user','page_title','schedules','holidays','rescheduled',
                                    'today_meetings','today_onsite_meetings','today_online_meetings','upcoming'));
    }

    public function schedule_addsingle()
    {
        $user = Auth::user();        
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;

        $page_title = __('Add a Schedule');            
        $sel_date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');
        $single_range = 'single';

        $old_sch = Schedule::where('date', $sel_date)->where('mission_id', $mission_id)->get()->first();
        $weekday = strtolower(Carbon::parse($sel_date)->format('l'));
        $default_timings = ScheduleTiming::where('mission_id',$mission_id)->where('weekday', $weekday)->get();
        $holiday = Holiday::where('holiday_date', $weekday)->where('mission_id', $mission_id)->get()->first();
        return view('include.schedule.add_single',compact('user','page_title','sel_date','single_range','default_timings','holiday','old_sch'));
    }
    public function schedule_reschedule()
    {
        $user = Auth::user();        
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Edit a Schedule');            
        $sel_date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');

        $schedule = Schedule::where('date', $sel_date)->where('mission_id', $mission_id)->get()->first();
        $weekday = strtolower(Carbon::parse($sel_date)->format('l'));
        $default_timings = ScheduleTiming::where('mission_id',$mission_id)->where('weekday', $weekday)->get();
        $holiday = Holiday::where('holiday_date', $weekday)->where('mission_id', $mission_id)->get()->first();
        return view('include.schedule.edit_reschedule_single',compact('user','page_title','sel_date','schedule','default_timings','holiday'));
    }

    public function schedule_addrange()
    {
        $user = Auth::user();        
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Add a Schedule');            
        $st_date = isset($_GET['date_start'])?$_GET['date_start']:date('Y-m-d');
        $ed_date = isset($_GET['date_end'])?$_GET['date_end']:date('Y-m-d');
        $single_range = 'range';        
        $period = CarbonPeriod::create($st_date, $ed_date);
        $total_schedules = 0;
        foreach ($period as $one_p) {
            $tp = Schedule::select()->where('date', $one_p)->where('mission_id', $mission_id)->get()->first();
            if ($tp) $total_schedules++;
        }
        if ($total_schedules ==  count($period)) 
            $old_sch = true;
        else 
            $old_sch = false;

        $default_timings = ScheduleTiming::where('mission_id',$mission_id)->where('mission_id', $mission_id)->get();

        $holiday = Holiday::all();
        return view('include.schedule.add_range',compact('user','page_title','st_date','ed_date','single_range','default_timings','holidays','old_sch'));
    }


    public function edit()
    {
        $user = Auth::user();        
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Edit a Schedule');            
        $sel_date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');

        $schedule = Schedule::where('date', $sel_date)->where('mission_id', $mission_id)->get()->first();
        $weekday = strtolower(Carbon::parse($sel_date)->format('l'));
        $default_timings = ScheduleTiming::where('mission_id',$mission_id)->where('weekday', $weekday)->get();
        $holiday = Holiday::where('holiday_date', $weekday)->where('mission_id', $mission_id)->get()->first();
        return view('include.schedule.edit',compact('user','page_title','sel_date','schedule','default_timings','holiday'));
    }
    public function schedule_save(Request $request)
    {
        $single_range = $request['single_range'];
        if ($single_range == 'single') $this->saveSingleSchedule($request->all());
        if ($single_range == 'range') $this->saveRangeSchedule($request->all());
        if ($single_range == 'reschedule') $this->saveReSchedule($request->all());        

        return back()->with('success',__('Created Schedule successfully'));
    }

    public function check_reschedule($sch, $request)
    {
        if ($sch->slots != $request->no_slots) return true;
        if ($sch->isDefault && $request->slots_val_optoins == 'custom') return true;
        if (!$sch->isDefault && $request->slots_val_optoins == 'default') return true;
        return false;        
    }
    public function schedule_update(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $sch_date = $request['schedule_date'];
        $sch = Schedule::where('date', $sch_date)->where('mission_id', $mission_id)->get()->first();
        $no_slots = $request['no_slots'];
        $slot_option = $request['slots_val_optoins'];
        $holiday_option = $request['holiday_val_optoins'];   

        if ($slot_option == 'default') 
            $slots = null;
        else    
            $slots = $request['slots'];

        $json_timings = [];
        if ($slots!= null) {
            foreach ($slots as $slot) {
                $tp = explode('|',$slot);
                if ($tp[1] !='' && $tp[2] != '' && $tp[3]!='' && $tp[4]!='') {
                    $json_timings[] = [
                        'i' => $tp[0],
                        's' => $tp[1],
                        'e' => $tp[2],
                        'd' => $tp[3],
                        't' => $tp[4] 
                    ];
                }
            }
        }            

        
        //$reschedule = isset($request['reschedule'])?true:false;
        //Check reschedule or not
        $reschedule = $this->check_reschedule($sch, $request);
   
        $hol_date = Holiday::where('holiday_date', strtolower(Carbon::parse($sch_date)->format('Y-m-d')))->where('mission_id', $mission_id)->get()->first();
        $hol_weekday = Holiday::where('holiday_date', strtolower(Carbon::parse($sch_date)->format('l')))->where('mission_id', $mission_id)->get()->first();
        $isHoliday = false;
        if($hol_date || $hol_weekday) $isHoliday = true;
        if ($holiday_option == 'default') {
            // Use default Holiday setting
            if ($isHoliday) {
                Schedule::where('date', $sch_date)->where('mission_id', $mission_id)->update([
                    'slots' => $request['no_slots'],
                    'timings' => '',
                    'isDefault' => true,
                    'isHoliday' => true,
                    'isReschedule' => $reschedule,
                    'isUseDefault' => true,
                ]);
            } else {                
                Schedule::where('date', $sch_date)->where('mission_id', $mission_id)->update([
                    'slots' => $no_slots,
                    'timings' => $slot_option=='custom'&&$slots&&count($slots)>0?json_encode($json_timings):'',
                    'isDefault' => $slot_option=='default'?true:false,
                    'isHoliday' => false,
                    'isReschedule' => $reschedule,
                    'isUseDefault' => true,
                ]);
            }
        }  else {
            //Ignore Holiday
            Schedule::where('date', $sch_date)->where('mission_id', $mission_id)->update([              
                'slots' => $no_slots,
                'timings' => $slots&&count($slots)>0?json_encode($json_timings):'',
                'isDefault' => false,
                'isHoliday' => false,
                'isReschedule' => $reschedule,
                'isUseDefault' => false,
            ]);
        }   

        //notify to users
        $msg = '';
        if( app()->getLocale() == 'en')
            $msg = 'Schedule updated by ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'Date '. $sch_date ."\r\n";
        else
            $msg = 'تم تحديث الجدول بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'تاريخ '. $sch_date ."\r\n";

        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $sch,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sch,  $msg);
        $event_msg = '';
        if( app()->getLocale() == 'en')
        $event_msg = 'Schedule updated by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Date '. $sch_date ."<br>";                  
        else
        $event_msg = 'تم تحديث الجدول بواسطة' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'تاريخ '. $sch_date ."<br>";          
                
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));
         

        if ($reschedule)
            return back()->with('success',__('Rescheduled successfully'));
        else
            return back()->with('success',__('Schedule updated successfully'));
    }

    public function schedule_delete(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $s_date = $request['s_date'];
        $sch = Schedule::where('date',$s_date)->where('mission_id', $mission_id)->get()->first();

        //notify to users
        $msg = '' ;
        if (app()->getLocale() == 'en')
        $msg = 'Schedule deleted by ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'Date '. $s_date ."\r\n";
        else
        $msg = 'تم حذف الجدول الزمني بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'تاريخ '. $s_date ."\r\n";
        
        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $sch,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sch,  $msg);
                      
        $event_msg = '' ;
        if (app()->getLocale() == 'en')
        $event_msg = 'Schedule deleted by ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'Date '. $s_date ."\r\n";
        else
        $event_msg = 'تم حذف الجدول الزمني بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'تاريخ '. $s_date ."\r\n";
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        Schedule::where('date',$s_date)->where('mission_id', $mission_id)->delete(); 

        return back()->with('success',__('Schdules on').' '. date('Y-m-d', strtotime($request['s_date'])). '  '.__('deleted successfully'));
    }

    public function schedule_easy_delete(Request $request)
    {
        $option = $request->reset_option;
        $start = $request->date_start;
        $end = $request->date_end;

        if ($option == 1) {
            Schedule::truncate();
        } else {
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $one) {
                if (Schedule::where('date', Carbon::parse($one)->format('Y-m-d'))->get()->first())
                    Schedule::where('date',Carbon::parse($one)->format('Y-m-d'))->delete();
            }
        }
        return back()->with('success',__('Schdules deleted successfully'));
    }
    public function saveSingleSchedule($request) 
    {
        
        $user = Auth::user();              
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $sch = null;
        $sch_date = $request['schedule_date'];
        $no_slots = $request['no_slots'];
        $slot_option = $request['slots_val_optoins'];
        $holiday_option = $request['holiday_val_optoins'];
        if ($slot_option == 'default') 
            $slots = null;
        else
            $slots = $request['slots'];

        $json_timings = [];
        if ($slots!= null) {
            foreach ($slots as $slot) {
                $tp = explode('|',$slot);
                if ($tp[1] !='' && $tp[2] != '' && $tp[3]!='' && $tp[4]!='') {
                    $json_timings[] = [
                        'i' => $tp[0],
                        's' => $tp[1],
                        'e' => $tp[2],
                        'd' => $tp[3],
                        't' => $tp[4] 
                    ];
                }
            }
        }            
        $hol_date = Holiday::where('holiday_date', strtolower(Carbon::parse($sch_date)->format('Y-m-d')))->where('mission_id', $mission_id)->get()->first();
        $hol_weekday = Holiday::where('holiday_date', strtolower(Carbon::parse($sch_date)->format('l')))->where('mission_id', $mission_id)->get()->first();
        $isHoliday = false;

        if($hol_date || $hol_weekday) $isHoliday = true;
        if ($holiday_option == 'default') {
            // Use default Holiday setting
            if ($isHoliday) {
                $sch = Schedule::create([
                    'user_id' => $user->id,
                    'mission_id' => $mission_id,
                    'consultant_id' => $user->role=='consul'?$user->consultant->id:$user->secretary->consultant->id,
                    'date' => $sch_date,
                    'weekday' => strtolower(Carbon::parse($sch_date)->format('l')),
                    'slots' => 0,
                    'timings' => '',
                    'isDefault' => true,
                    'isHoliday' => true,
                    'isReschedule' => false,
                    'isUseDefault' => true,
                ]);
            } else {                
                $sch = Schedule::create([
                    'user_id' => $user->id,
                    'mission_id' => $mission_id,
                    'consultant_id' => $user->role=='consul'?$user->consultant->id:$user->secretary->consultant->id,
                    'date' => $sch_date,
                    'weekday' => strtolower(Carbon::parse($sch_date)->format('l')),
                    'slots' => $no_slots,
                    'timings' => $slots&&count($slots)>0?json_encode($json_timings):'',
                    'isDefault' => $slot_option=='default'?true:false,
                    'isHoliday' => false,
                    'isReschedule' => false,
                    'isUseDefault' => true,
                ]);
            }
        }  else {
            //Ignore Holiday
            $sch = Schedule::create([
                'user_id' => $user->id,
                'mission_id' => $mission_id,
                'consultant_id' => $user->role=='consul'?$user->consultant->id:$user->secretary->consultant->id,
                'date' => $sch_date,
                'weekday' => strtolower(Carbon::parse($sch_date)->format('l')),
                'slots' => $no_slots,
                'timings' => $slots&&count($slots)>0?json_encode($json_timings):'',
                'isDefault' => false,
                'isHoliday' => false,
                'isReschedule' => false,
                'isUseDefault' => false,
            ]);
        }      

        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en')
        $msg = 'New Schedule created by ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'Date '. $sch_date ."\r\n";
        else 
        $msg = 'تم إنشاء جدول جديد بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'تاريخ '. $sch_date ."\r\n";

        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $sch,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sch,  $msg);

        $event_msg = '';
        if (app()->getLocale() == 'en')
        $event_msg = 'New Schedule created by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Date '. $sch_date ."<br>";                  
        else 
        $event_msg = 'تم إنشاء جدول جديد بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "<br>"
        . 'تاريخ '. $sch_date ."<br>";                  
                
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));
                
    }
    public function saveRangeSchedule($request) 
    {     
        $user = Auth::user();              
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $sch_date_start = $request['range_start'];
        $sch_date_end = $request['range_end'];
        $sch = null;
        $no_slots = $request['no_slots'];
        $slot_option = $request['slots_val_optoins'];
        $holiday_option = $request['holiday_val_optoins'];
        if ($slot_option == 'default') 
            $slots = null;
        else
            $slots = $request['slots'];
        $json_timings = [];
        if ($slots!= null) {
            foreach ($slots as $slot) {
                $tp = explode('|',$slot);
                $json_timings[] = [
                    'i' => $tp[0],
                    's' => $tp[1],
                    'e' => $tp[2],
                    'd' => $tp[3],
                    't' => $tp[4] 
                ];
            }
        }

        $period = CarbonPeriod::create($sch_date_start, $sch_date_end);            
        foreach ($period as $one_sch_date) {
            if ($holiday_option == 'default') {
                $hd = Holiday::where('mission_id', $mission_id)->where('holiday_date',strtolower(Carbon::parse($one_sch_date)->format('l')))
                        ->orWhere('holiday_date',strtolower(Carbon::parse($one_sch_date)->format('Y-m-d')))->get()->first();                
                $sch = Schedule::create([
                    'user_id' => $user->id,
                    'mission_id' => $mission_id,
                    'consultant_id' => $user->role=='consul'?$user->consultant->id:$user->secretary->consultant->id,
                    'date' => Carbon::parse($one_sch_date)->format('Y-m-d'),
                    'weekday' => strtolower(Carbon::parse($one_sch_date)->format('l')),
                    'slots' => $no_slots,
                    'timings' => !$hd&&$slots&&count($slots)>0?json_encode($json_timings):'',
                    'isDefault' => $slot_option=='default'?true:false,
                    'isHoliday' => $hd?true:false,
                    'isReschedule' => false,
                    'isUseDefault' => true,
                ]);
            } else {
                $sch =  Schedule::create([
                    'user_id' => $user->id,
                    'mission_id' => $mission_id,
                    'consultant_id' => $user->role=='consul'?$user->consultant->id:$user->secretary->consultant->id,
                    'date' => Carbon::parse($one_sch_date)->format('Y-m-d'),
                    'weekday' => strtolower(Carbon::parse($one_sch_date)->format('l')),
                    'slots' => $no_slots,
                    'timings' => $slots&&count($slots)>0?json_encode($json_timings):'',
                    'isDefault' => false,
                    'isHoliday' => false,
                    'isReschedule' => false,
                    'isUseDefault' => false,
                ]);
            }
        }  
      


        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en')
        $msg = 'New Schedule created by ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'Start Date '. $sch_date_start ."\r\n"
            . 'End Date: '. $sch_date_end."\r\n";     
        else
        $msg = 'تم إنشاء جدول جديد بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'تاريخ البدء '. $sch_date_start ."\r\n"
        . 'تاريخ الانتهاء : '. $sch_date_end."\r\n";   

        $consuls = Consultant::get_mission_users($mission_id);
        $secrets = Secretary::get_mission_users($mission_id);

        foreach ($consuls as $one)  $this->notification_open($one, $sch,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sch,  $msg);

        $event_msg = ''; 
        if (app()->getLocale() == 'en')
        $event_msg = 'New Schedule created by ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'Start Date '. $sch_date_start ."\r\n"
            . 'End Date: '. $sch_date_end."\r\n";     
        else
        $event_msg = 'تم إنشاء جدول جديد بواسطة ' 
        . $user->userinfo->fname. ' '
        . $user->userinfo->lname. "\r\n"
        . 'تاريخ البدء '. $sch_date_start ."\r\n"
        . 'تاريخ الانتهاء : '. $sch_date_end."\r\n";                   
        event(new MissionNotify( $mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

    }
    public function saveReSchedule($date) { }    

    public function notification_open($receiver, $handler,   $msg, $type='schedule')
    {
        
        if ($type == 'schedule') {
            if($receiver->notify_email)
                Notification::route('mail', $receiver->email)->notify( new ScheduleCreated('mail', $receiver->email, $msg));
            if($receiver->notify_phone)
                Notification::send( $handler, new ScheduleCreated('sms',$receiver->phone, $msg));            
            if($receiver->notify_whatsapp)
                Notification::send( $handler, new ScheduleCreated('whatsapp',$receiver->whatsapp,$msg));         
        }
        if ($type == 'holiday') {
            if($receiver->notify_email)
                Notification::route('mail', $receiver->email)->notify( new HolidayCreated('mail', $receiver->email, $msg));
            if($receiver->notify_phone)
                Notification::send( $handler, new HolidayCreated('sms',$receiver->phone, $msg));            
            if($receiver->notify_whatsapp)
                Notification::send( $handler, new HolidayCreated('whatsapp',$receiver->whatsapp,$msg));         
        }        
        if ($type == 'time') {
            if($receiver->notify_email)
                Notification::route('mail', $receiver->email)->notify( new ScheduleTimingCreated('mail', $receiver->email, $msg));
            if($receiver->notify_phone)
                Notification::send( $handler, new ScheduleTimingCreated('sms',$receiver->phone, $msg));            
            if($receiver->notify_whatsapp)
                Notification::send( $handler, new ScheduleTimingCreated('whatsapp',$receiver->whatsapp,$msg));         
        }    
    }


}
