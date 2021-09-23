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
use App\ScheduleOption;
use App\ScheduleSlot;
use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;
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
        $page_title = __('Schedule Timings');            
        return view('include.schedule.timingslots',compact('user','page_title'));
    }
    public function index()
    {
        $user = Auth::user();
        $page_title = __('Schedules');    
        $schedules = Schedule::where('mission_id',$user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id )->get();
        return view('include.schedule.list',compact('user','page_title','schedules'));
    }

    public function add()
    {
        $user = Auth::user();        
        $page_title = __('Add a Schedule');            
        $sel_date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');
        return view('include.schedule.add',compact('user','page_title','sel_date'));
    }

    public function edit()
    {
        $user = Auth::user();
        $page_title = __('Edit a Schedule');            
        $sel_date = isset($_GET['date'])?$_GET['date']:''; 
        $editable = false;
        $main_sch = false;
        $sch = $this->check_reschedulable($sel_date);
        if ($sch['flag']) {
            $editable = true;
            $schedule = Schedule::find($sch['sch_id']);
            $main_sch = $sch['main'];
        } else {
            $schedule = null;
        }



        return view('include.schedule.edit',compact('user','page_title','sel_date','editable','schedule','main_sch'));
    }
    public function schedule_save(Request $request)
    {
        $user = Auth::user();
        if ($request['date'] == null) return back()->with('error','Enter the schedule date correctly.');
        $no_slots = $request['no_slots'];
        if (Schedule::check_schedule($request['date'])) return back()->with('error','You already created schedule for '. $request['date']);
        
        $sch = Schedule::create([
                'user_id' => $user->id, 
                'mission_id'=> $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id,
                'date'=> $request['date'],
                'slots'=>$request['no_slots'],
                'duration' => 0,
        ]);        
        if ($request['options']) {
            $options = explode('|', $request['options']);
            $repeat_every = ['day'=>'day','week'=>'week','month' =>'month', 'custom' => 'custom'];
            $holiday = ['nothing'=>'nothing','holiday'=>'holiday','weekend'=>'weekend'];

            ScheduleOption::create([
                'schedule_id' => $sch->id,
                'repeat' => $options[0],
                'start_date' => $options[1],
                'end_date' => $options[2],
                'repeat_every' => $repeat_every[$options[3]],
                'repeat_custom' => $options[4]==''?0:$options[4],
                'relax' => $holiday[$options[5]],
            ]);            
        }
        if ($request['slots']) {
            $slots = $request['slots'];
            foreach ($slots as $slot) {
                $tp = explode('|',$slot);
                ScheduleSlot::create([
                    'schedule_id' => $sch->id,
                    'start_time' => $tp[1],
                    'end_time' => $tp[2],
                    'duration' => $tp[3],
                    'type' => $tp[4] 
                ]);
            }
        }
        //Notify to Secretary and Sub consultant

        return back()->with('success','Created new Schedule successfully');
    }

    public function schedule_update(Request $request)
    {
        $old_schid = isset($request['old_sch_id'])?$request['old_sch_id']:0;
        

        dd($request->all());
        return redirect('schedules')->with('success','Schedules have been changed successfully');
    }
    public function check_reschedulable($date)
    {

        $user = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $main_exist = Schedule::where('date', $date)->get()->first();

        if ($main_exist) return ['flag'=>true, 'sch_id'=>$one->id,'main'=>true];

        $selected_date_weekday = Carbon::parse($date)->format('l');
        $selected_date_day = Carbon::parse($date)->day;
        $selected_date_month = Carbon::parse($date)->month;
        $selected_date_year = Carbon::parse($date)->year;

        $my_schedules = Schedule::where('mission_id', $mission_id)->get();


        foreach ($my_schedules as $one) {
            if ($one->option) {
                $rp_start = strtotime($one->option->start_date);
                $rp_end = strtotime($one->option->end_date);
                if (strtotime($date) > $rp_start && strtotime($date) < $rp_end) {
                    switch ( $one->option->repeat_every ) {
                        case 'day':
                            # code...
                            return ['flag'=>true, 'sch_id'=>$one->id,'main'=>false];
                            break;
                        case 'week':
                            //how many weeks have
                            $period = CarbonPeriod::between($rp_start, $rp_end);
                            foreach ($period as $op )
                                if ($op->format('l') ==  $selected_date_weekday) return ['flag'=>true, 'sch_id'=>$one->id,'main'=>false];
                            # code...
                            break;                        
                        case 'month':                            
                            # code...
                            //how many months have
                            $period = CarbonPeriod::between($rp_start, $rp_end);
                            foreach ($period as $op )
                                if ($op->format('d') ==  $selected_date_day) return ['flag'=>true, 'sch_id'=>$one->id,'main'=>false];
                            break; 
                        case 'custom':                            
                            # code...
                            // how many days have
                            $repeat_custom = $one->option->repeat_custom;               
                            $period = CarbonPeriod::between($rp_start, $rp_end);
                            $days = [];
                            foreach ($period as $op ) { 
                                $days[] = $op;
                                if ($op == $date) return ['flag'=>true, 'sch_id'=>$one->id,'main'=>false];
                                $period->skip($repeat_custom);
                            }
                            break;                                                           
                    }
                }
            }
        }
        
        return ['flag'=>false, 'sch_id'=>null,'main'=>false];
    }

}

