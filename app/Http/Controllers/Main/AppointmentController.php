<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\Section;
use App\Consultant;
use App\Secretary;
use App\Schedule;
use App\ScheduleTiming;
use App\Holiday;

use App\Client;
use App\ClientVerify;
use App\Booking;
use App\AddLink;
use App\MeetingRoom;
use App\ChatRoom;
use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

use Twilio\Rest\Client as TwilioClient;
use Twilio\Jwt\ClientToken;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentBooked;
use App\Events\MissionNotify;

class AppointmentController extends Controller
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
        $page_title = __('Booking Appointments');
        
        $start = isset($_GET['date_start'])?$_GET['date_start']:'';
        $end = isset($_GET['date_end'])?$_GET['date_end']:'';

        if ($start!='' && $end !='' ) {
            $bookings = Booking::where('mission_id', $mission_id)->where('schedule_date','>=', $start)->where('schedule_date','<=',$end)->get();                
            $finished_bookings = Booking::where('mission_id', $mission_id)->where('schedule_date','>=', $start)->where('schedule_date','<=',$end)->where('status','finished')->get()->count();
            $declined_bookings = Booking::where('mission_id', $mission_id)->where('schedule_date','>=', $start)->where('schedule_date','<=',$end)->where('status','declined')->get()->count();
        } else {
            $bookings = Booking::where('mission_id', $mission_id)->get();                
            $finished_bookings = Booking::where('mission_id', $mission_id)->where('status','finished')->get()->count();
            $declined_bookings = Booking::where('mission_id', $mission_id)->where('status','declined')->get()->count();
        }
        $today_bookings = Booking::where('mission_id', $mission_id)->where('schedule_date',date('Y-m-d'))->get()->count();
        return view('include.booking.list', compact('user','page_title','bookings','finished_bookings','today_bookings','declined_bookings','start','end'));
    }
    public function view_booking_detail($id)
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('View Booking Detail');

        $booking = Booking::find($id);
        $extra_links = AddLink::where('booking_id', $booking->id)->get();
        //$meeting = MeetingRoom::where('booking_id', $booking->id)->get()->first();
        $meeting = ChatRoom::where('booking_id', $booking->id)->get()->first();
        return view('include.booking.detail', compact('user','page_title','booking','extra_links','meeting'));
    }

    public function booking_decline(Request $request)
    {
        $decline_reason = $request['reason'];
        $user = Auth::user();
        $booking = Booking::find($request['bk_id']);
        $booking->status = 'declined';
        $booking->decline_reason = $decline_reason;
        $booking->save();


        $client = Client::where('id', $booking->client_id)->get()->first();
        $mission = Mission::where('id', $booking->mission_id)->get()->first();
        $section = Section::where('id', $booking->section_id)->get()->first();
        //Notify to Client through whtasapp phone, email, 

        $msg = '';
        if ($booking->lang == 'en') 
            $msg = 'Your appointment has been declined'."\r\n"
               .'Mission: '. $mission->name ."\r\n"
               .'Section: '. $section->en_name ."\r\n"
               .'Schedule Date: '. $booking->schedule_date."\r\n"
               .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
               .'Type: '. $booking->type .' Meeting'."\r\n"  
               .'Reason:'. $decline_reason."\r\n";
        else
            $msg = 'تم رفض موعدك '."\r\n"
            .'مهمة: '. $mission->name_ar ."\r\n"
            .'الجزء: '. $mission->ar_name ."\r\n"
            .'تاريخ الجدول الزمني: '. $booking->schedule_date."\r\n"
            .'وقت: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
            .'اكتب: '. $booking->type .' '."\r\n"
            .'سبب:'. $decline_reason."\r\n";
        
        
        
        $this->notification_open($client, $booking, $msg);
        //Alert to Users
        $event_msg = '';
        if ($booking->lang == 'en') 
            $event_msg = 'Appointment has been cancelled by '. $user->userinfo->fname. ' '.$user->userinfo->lname.'<br>'
                    .'Mission: '. $mission->name .'<br>'
                    .'Section: '. $mission->en_name .'<br>'
                    .'Schedule Date: '. $booking->schedule_date. '<br>'
                    .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A').'<br>'
                    .'Type: '. $booking->type .' Meeting'.'<br>';               
        else
            $event_msg = 'تم إلغاء الموعد من قبل '. $user->userinfo->fname. ' '.$user->userinfo->lname.'<br>'
            .'مهمة: '. $mission->name_ar .'<br>'
            .'الجزء: '. $mission->ar_name .'<br>'
            .'تاريخ الجدول الزمني : '. $booking->schedule_date. '<br>'
            .'وقت: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A').'<br>'
            .'اكتب: '. $booking->type .' '.'<br>';               

        event(new MissionNotify( $booking->mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));


        return back()->with('success',__('Declined appiontment successfully'));
    }

    public function finish_meeting(Request $request)
    {
        $user = Auth::user();

        $bk_id = $request['bk_id'];
        $booking = Booking::find($bk_id);
        Booking::where('id', $bk_id)->update([
            'status'=>'finished',
            'finish_time' => date('Y-m-d H:i:s')
        ]);

        // Notify to Clients & Mission users
        $client = Client::where('id', $booking->client_id)->get()->first();
        $mission = Mission::where('id', $booking->mission_id)->get()->first();
        $section = Section::where('id', $booking->section_id)->get()->first();

        //Notify to Client through whtasapp phone, email, 
        $msg = '';
        if ($booking->lang == 'en') 
            $msg = 'Meeting has been finished'."\r\n"
                .'Mission: '. $mission->name ."\r\n"
                .'Section: '. $section->en_name ."\r\n"
                .'Schedule Date: '. $booking->schedule_date."\r\n"
                .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
                .'Type: '. $booking->type .' Meeting'."\r\n";  
        else
            $msg = 'تم الانتهاء من الاجتماع'."\r\n"
                .'مهمة: '. $mission->name_ar ."\r\n"
                .'الجزء: '. $section->ar_name ."\r\n"
                .'تاريخ الجدول الزمني : '. $booking->schedule_date."\r\n"
                .'وقت: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
                .'اكتب: '. $booking->type .' Meeting'."\r\n";  
                             
         $this->notification_open($client, $booking, $msg);
         //Alert to Users
         $event_msg = '';
         if ($booking->lang == 'en')
            $event_msg = 'Meeting has been finished with '.$client->fname. ' '. $client->lname.'<br>'
                     .'Mission: '. $mission->name .'<br>'
                     .'Section: '. $section->en_name .'<br>'
                     .'Schedule Date: '. $booking->schedule_date. '<br>'
                     .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A').'<br>'
                     .'Type: '. $booking->type .' '.'<br>';               
        else
            $event_msg = 'تم الانتهاء من الاجتماع مع '.$client->fname. ' '. $client->lname.'<br>'
            .'مهمة: '. $mission->name_ar .'<br>'
            .'الجزء: '. $section->ar_name .'<br>'
            .'تاريخ الجدول الزمني : '. $booking->schedule_date. '<br>'
            .'وقت: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A').'<br>'
            .'اكتب: '. $booking->type .' '.'<br>';               

         event(new MissionNotify( $booking->mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        return back()->with('success',"Meeting finished successfully");
    }
    public function ask_booking_file(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role == 'consul'?$user->consultant->mission->id:$user->secretary->mission->id;               
        
        $consultant_id = $user->role == 'consul'?$user->consultant->id:$user->secretary->consultant->id;

        $about = $request['about'];
        $via = $request['via'];

        $booking =  Booking::find($request['bk_id']);

        $returnflag = false;

        if ($via == 'email')
        {
            //Send Email
            $returnflag = $this->send_email($booking, $about);
        } else {
            //$returnflag = $this->send_message($via, $booking, $about);
        }        

        //Generate Extra file Link
        AddLink::create([
            'booking_id'=>$booking->id,
            'about'=>$about,
            'url'=>url('extra/'.base64_encode($booking->id).'/booking/'.base64_encode($booking->client_id).'/'.base64_encode($booking->mission_id).'/'.base64_encode($booking->section_id)),
            'status'=>'pending'
        ]);

        //notify to users
        $msg =  $user->userinfo->fname. ' ' .$user->userinfo->lname .' requested '. $booking->client->fname. ' ' .$booking->client->lname . 
                    'to upload more files'."<br>"; 

        event(new MissionNotify( $booking->mission_id, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $msg));

        return back()->with('success',__('Sent link to Client successfully'));
    }

    public function send_message($type, $booking, $about)
    {
        
        if ($type != 'phone') return true;
        
        
        if ($type == 'phone')
            $phone = $booking->client->phone;
        else
            $phone = $booking->client->whatsapp;        
            
        //$phone = '+15005550006';

        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $tphone     = config('app.twilio')['TWILIO_PHONE_NUMBER'];
        $whatsapp   = config('app.twilio')['TWILIO_WHATSAPP_FROM'];

        $client = new Client($accountSid, $authToken);
        
        try
        {

            // Use the client to do fun stuff like send text messages!
            $message = $client->messages
                    ->create($type=='phone'?$phone:'whatsapp:'.$phone, // to
                        [
                            "from" =>$type=='phone'?$tphone:$whatsapp,
                            "body" => $about
                        ]
                    );           
            return true;
        }
        catch (TwilioException  $e)
        {
           return false;
        }       
    }

    public function send_email($booking, $about)
    {
       Notification::route('mail', $booking->client->email)->notify( new AppointmentBooked('mail', $booking->client->email, $about));
       return true;
    }


    public function notification_open($receiver, $handler,   $msg)
    {
        Notification::route('mail', $receiver->email)->notify( new AppointmentBooked('mail', $receiver->email, $msg));
        Notification::send( $handler, new AppointmentBooked('sms',$receiver->phone, $msg));            
        Notification::send( $handler, new AppointmentBooked('whatsapp',$receiver->whatsapp,$msg));         
    }



}