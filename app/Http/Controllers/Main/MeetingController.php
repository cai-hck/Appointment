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
use App\Chat;
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
use App\Notifications\BookReminderNotice;
use App\Events\MissionNotify;
use App\Events\ChatEvent;
use App\Events\GroupScheduleEvent;


use Illuminate\Notifications\AnonymousNotifiable;
use Thomasjohnkane\Snooze\Traits\SnoozeNotifiable;
use Thomasjohnkane\Snooze\Models\ScheduledNotification;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;

class MeetingController extends Controller
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
        $page_title = __('Meetings');
        $start = isset($_GET['date_start'])?$_GET['date_start']:'';
        $end = isset($_GET['date_end'])?$_GET['date_end']:'';
        
        if ($start == '' && $end == '') 
            $schedules = Schedule::where('mission_id', $mission_id)->get();
        else
            $schedules = Schedule::where('mission_id', $mission_id)->where('date','>=', $start)->where('date', '<=',$end)->get();

        $meetings = [];

        foreach ($schedules as $one_schedule) {
            $tp = [];
            $tp['schedule'] = $one_schedule;
            $tp['slots'] = [];
            $tp['is_meetingday'] = false;
            if ($one_schedule->isDefault == true) {
                //get Defaults timings
                $times = ScheduleTiming::where('mission_id', $mission_id)->where('weekday', $one_schedule->weekday)->get();
                foreach ($times as $one_time) {
                    $bookings = Booking::where('mission_id', $mission_id)->where('schedule_date', $one_schedule->date)
                                ->where('start_time', $one_time->start)->where('end_time', $one_time->end)
                                ->where('type',$one_time->type?'Onsite':'Online')
                                ->get();
                    if (count($bookings) > 0) $tp['is_meetingday'] = true;
                    $tp['slots'][] = [
                        'start'=>$one_time->start,
                        'end'=>$one_time->end,
                        'type'=>$one_time->type?'onsite':'online',
                        'range'=>Carbon::parse($one_time->start)->format('g:i A'). ' ~ ' . Carbon::parse($one_time->end)->format('g:i A'),
                        'clients' => $bookings
                    ];
                }
            } else {
                //get Custom timings
                $times = json_decode($one_schedule->timings);
                foreach ($times as $one_time) {
                    $bookings = Booking::where('mission_id', $mission_id)->where('schedule_date', $one_schedule->date)
                                ->where('start_time', $one_time->s)->where('end_time', $one_time->e)
                                ->where('type',$one_time->t=='onsite'?'Onsite':'Online')
                                ->get();                    
                    if (count($bookings) > 0) $tp['is_meetingday'] = true;                                
                    $tp['slots'][] = [
                        'start'=>$one_time->s,
                        'end'=>$one_time->e,
                        'type'=>$one_time->t,
                        'range'=>Carbon::parse($one_time->s)->format('g:i A'). ' ~ ' . Carbon::parse($one_time->e)->format('g:i A'),
                        'clients' => $bookings
                    ];
                }
            }
            $meetings[] = $tp;
        }

        return view('include.meeting.index',compact('user','page_title','start','end','meetings'));
    }

    public function room_meeting($base)
    {
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Meetings') .'   '. Carbon::parse($base)->toDateString() 
                        .', ' .Carbon::parse($_GET['st'])->format('g:i A') 
                        .' ~ ' .Carbon::parse($_GET['dt'])->format('g:i A');

        $bookings = Booking::where('mission_id', $mission_id)
                            ->where('schedule_date', $base)
                            ->where('start_time', $_GET['st'])
                            ->where('end_time', $_GET['dt'])
                            ->orderby('created_at','asc')->get();

        // Remove finsiehd temporary client users
        User::leftjoin('chat_rooms','chat_rooms.room_client','=','users.id')
                    ->where('chat_rooms.status','finished')
                    ->where('users.role','client')
                    ->delete();
        return view('include.meeting.room',compact('user','page_title','bookings'));
    }

    public function single_meeting($bkid)
    {
        $user  = Auth::user();
        $booking = Booking::find($bkid);
        $client = Client::find($booking->client_id);
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Meetings') .'  with '.$client->fname. ' ' .$client->lname ;       
        $bookings = Booking::where('mission_id', $mission_id)
            ->where('schedule_date', $booking->schedule_date)
            ->where('start_time', $booking->start_time)
            ->where('end_time', $booking->end_time)
            ->orderby('created_at','asc')->get();

        return view('include.meeting.single',compact('user','page_title','booking','client','bookings'));
    }

    public function generate_meeting_url(Request $request)
    {
        $bkid = $request['bk_id'];
        $cid = $request['c_id'];
        $booking = Booking::find($bkid);
        $room_id = $this->generate_temporary_password();
        //Create Temporary Client User
        $client_user = User::create([
            'name' => strtolower($booking->client->fname),
            'email' => $booking->client->email,
            'password'=> bcrypt($room_id),
            'role' => 'client'
        ]);
        ChatRoom::create([
            'booking_id' => $booking->id,
            'room_client' => $client_user->id,
            'room_id' => $room_id,
        ]);        
        //Send notification right not and set Reminder Message/Email with Chatroom LINK before 1 day and 1 hour

        $client = Client::where('id', $booking->client_id)->get()->first();
        $mission = Mission::where('id', $booking->mission_id)->get()->first();
        $section = Section::where('id', $booking->section_id)->get()->first();
        $qrcode = asset($booking->qrcode);
        //Notify to User

        $msg = '';
        if ($booking->lang == 'en')
            $msg = 'Your Book appointment meeting room has been created successfully '."\r\n"
               .'You will have meeting with consultant'."\r\n"
               .'Mission: '. $mission->name ."\r\n"
               .'Section: '. $section->en_name ."\r\n"
               .'Schedule Date: '. $booking->schedule_date."\r\n"
               .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
               .'Type: '. $booking->type .' Meeting'."\r\n"
               .'ROOM LINK:'. url('/room/').'/'.base64_encode($room_id)."\r\n";  
        else
            $msg = 'تم إنشاء غرفة اجتماعات موعد الكتاب الخاص بك بنجاح '."\r\n"
                .'سيكون لديك لقاء مع مستشار  '."\r\n"
                .'مهمة: '. $mission->name_ar ."\r\n"
                .'الجزء: '. $section->ar_name ."\r\n"
                .'تاريخ الجدول الزمني  : '. $booking->schedule_date."\r\n"
                .'وقت: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
                .'اكتب: '. $booking->type .' '."\r\n"
                .'وصلة الغرفة :'. url('/room/').'/'.base64_encode($room_id)."\r\n";  
        $this->notification_open($client, $booking, $msg, $qrcode);

        //Remind To Client before 1 day and 1 hour 2 times...
        $sendAt = Carbon::parse($booking->schedule_date. ' ' .$booking->start_time)->subDays(1);
        if ( strtotime($sendAt) > strtotime(date("Y-m-d H:i:s")) ) $this->reminder_notification_open($client, $booking, $msg, $sendAt);
        $sendAt = Carbon::parse($booking->schedule_date. ' ' .$booking->start_time)->subHour();
        if ( strtotime($sendAt) > strtotime(date("Y-m-d H:i:s")) )  $this->reminder_notification_open($client, $booking, $msg, $sendAt);        

        return back()->with('success','Generated Meeting Room URL successfully');
    }

    public function generate_temporary_password()
    {
        return rand(10000,99999);
    }


    public function finish_meeting(Request $request)
    {
        $user = Auth::user();
        $rid = $request['room_id'];
        $chatroom = ChatRoom::where('room_id', $rid)->get()->first();
        //2021-02-12?st=17:25&dt=17:40
        $sch_date = $chatroom->booking->schedule_date;
        $start_time = $chatroom->booking->start_time;
        $end_time = $chatroom->booking->end_time;

        $chatroom->status = 'finished';
        $chatroom->save();
        $booking = Booking::find($chatroom->booking_id);
        $booking->status = 'finished';
        $booking->finish_time = date("Y-m-d H:i:s");
        $booking->save();

        $chat = Chat::create([
            'booking_id' => $chatroom->booking_id,
            'user_id' => $user->id,
            'sender'=>'consul',
            'message' => __('Ended Meeting Successfully'),
            'date'=> Carbon::now()->format('m/d/Y g:i A')
        ]);

        //BroadCast to client about finished meeting
        broadcast(new ChatEvent($chat->load('user'),$chatroom->booking_id, 1))->toOthers();        
        //BroadCast to client about finished meeting
        broadcast(new GroupScheduleEvent(str_replace(':','',$booking->schedule_date.$booking->start_time.$booking->end_time), 1))->toOthers(); 
                
        return redirect('/meetings/room/'.$sch_date.'?st='. $start_time.'&dt='.$end_time);
    }

    public function leave_meeting(Request $request)
    {
        //Client Leave meeting Room after end meeting by Consultant
        $user = Auth::user();
        if ($user)  {
            \Auth::logout();
            User::find($request->uid)->delete();
        }
        return redirect('/bookings');
    }


    public function notification_open($receiver, $handler,   $msg, $qrcode='')
    {
        Notification::route('mail', $receiver->email)->notify( new AppointmentBooked('mail', $receiver->email, $msg,$qrcode));
        Notification::send( $handler, new AppointmentBooked('sms',$receiver->phone, $msg));            
        Notification::send( $handler, new AppointmentBooked('whatsapp',$receiver->whatsapp,$msg));         
    }
    public function reminder_notification_open($receiver, $handler,   $msg, $sendAt)
    {
       $handler->notifyAt( new BookReminderNotice('mail',$receiver->email, $msg), $sendAt);
       $handler->notifyAt( new BookReminderNotice('sms',$receiver->phone, $msg), $sendAt);
       $handler->notifyAt( new BookReminderNotice('whatsapp',$receiver->phone, $msg), $sendAt);
    }

}

?>