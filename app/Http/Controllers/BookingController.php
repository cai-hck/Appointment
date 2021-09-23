<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Mission;
use App\MissionSetting;
use App\Consultant;
use App\Secretary;
use App\Section;
use App\SectionInfo;
use App\Schedule;
use App\ScheduleTiming;
use App\Holiday;

use App\Client;
use App\ClientVerify;
use App\Booking;
use App\BookingFile;
use App\AddLink;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentBooked;
use App\Notifications\BookReminderNotice;

use Illuminate\Notifications\AnonymousNotifiable;
use Thomasjohnkane\Snooze\Traits\SnoozeNotifiable;
use Thomasjohnkane\Snooze\Models\ScheduledNotification;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
class BookingController extends Controller
{

    public $file_store_path;
    public function __construtor() 
    {
        $this->file_store_path = env("FILE_STORE_PATH", ""); 
        $user = Auth::user();

        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
    }

    /*  Stage 1*/
    public function index()
    {
        $missions = Mission::select('missions.*')->leftjoin('consultants','consultants.id','=','missions.consultant_id')
                    ->where('consultants.status', true)
                    ->where('missions.status', true)->get();
        return view('bookings',compact('missions'));
    }
    /* Stage 2 */
    public function booking($base)
    {

        $mid = base64_decode($base);
        $mission = Mission::find($mid);


        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            if (app()->getLocale() == 'en')
                 $sections = Section::where('en_name','like','%'.$_GET['key'].'%')->where('mission_id', $mission->id)->get();
            else
                $sections = Section::where('ar_name','like','%'.$_GET['key'].'%')->where('mission_id', $mission->id)->get();
        } else {
            $key = '';
            $sections = Section::where('mission_id', $mission->id)->get();
        }

