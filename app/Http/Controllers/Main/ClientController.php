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

class ClientController extends Controller
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
        $page_title = __('Clients');

        $all_clients = FrontendClient::where('mission_id', $mission_id)->get()->count();
        $today_clients = Booking::where('mission_id', $mission_id)->where('schedule_date', date('Y-m-d'))->get()->count();
        $clients = FrontendClient::where('mission_id', $mission_id)->get();
        return view('include.client.list',compact('user','page_title','all_clients','today_clients','clients'));
    }
    
    public function client_view($id)
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;

        $client = FrontendClient::find($id);
        $page_title = __('Client Detail') .' - ' .$client->fname . ' ' . $client->lname;

        $all_apps = $client->bookings->count();
        $today_apps = Booking::where('mission_id', $mission_id)->where('client_id', $client->id)->where('schedule_date', date('Y-m-d'))->get()->count();

        $bookings = Booking::where('mission_id', $mission_id)->where('client_id', $client->id)->get();
        return view('include.client.view',compact('user','page_title','all_apps','today_apps','bookings'));
    }
}