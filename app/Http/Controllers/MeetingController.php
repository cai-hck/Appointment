<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Mission;
use App\Consultant;
use App\Secretary;
use App\Section;
use App\Schedule;
use App\ScheduleTiming;
use App\Holiday;
use App\Client as FrontendClient;
use App\ClientVerify;
use App\Booking;
use App\BookingFile;
use App\AddLink;
use App\MeetingRoom;
use Carbon\Carbon;


use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use Twilio\Rest\Client;
class MeetingController extends Controller
{

    public function __construtor() 
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
    }

    public function index($base)
    {
        $meeting = MeetingRoom::where('meeting_url','/online-meeting/'.$base)->get()->first();     
        $consul = Consultant::find($meeting->consultant_id);
        $booking = Booking::find($meeting->booking_id);        
        return view('meeting',compact('meeting','consul','booking'));
    }
}