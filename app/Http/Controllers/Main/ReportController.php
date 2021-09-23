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
use App\Client as FrontendClient;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use App\ClientVerify;
use App\Booking;

use Twilio\Exceptions\TwilioException;

class ReportController extends Controller
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

    public function index()
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Report');

        $start = isset($_GET['start_date'])?$_GET['start_date']: '';
        $end = isset($_GET['end_date'])?$_GET['end_date']: '';
    
        $bookings = [];
            if ($start == '' && $end == '') {
                $bookings['all'] = Booking::where('mission_id', $mission_id)->get()->count();
                $bookings['finished'] = Booking::where('status','finished')->where('mission_id', $mission_id)->get()->count();
                $bookings['upcoming'] = Booking::where('status','approved')->where('mission_id', $mission_id)->get()->count();
                $bookings['declined'] = Booking::where('status','declined')->where('mission_id', $mission_id)->get()->count();
            } else {
                $bookings['all'] = Booking::where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
                $bookings['finished'] = Booking::where('status','finished')->where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
                $bookings['upcoming'] = Booking::where('status','approved')->where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
                $bookings['declined'] = Booking::where('status','declined')->where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
            }

        $today_bookings = [];
            $today_bookings['all'] = Booking::where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $today_bookings['finished'] = Booking::where('status','finished')->where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $today_bookings['upcoming'] = Booking::where('status','approved')->where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $today_bookings['declined'] = Booking::where('status','declined')->where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();


        $clients = [];
            
            if ($start == '' && $end == '') {
                $clients['all'] = Booking::where('mission_id', $mission_id)->get()->count();
                $clients['onsite'] = Booking::where('type','Onsite')->where('mission_id', $mission_id)->get()->count();
                $clients['online'] = Booking::where('type','Online')->where('mission_id', $mission_id)->get()->count();
            } else {
                $clients['all'] = Booking::where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
                $clients['onsite'] = Booking::where('type','Onsite')->where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
                $clients['online'] = Booking::where('type','Online')->where('mission_id', $mission_id)->where('schedule_date' ,'>=', $start )->where('schedule_date', '<=', $end)->get()->count();
            }

        $today_clients = [];
            $today_clients['all'] = Booking::where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $today_clients['onsite'] = Booking::where('type','Onsite')->where('schedule_date', date('Y-m-d'))->where('mission_id', $mission_id)->get()->count();
            $today_clients['online'] = Booking::where('type','Online')->where('schedule_date', date('Y-m-d'))->where('mission_id', $mission_id)->get()->count();            


        return view('include.report.view',compact('user','page_title','bookings','today_bookings','clients','today_clients','start','end'));
    }


}