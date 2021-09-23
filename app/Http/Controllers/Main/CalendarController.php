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

use App\Client;
use App\ClientVerify;
use App\Booking;
use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalendarController extends Controller
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

    public function index_old()
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Calendar');

        $array_schedule = [];            
        $schedules = Schedule::where('mission_id', $mission_id)->get();
        foreach ($schedules as $one) {
            if ($one->isDefault) {
                //Default timing
                if($one->isHoliday) {
                    $elmenet = [];
                    $element['id'] = strtotime($one->date);                         
                    $element['title'] = 'Holiday';
                    $element['start'] = $one->date.'T00:00:00';
                    $element['end'] = $one->date.'T23:59:59';
                    /* $element['display'] = "block"; */
                    $element['editable'] = true;
                    $element['borderColor'] = "#ffbc34";
                    $element['backgroundColor'] = "#ffbc34"; //#009efb
                    $array_schedule[] = $element;
                } else {
                    $timings = ScheduleTiming::where('weekday', $one->weekday)->where('mission_id', $mission_id)->get();
                    foreach ($timings as $time) {
                        $elmenet = [];
                        $element['id'] = strtotime($one->date. ' ' .$time->start) .'_'.$time->id;                         
                        $element['title'] = Carbon::parse($time->start)->format('g:i A') . ' ~ ' . Carbon::parse($time->end)->format('g:i A');
                        $element['start'] = $one->date.'T'.$time->start;
                        $element['end'] = $one->date.'T'.$time->end;
                        $element['display'] = "block";
                        $element['borderColor'] = $time->type?"#09e5ab":"#009efb";
                        $element['backgroundColor'] = $time->type?"#09e5ab":"#009efb";
                        $element['editable'] = true;
                        $array_schedule[] = $element;
                    }                  
                }
            } else {
                if ($one->timings != '') {
                    $timings = json_decode($one->timings);
                    foreach ($timings as $time) {
                        $elmenet = [];
                        $element['id'] = strtotime($one->date. ' ' .$time->s) .'_'.$time->i;                         
                        $element['title'] = Carbon::parse($time->s)->format('g:i A') . ' ~ ' . Carbon::parse($time->e)->format('g:i A');
                        $element['start'] = $one->date.'T'.$time->s;
                        $element['end'] = $one->date.'T'.$time->e;
                        $element['display'] = "block";
                        $element['borderColor'] = $time->t=='onsite'?"#09e5ab":"#009efb";
                        $element['backgroundColor'] = $time->t=='onsite'?"#09e5ab":"#009efb";
                        $element['editable'] = true;
                        $array_schedule[] = $element;
                    } 
                }
                   
            }
        }
        $json_schedule = json_encode($array_schedule);


        return view('include.calendar.list',compact('user','page_title','json_schedule'));
    }


    public function index()
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Calendar');

        $array_schedule = [];            
        $schedules = Schedule::where('mission_id', $mission_id)->get();
        foreach ($schedules as $one) {
            if ($one->isDefault) {
                //Default timing
                if($one->isHoliday) {
                    $elmenet = [];
                    $element['id'] = strtotime($one->date);                         
                    $element['summary'] = 'Holiday';
                    $element['startDate'] = $one->date.'T00:00:00';
                    $element['endDate'] = $one->date.'T23:59:59';
                    $element['bgColor'] = "bg-warning";
                    $element['isHoliday'] = $one->isHoliday;
                    $element['meetingType'] = 'Holiday';
                    $element['bookings'] = [];
                    $element['slots'] = 0;
                    $array_schedule[] = $element;
                } else {
                    $timings = ScheduleTiming::where('weekday', $one->weekday)->where('mission_id', $mission_id)->get();
                    foreach ($timings as $time) {
                        $elmenet = [];
                        $element['id'] = strtotime($one->date. ' ' .$time->start) .'_'.$time->id;                         
                        $element['summary'] = ( Carbon::parse($time->start)->format('g:i A') . ' ~ ' . Carbon::parse($time->end)->format('g:i A') );
                        $element['startDate'] = $one->date.'T'.$time->start;
                        $element['endDate'] = $one->date.'T'.$time->end;
                        $element['bgColor'] = $time->type?"bg-primary":"bg-info";
                        $element['isHoliday'] = $one->isHoliday;
                        $element['meetingType'] = $time->type?'Onsite Meeting':'Online Meeting';
                        $element['slots'] = $one->slots;
                        $bookings = Booking::select('bookings.id as bkid','bookings.status as bks','clients.fname as cfname','clients.lname as clname')
                                        ->leftjoin('clients','clients.id','=','bookings.client_id')
                                        ->where('bookings.mission_id', $mission_id)
                                        ->where('bookings.timing_id', $time->id)
                                        ->where('bookings.schedule_date', $one->date)
                                        ->get();
                        $element['viewLink'] = url('/appointments/viewbooking/');
                        $element['bookings'] = $bookings;                        

                        $array_schedule[] = $element;
                    }                  
                }
            } else {
                if ($one->timings != '') {
                    $timings = json_decode($one->timings);
                    foreach ($timings as $time) {
                        $elmenet = [];
                        $element['id'] = strtotime($one->date. ' ' .$time->s) .'_'.$time->i;                         
                        $element['summary'] = ( Carbon::parse($time->s)->format('g:i A') . ' ~ ' . Carbon::parse($time->e)->format('g:i A'));
                        $element['startDate'] = $one->date.'T'.$time->s;
                        $element['endDate'] = $one->date.'T'.$time->e;
                        $element['bgColor'] = $time->t=='onsite'?"bg-primary":"bg-info";
                        $element['isHoliday'] = $one->isHoliday;
                        $element['meetingType'] = $time->t=='onsite'?'Onsite Meeting':'Online Meeting';

                        $bookings = Booking::select('bookings.id as bkid','bookings.status as bks','clients.fname as cfname','clients.lname as clname')
                                        ->leftjoin('clients','clients.id','=','bookings.client_id')
                                        ->where('bookings.mission_id', $mission_id)
                                        ->where('bookings.schedule_date', $one->date)
                                        ->where('bookings.timing_id', $time->i)->get();      
                        $element['viewLink'] = url('/appointments/viewbooking/');                                                                                              
                        $element['slots'] = $one->slots;
                        $element['bookings'] = $bookings;                        

                        $array_schedule[] = $element;
                    } 
                }
                   
            }
        }
        $holidays = Holiday::all();
        foreach ($holidays as $one) {
            $elmenet = [];
            $element['id'] = strtotime($one->holiday_date);                         
            $element['summary'] = 'Holiday';
            $element['startDate'] = $one->holiday_date.'T00:00:00';
            $element['endDate'] = $one->holiday_date.'T23:59:59';
            $element['bgColor'] = "bg-warning";
            $element['isHoliday'] = true;
            $element['meetingType'] = 'Holiday';
            $element['slots'] = 0;
            $array_schedule[] = $element;
        }


        $json_schedule = json_encode($array_schedule);

        return view('include.calendar.list',compact('user','page_title','json_schedule'));
    }
}