        $page_limit = 5;
        $pages =  ceil(count($sections) / $page_limit) ;        
        // What page are we currently on?
        $current = min($pages, filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default'   => 1,
                'min_range' => 1,
            ),
        )));
        // Calculate the offset for the query
        $offset = ($current - 1)  * $page_limit;
        // Some information to display to the user
        $start = $offset + 1;
        $end = min(($offset + $page_limit), count($sections));        

        // The "back" link
        $prevlink = ($current > 1) ? 'p=' . ($current - 1) : 'p=1';
        // The "forward" link
        $nextlink = ($current < $pages) ? 'p='.($current + 1): 'p=' . $pages;
      
        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            if (app()->getLocale() == 'en') {                
                $sections = Section::where('en_name','like','%'.$_GET['key'].'%')->where('mission_id', $mission->id)->limit($page_limit)->offset($offset)->get();
            }
            else
                $sections = Section::where('ar_name','like','%'.$_GET['key'].'%')->where('mission_id', $mission->id)->limit($page_limit)->offset($offset)->get();
        } else {
            $key = '';
            $sections = Section::where('mission_id', $mission->id)->limit($page_limit)->offset($offset)->get();
        }

        $latest = Section::where('mission_id', $mission->id)->orderby('created_at','desc')->limit(10)->get();

        return view('single_booking',compact('mission', 'sections','key','latest','base','pages','current','prevlink','nextlink'));        
    }
    /* Stage 3 */
    public function booking_appointment($base_m, $base_s)
    {
        $today = Carbon::now();
        $weekago = Carbon::now()->subDays(7);
        
        $mid = base64_decode($base_m);
        $sid = base64_decode($base_s);
        $today = Carbon::now()->format('Y-m-d');    
        $sel_date = isset($_GET['date'])?$_GET['date']:$today;
        $mission = Mission::find($mid);
        $section = Section::find($sid);
        $holiday = false;  

        $schedule = Schedule::where('mission_id', $mission->id)->where('date', $sel_date)->get()->first();

        $onsite = [];
        $online = [];

        if ($schedule && $schedule->isHoliday) $holiday = true;
        $hd = Holiday::where('mission_id', $mission->id)->where('holiday_date', $sel_date)->get()->first();
        $hd_weekday = Holiday::where('mission_id', $mission->id)->where('holiday_date', strtolower(Carbon::parse($sel_date)->format('l')))->get()->first();
        if ($hd || $hd_weekday) $holiday = true;

        if ($schedule && !$holiday) {
            if ($schedule->isDefault) {
                //Default timings
                $timings = ScheduleTiming::where('weekday', $schedule->weekday)->where('mission_id', $mission->id)->get();
                foreach ($timings as $one) {
                    if ($one->type) {
                        //onsite
                        $tp = [];
                        $tp['index'] = $one->mission_id.'-'. $schedule->isDefault.'-'.$one->id;
                        $tp['mission'] = $one->mission_id;
                        $tp['start'] = Carbon::parse($one->start)->format('g:i A');
                        $tp['end'] = Carbon::parse($one->end)->format('g:i A');                        
                        $tp['duration'] = $one->duration;
                        if ($schedule->slots == 0) $slots = 0;
                        $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                    ->where('start_time',$one->start)->where('end_time', $one->end)
                                    ->where('type',$one->type?'Onsite':'Online')
                                    ->get()->count();
                        $slots = $schedule->slots - $current;
                        $tp['slots'] = $slots;
                        $onsite[] = $tp;
                    } else {
                        //online
                        $tp = [];
                        $tp['index'] = $one->mission_id.'-'. $schedule->isDefault.'-'.$one->id;
                        $tp['mission'] = $one->mission_id;
                        $tp['start'] = Carbon::parse($one->start)->format('g:i A');
                        $tp['end'] = Carbon::parse($one->end)->format('g:i A');
                        $tp['duration'] = $one->duration;
                        if ($schedule->slots == 0) $slots = 0;
                        $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                    ->where('start_time',$one->start)->where('end_time', $one->end)
                                    ->where('type',$one->type?'Onsite':'Online')
                                    ->get()->count();
                        $slots = $schedule->slots - $current;
                        $tp['slots'] = $slots;                        
                        $online[] = $tp;
                    }
                }
            } else {
                if ($schedule->timings != '' ) {
                    $times = json_decode($schedule->timings);
                    foreach ($times as $one) {
                        if ($one->t == 'onsite') {
                            $tp = [];
                            $tp['index'] = $schedule->mission_id.'-'. $schedule->isDefault.'-'.$one->i;
                            $tp['mission'] = $schedule->mission_id;
                            $tp['start'] = Carbon::parse($one->s)->format('g:i A');
                            $tp['end'] = Carbon::parse($one->e)->format('g:i A');
                            $tp['duration'] = $one->d;
                            if ($schedule->slots == 0) $slots = 0;
                            $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                        ->where('start_time',$one->s)->where('end_time', $one->e)
                                        ->where('type',$one->t=='onsite'?'Onsite':'Online')
                                        ->get()->count();
                            $slots = $schedule->slots - $current;
                            $tp['slots'] = $slots;                      
                            $onsite[] = $tp;
                        } else {
                            $tp = [];
                            $tp['index'] = $schedule->mission_id.'-'. $schedule->isDefault.'-'.$one->i;
                            $tp['mission'] = $schedule->mission_id;
                            $tp['start'] = Carbon::parse($one->s)->format('g:i A');
                            $tp['end'] = Carbon::parse($one->e)->format('g:i A');
                            $tp['duration'] = $one->d;
                            if ($schedule->slots == 0) $slots = 0;
                            $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                        ->where('start_time',$one->s)->where('end_time', $one->e)
                                        ->where('type',$one->t=='onsite'?'Onsite':'Online')
                                        ->get()->count();
                            $slots = $schedule->slots - $current;
                            $tp['slots'] = $slots;                                                  
                            $online[] = $tp;
                        }
                    }
                } 
            }
        }
        return view('single_appointment',compact('sel_date','mission', 'section','onsite','online','schedule','holiday'));
    }
    /* Step 4 */
    public function booking_final()
    {
        $sel_date = $_GET['D'];
        $meeting = $_GET['MV'];
        $section = $_GET['SID'];
        $mission = Mission::find(explode('-', $meeting)[0]);
        $section = Section::find($section);

        $df = explode('-', $meeting)[1]; $tid = explode('-', $meeting)[2];

        $start = '';
        $end = '';
        $start_time = '';
        $end_time = '';
        $mt = '';
        $meeting_type = '';
        if ($df==1) {
            $sch = ScheduleTiming::where('mission_id', $mission->id)->where('weekday', strtolower(Carbon::parse($sel_date)->format('l')))->where('id', $tid)->get()->first();            
            $start = Carbon::parse($sch->start_time)->format('g:i A');   
            $end =Carbon::parse($sch->end_time)->format('g:i A');                 
            $meeting_type = $sch->type?'Onsite Meeting':'Online Meeting';    
            $start_time = $sch->start_time;                     
            $end_time =$sch->end_time;
            $mt = $sch->type?'Onsite':'Online';
        } else {
            $sch = Schedule::where('date', $sel_date)->where('mission_id', $mission->id)->get()->first();
            foreach (json_decode($sch->timings) as $one) {
                if ($one->i == $tid) {
                    $start = Carbon::parse($one->s)->format('g:i A');
                    $end = Carbon::parse($one->e)->format('g:i A');
                    $meeting_type = $one->t=='onsite'?'Onsite Meeting':'Online Meeting';
                    $start_time = $one->s;                     
                    $end_time =$one->e;
                    $mt = $one->t=='onsite'?'Onsite':'Online';
                }
            }
        }

        // Check available slots

        $sch = Schedule::where('date', $sel_date)->get()->first();
        $slots = 0;
        if ($sch->slots == 0) $slots = 0;
        $current = Booking::where('mission_id', $sch->mission_id)->where('section_id', $section->id)->where('schedule_date', $sel_date)
                    ->where('start_time', $start_time)->where('end_time', $end_time)->where('type',$mt)->where('timing_id', $tid)->get()->count();
        if ($sch->slots - $current == 0)         
            return back()->with('error',__('There is no available slots now. Please choose another time.'));

        return view('booking_final',compact('mission','section','sel_date','start','end','meeting','meeting_type'));
    }

    public function booking_final_post(Request $request)
    {
        
        $this->file_store_path = public_path();      
        $phone = str_replace('_','',$request['phone']);
        $full_filename = '';
        $verify = ClientVerify::where('phone', $phone)->where('digits', $request['verify_code'])->get()->first();        
        if (!$verify) return back()->with('error', __('Verification Code is incorrect'));
        //check if client is available to book appoint ment
        //Rule must do only 1 book in 1 week
        if (!$this->check_possibilty_bookappointment($request['mission'], $request['fname'],$request['lname'],$request['email'],$phone,$request['whatsapp'])) 
            return back()->with('error', __('You can book only 1 appointment in 1 week. You already booked appointment in last 7 days'));

        if($request->hasFile('file'))
        {
            if(!is_dir($this->file_store_path ."/upload/client/". explode('+',$phone)[1]."/")) {
                mkdir($this->file_store_path ."/upload/client/". explode('+',$phone)[1] ."/");
            }

            $location = "upload/client/". explode('+',$phone)[1] ."/";
            //get filename with extension
            $filenamewithextension = $request->file('file')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;
            //Upload File
            $request->file('file')->move(public_path($location), $filenametostore);    
            $full_filename = $location.$filenametostore;
        }        

        $client = Client::create([
            'fname'=> $request['fname'],
            'lname'=>$request['lname'],
            'email'=> $request['email'],
            'phone'=>str_replace('_','',$request['phone']),
            'whatsapp'=> str_replace('_','',$request['whatsapp']),
            'address'=>$request['address'],  
            'file' => '',          
            'mission_id'=> $request['mission'],
        ]);

        $verify->verified = true;
        $verify->client_id = $client->id;
        $verify->save();

        $sch = $request['schedule'];
        $default = explode('-',$sch)[1];
        $tid = explode('-',$sch)[2];

        $st_time = $ed_time = $sch_type = '';
        if ($default) {
            $time = ScheduleTIming::where('mission_id', $request['mission'])->where('weekday', strtolower(Carbon::parse($request['sch_date'])->format('l')))->where('id', $tid)->get()->first();
            $sch_id = $time->id;
            $st_time = $time->start;
            $ed_time = $time->end;
            $sch_type = $time->t?'Onsite':'Online';
        } else {
            $ti = Schedule::where('mission_id', $request['mission'])->where('date', strtolower(Carbon::parse($request['sch_date'])->format('Y-m-d')))->get()->first();
            foreach ( json_decode($ti->timings) as $one) {
                if ($one->i == $tid) {
                    $sch_id = $one->i;
                    $st_time = $one->s;
                    $ed_time = $one->e;
                    $sch_type = $one->t=='onsite'?'Onsite':'Online';
                }
            }
        }

        $booking = Booking::create([
            'client_id'=> $client->id,
            'mission_id'=> $request['mission'],
            'section_id'=> $request['section'],
            'schedule_date'=> $request['sch_date'],
            'start_time'=> $st_time,
            'end_time'=> $ed_time,
            'type' => $sch_type,
            'timing_id'=> $sch_id,
            'isDefault'=> $default,            
            'status'=>'approved',
            'file'=>$full_filename,
            'ipaddr'=>$this->get_user_ip(),
        ]);

        //Generate QR code
        $qrcode_string = url('/my-booking').'/' .base64_encode($booking->id);
        $qrcode  = "data:image/png;base64," . \DNS2D::getBarcodePNG($qrcode_string, "QRCODE");
        $image = str_replace('data:image/png;base64,', '', $qrcode);
        $image = str_replace(' ', '+', $image);
        $imageName = 'qrcode.'.'png';
        if(!is_dir($this->file_store_path ."/upload/client/". explode('+',$phone)[1]."/")) {
            mkdir($this->file_store_path ."/upload/client/". explode('+',$phone)[1] ."/");
        }
        $location = "upload/client/". explode('+',$phone)[1] ."/";        
        \File::put($this->file_store_path .'/'.$location.$imageName, base64_decode($image));

        $booking->qrcode = $location.$imageName;
        $booking->save();

        if ($full_filename != '') {
            BookingFile::create([
                'booking_id'=> $booking->id,
                'file'=>$full_filename,
            ]);
        }
   
        $client = Client::where('id', $booking->client_id)->get()->first();
        $mission = Mission::where('id', $booking->mission_id)->get()->first();
        $section = Section::where('id', $booking->section_id)->get()->first();
        $qrcode = asset($location.$imageName);
        //Notify to User
        $msg = 'Book appointment has been submitted successfully '."\r\n"
               .'Mission: '. $mission->name ."\r\n"
               .'Section: '. $section->en_name ."\r\n"
               .'Schedule Date: '. $booking->schedule_date."\r\n"
               .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
               .'Type: '. $booking->type .' Meeting'."\r\n";  
                            
        $this->notification_open($client, $booking, $msg, $qrcode);

        //Remind To Client before 1 day and 1 hour 2 times...
        /*
        $sendAt = Carbon::parse($booking->schedule_date. ' ' .$booking->start_time)->subDays(1);
        $this->reminder_notification_open($client, $booking, $msg, $sendAt);
        $sendAt = Carbon::parse($booking->schedule_date. ' ' .$booking->start_time)->subHour();
        $this->reminder_notification_open($client, $booking, $msg, $sendAt);        
        */
        return redirect('/bookingsuccess?bk='.base64_encode($booking->id))->with('success','');
    }

    public function booking_success()
    {
        $bk_id = base64_decode($_GET['bk']);
        $booking = Booking::find($bk_id);
        return view('booking_success',compact('booking'));
    }

    public function check_possibilty_bookappointment($mission, $fname, $lname, $email, $phone,$whatsapp)
    {
        $today = Carbon::now();
        $weekago = Carbon::now()->subDays(7);
        $period = new CarbonPeriod($weekago, $today);
        $ip = $this->get_user_ip();
/*         $all = Booking::select('bookings.*')->leftjoin('clients','clients.id','=','bookings.client_id')
                        ->where('bookings.mission_id', $mission)
                        ->where('clients.fname', $fname)
                        ->where('clients.lname', $lname)
                        ->where('clients.email', $email)
                        ->where('clients.phone', $phone)
                        ->whereBetween('bookings.created_at',[$weekago, $today])
                        ->get(); */
        $all = Booking::where('mission_id', $mission)                       
                        ->where('ipaddr',$ip)
                        ->whereBetween('created_at',[$weekago, $today])
                        ->get();                                              
        if (count($all)>0)  return false;   
        return true;                                
    }

    function get_user_ip() { 
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
        $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
        $ip = $forward;
        }
        else
        {
        $ip = $remote;
        }

        return $ip;
    }        

    public function extra_upload_page($bid, $cid, $mid, $sid)
    {        
        $booking = Booking::find(base64_decode($bid));
        $mission = Mission::find(base64_decode($mid));
        $section = Section::find(base64_decode($sid));
        $client = Client::find(base64_decode($cid));
        $extra = AddLink::where('booking_id',$booking->id)->where('status', 'pending')->get()->first();
        return view('booking_extra',compact('booking','mission','section','client','extra'));
    }


    public function booking_extra_post(Request $request)
    {
        $bkid = $request['bkid'];
        $booking = Booking::find($bkid);
        $phone = Client::find($booking->client_id)->phone;

        if($request->hasFile('file'))
        {
            $this->file_store_path = public_path();
            if(!is_dir($this->file_store_path ."/upload/client/". explode('+',$phone)[1]."/")) {
                mkdir($this->file_store_path ."/upload/client/". explode('+',$phone)[1] ."/");
            }

            $location = "upload/client/". explode('+',$phone)[1] ."/";
            //get filename with extension
            $filenamewithextension = $request->file('file')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;
            //Upload File
            $request->file('file')->move(public_path($location), $filenametostore);    
            $full_filename = $location.$filenametostore;

            BookingFile::create([
                'booking_id'=> $booking->id,
                'file'=>$full_filename,
            ]);
            AddLink::where('booking_id', $booking->id)->where('status', 'pending')->update([
                'status'=>'done'
            ]);
            return back()->with('success',__('Submit additional file successfully'));
        }        
        return back()->with('error',__('Submit additional file error'));
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
       //$handler->notifyAt( new BookReminderNotice('sms',$receiver->phone, $msg), $sendAt);
       //$handler->notifyAt( new BookReminderNotice('whatsapp',$receiver->phone, $msg), $sendAt);
    }

    public function show_my_BookAppointment($baseid)
    {
        $bookingid = base64_decode($baseid);
        $booking = Booking::find($bookingid);
        return view('my-booking-view',compact('booking'));
    }


    /* New Design new functions */

    public function get_date_carbon($date)
    {
        //$date = 'Mon Feb 22 2021 03:16:15 GMT+0100 (Central European Standard Time)';
        $carbon = Carbon::createFromFormat('D M d Y H:i:s e+', $date);
        return $carbon->format('Y-m-d');
    }
    public function ajax_retrive_data(Request $request)
    {
        $mid = $request->mid;
        $sid = $request->sid;
        $curdate = '';

        if ($request->curdate != '') {
            $curdate = $this->get_date_carbon($request->curdate);
        }
            
        if ($sid == 0) {
            // get sections list
            $sections = Section::select('en_name','ar_name','id')->where('mission_id', $mid)->get();

            $settings = MissionSetting::where('mission_id', $mid)->get()->first();
            return $sections;
        } else {
            //get section info
            if ($request->curdate == '') {
                $section = Section::select('en_about','ar_about')->where('id', $sid)->get()->first();
                return $section;
            } else {
                //get slots and schedules
                return $this->get_schedule($curdate, $mid, $sid);   
                //return 'abbb';
            }
        }
    }

    public function ajax_retrive_sch_data(Request $request)
    {
        $mid = $request->mid;
        $sid = $request->sid;
        $curdate = $this->get_date_carbon($request->curdate);
        
        //dd($curdate);
        return $this->get_schedule($curdate, $mid, $sid);   

    }

    public function ajax_retrive_chk_slot(Request $request)
    {
        //$sel_date = Carbon::create($request->curdate)->format('Y-m-d');
        $sel_date = $this->get_date_carbon($request->curdate);

        $meeting = $request->meeting;
        $mission = Mission::find($request->mid);
        $section = Section::find($request->sid);

        $df = explode('-', $meeting)[1]; $tid = explode('-', $meeting)[2];

        $start = '';
        $end = '';
        $start_time = '';
        $end_time = '';
        $mt = '';
        $meeting_type = '';
        if ($df==1) {
            $sch = ScheduleTiming::where('mission_id', $mission->id)->where('weekday', strtolower(Carbon::parse($sel_date)->format('l')))->where('id', $tid)->get()->first();                  
            $start_time = $sch->start;                     
            $end_time =$sch->end;
            $mt = $sch->type?'Onsite':'Online';
        } else {
            $sch = Schedule::where('date', $sel_date)->where('mission_id', $mission->id)->get()->first();
            foreach (json_decode($sch->timings) as $one) {
                if ($one->i == $tid) {
                    $start_time = $one->s;                     
                    $end_time =$one->e;
                    $mt = $one->t=='onsite'?'Onsite':'Online';
                }
            }
        }

        // Check available slots
        $sch = Schedule::where('date', $sel_date)->get()->first();
        $slots = $sch->slots;
        $current = Booking::where('mission_id', $sch->mission_id)->where('schedule_date', $sel_date)
                    ->where('start_time', $start_time)->where('end_time', $end_time)->where('type',$mt)->get()->count();        
        if ($slots - $current <= 0)         
            return false;
        else
            return true;
    }
    public function get_schedule($sel_date, $mid, $sid)
    {
        $today = Carbon::now()->format('Y-m-d');    
        $mission = Mission::find($mid);
        $section = Section::find($sid);
        $section_info = SectionInfo::where('section_id', $sid)->get()->first();
        $holiday = false;  
        $schedule = Schedule::where('mission_id', $mission->id)->where('date', $sel_date)->get()->first();
        $onsite = []; // onsite meetings array
        $online = []; // online meetings array

        if ($schedule && $schedule->isHoliday) $holiday = true;
        $hd = Holiday::where('mission_id', $mission->id)->where('holiday_date', $sel_date)->get()->first();
        $hd_weekday = Holiday::where('mission_id', $mission->id)->where('holiday_date', strtolower(Carbon::parse($sel_date)->format('l')))->get()->first();
        if ($hd || $hd_weekday) $holiday = true;
        if ($schedule && !$holiday) {
            if ($schedule->isDefault) {
                //Default timings
                $timings = ScheduleTiming::where('weekday', $schedule->weekday)->where('mission_id', $mission->id)->get();
                foreach ($timings as $one) {
                    if ($one->type) {
                        //onsite
                        if ($section_info && $section_info->meetings!='online') {
                            $tp = [];
                            $tp['index'] = $one->mission_id.'-'. $schedule->isDefault.'-'.$one->id;
                            $tp['mission'] = $one->mission_id;
                            $tp['start'] = Carbon::parse($one->start)->format('g:i A');
                            $tp['end'] = Carbon::parse($one->end)->format('g:i A');                        
                            $tp['duration'] = $one->duration;
                            if ($schedule->slots == 0) $slots = 0;
                            $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                        ->where('start_time',$one->start)->where('end_time', $one->end)
                                        ->where('type',$one->type?'Onsite':'Online')
                                        ->get()->count();
                            $slots = $schedule->slots - $current;
                            $tp['slots'] = $slots <0 ? 0: $slots;
                            $onsite[] = $tp;
                        }                        
                    } else {
                        //online
                        if ($section_info && $section_info->meetings!='onsite') {
                            $tp = [];
                            $tp['index'] = $one->mission_id.'-'. $schedule->isDefault.'-'.$one->id;
                            $tp['mission'] = $one->mission_id;
                            $tp['start'] = Carbon::parse($one->start)->format('g:i A');
                            $tp['end'] = Carbon::parse($one->end)->format('g:i A');
                            $tp['duration'] = $one->duration;
                            if ($schedule->slots == 0) $slots = 0;
                            $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                        ->where('start_time',$one->start)->where('end_time', $one->end)
                                        ->where('type',$one->type?'Onsite':'Online')
                                        ->get()->count();
                            $slots = $schedule->slots - $current;
                            $tp['slots'] = $slots;                        
                            $online[] = $tp;
                        }
                    }
                }
            } else {
                if ($schedule->timings != '' ) {
                    $times = json_decode($schedule->timings);
                    foreach ($times as $one) {
                        if ($one->t == 'onsite') {
                            if ($section_info && $section_info->meetings!='online') {
                                $tp = [];
                                $tp['index'] = $schedule->mission_id.'-'. $schedule->isDefault.'-'.$one->i;
                                $tp['mission'] = $schedule->mission_id;
                                $tp['start'] = Carbon::parse($one->s)->format('g:i A');
                                $tp['end'] = Carbon::parse($one->e)->format('g:i A');
                                $tp['duration'] = $one->d;
                                if ($schedule->slots == 0) $slots = 0;
                                $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                            ->where('start_time',$one->s)->where('end_time', $one->e)
                                            ->where('type',$one->t=='onsite'?'Onsite':'Online')
                                            ->get()->count();
                                $slots = $schedule->slots - $current;
                                $tp['slots'] = $slots;                      
                                $onsite[] = $tp;
                            }
                        } else {
                            if ($section_info && $section_info->meetings!='onsite') {
                                $tp = [];
                                $tp['index'] = $schedule->mission_id.'-'. $schedule->isDefault.'-'.$one->i;
                                $tp['mission'] = $schedule->mission_id;
                                $tp['start'] = Carbon::parse($one->s)->format('g:i A');
                                $tp['end'] = Carbon::parse($one->e)->format('g:i A');
                                $tp['duration'] = $one->d;
                                if ($schedule->slots == 0) $slots = 0;
                                $current = Booking::where('mission_id', $schedule->mission_id)->where('schedule_date', $schedule->date)
                                            ->where('start_time',$one->s)->where('end_time', $one->e)
                                            ->where('type',$one->t=='onsite'?'Onsite':'Online')
                                            ->get()->count();
                                $slots = $schedule->slots - $current;
                                $tp['slots'] = $slots;                                                  
                                $online[] = $tp;
                            }
                        }
                    }
                } 
            }
        }

        $empty = false;
        $holiday_desc = '';
        if ($holiday) {
            if ($hd) $holiday_desc = $hd;
            if ($hd_weekday) $holiday_desc = $hd_weekday;
        }
        if (count($onsite) == 0 && count($online) == 0) $empty = true;

        $dlist = $section_info&&$section_info->doc_list!=''? json_decode($section_info->doc_list):[];

        return ['onsite'=>$onsite,'online'=>$online,'holiday'=>$holiday_desc, 'empty'=> $empty,'dlist'=>$dlist,'type'=>$section_info?$section_info->meetings:'both'];
    }
    
    public function final_post(Request $request)
    {
        $phone = str_replace('_','',$request['phone']);

        $full_filename = '';
        $full_fname_list = [];
        // Check 
        if (!$this->check_possibilty_bookappointment($request['mission_id'], $request['fname'],$request['lname'],$request['email'], $phone ,$request['whatsapp'])) 
        {
            return redirect('/#step-6')->with('error', __('You can book only 1 appointment in 1 week. You already booked appointment in last 7 days'));
        }
                
        $this->file_store_path = public_path();      

        $verify = ClientVerify::where('phone', $phone)->where('verified', false)->where('client_id',0)->get()->first();        


        if($request->hasFile('file'))
        {
            if(!is_dir($this->file_store_path ."/upload/client/". explode('+',$phone)[1]."/")) {
                mkdir($this->file_store_path ."/upload/client/". explode('+',$phone)[1] ."/");
            }

            $files = $request->file('file');

            $location = "upload/client/". explode('+',$phone)[1] ."/";
            //get filename with extension        
            foreach ($files as $file) {
                $full_filename = '';

                $filenamewithextension = $file->getClientOriginalName();
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
                //get file extension
                $extension = $file->getClientOriginalExtension();
                //filename to store
                $filenametostore = $filename.'_'.time().'.'.$extension;
                //Upload File
                $file->move(public_path($location), $filenametostore);

                $full_filename = $location.$filenametostore;

                $full_fname_list[] = $full_filename;
            }    
        
        }   
        
        

        $client = Client::create([
            'fname'=> $request['fname'],
            'lname'=>$request['lname'],
            'email'=> $request['email'],
            'phone'=>str_replace('_','',$request['phone']),
            'whatsapp'=> str_replace('_','',$request['whatsapp']),
            'address'=>$request['address'],  
            'file' => '',          
            'mission_id'=> $request['mission_id'],
        ]);

        $verify->verified = true;
        $verify->client_id = $client->id;
        $verify->save();


        $mission = Mission::find($request->mission_id);
        $section = Section::find($request->section_id);
        //$sch_date = Carbon::create($request->sch_date)->format('Y-m-d');  
        $sch_date = $this->get_date_carbon($request->sch_date);
        
        $sch = $request->meeting_value;
        $default = explode('-',$sch)[1];
        $tid = explode('-',$sch)[2];

        $st_time = $ed_time = $sch_type = $sch_id = '';

        if ($default) {
            $time = ScheduleTiming::where('mission_id', $request['mission_id'])->where('weekday', strtolower(Carbon::parse($sch_date)->format('l')))->where('id', $tid)->get()->first();
            $sch_id = $time->id;
            $st_time = $time->start;
            $ed_time = $time->end;
            $sch_type = $time->type?'Onsite':'Online';
        } else {
            $ti = Schedule::where('mission_id', $request['mission_id'])->where('date', strtolower(Carbon::parse($sch_date)->format('Y-m-d')))->get()->first();
            foreach ( json_decode($ti->timings) as $one) {
                if ($one->i == $tid) {
                    $sch_id = $one->i;
                    $st_time = $one->s;
                    $ed_time = $one->e;
                    $sch_type = $one->t=='onsite'?'Onsite':'Online';
                }
            }
        }

        $booking = Booking::create([
            'client_id'=> $client->id,
            'mission_id'=> $request['mission_id'],
            'section_id'=> $request['section_id'],
            'schedule_date'=> $sch_date,
            'start_time'=> $st_time,
            'end_time'=> $ed_time,
            'type' => $sch_type,
            'timing_id'=> $sch_id,
            'isDefault'=> $default,            
            'status'=>'approved',
            'file'=>'',
            'ipaddr'=>$this->get_user_ip(),
            'reason'=>$request['reason'],
            'lang' => app()->getLocale(),
        ]);

        //Generate QR code
        $qrcode_string = url('/my-booking').'/' .base64_encode($booking->id);
        $qrcode  = "data:image/png;base64," . \DNS2D::getBarcodePNG($qrcode_string, "QRCODE");
        $image = str_replace('data:image/png;base64,', '', $qrcode);
        $image = str_replace(' ', '+', $image);
        $imageName = 'qrcode.'.'png';
        if(!is_dir($this->file_store_path ."/upload/client/". explode('+',$phone)[1]."/")) {
            mkdir($this->file_store_path ."/upload/client/". explode('+',$phone)[1] ."/");
        }
        $location = "upload/client/". explode('+',$phone)[1] ."/";        
        \File::put($this->file_store_path .'/'.$location.$imageName, base64_decode($image));

        $booking->qrcode = $location.$imageName;
        $booking->save();

        if (count($full_fname_list)>0) {
            foreach ($full_fname_list as $f_one) {
                if ($f_one!='') {
                    BookingFile::create([
                        'booking_id'=> $booking->id,
                        'file'=>$f_one,
                    ]);
                }
            }
        }
   
        $client = Client::where('id', $booking->client_id)->get()->first();
        $mission = Mission::where('id', $booking->mission_id)->get()->first();
        $section = Section::where('id', $booking->section_id)->get()->first();
        $qrcode = asset($location.$imageName);
        //Notify to User

        if (app()->getLocale() == 'en') {
        $msg = 'Book appointment has been submitted successfully '."\r\n"
               .'Mission: '. $mission->name ."\r\n"
               .'Section: '. $section->en_name ."\r\n"
               .'Schedule Date: '. $booking->schedule_date."\r\n"
               .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
               .'Type: '. $booking->type .' Meeting'."\r\n";  
        } else {
            $msg = 'Book appointment has been submitted successfully '."\r\n"
            .'Mission: '. $mission->name ."\r\n"
            .'Section: '. $section->en_name ."\r\n"
            .'Schedule Date: '. $booking->schedule_date."\r\n"
            .'Time: '. Carbon::parse($booking->start_time)->format('g:i A').'~'.Carbon::parse($booking->end_time)->format('g:i A')."\r\n"
            .'Type: '. $booking->type .' Meeting'."\r\n";  
        }
                            
        $this->notification_open($client, $booking, $msg, $qrcode);      
        return redirect('/bookingsuccess?bk='.base64_encode($booking->id))->with('success','');  
    }
}