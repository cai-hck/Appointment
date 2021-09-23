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
use App\Chat;
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

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Events\ChatEvent;
use App\Events\CallEvent;
use App\Events\GroupScheduleEvent;

class ChatController extends Controller
{

    public function openMeeting($rid)
    {       
        // Meeting Room for Consuls
        $user = Auth::user();
        if ($user->role!='consul') {
            return back();
        }
        $chatroom = ChatRoom::where('room_id', $rid)->get()->first();
/*         if ($chatroom->status == 'pending') {
            $chatroom->status = 'air';
            $chatroom->save();
        } */
        
        $book = $chatroom->booking_id;
        $booking = Booking::find($book);
        
        $recepiant = Client::find($booking->client_id);
        $page_title = 'Meeting';

        $bookings = Booking::where('mission_id', $booking->mission_id)
                    ->where('schedule_date', $booking->schedule_date)
                    ->where('start_time', $booking->start_time)
                    ->where('end_time', $booking->end_time)
                    ->where('type','Online')
                    ->orderby('created_at','asc')->get();
        $room = $rid;   
        
    
        broadcast(new GroupScheduleEvent(str_replace(':','',$booking->schedule_date.''.$booking->start_time.''.$booking->end_time),  2))->toOthers(); 

        return view('include.meeting.open',compact('chatroom','user','page_title',
            'book','avatar','recepiant','bookings','room',
            'total_users','total_finished','total_pending',
            'booking'
        ));
    }
    public function clientRoomMeeting($ridbase)
    {
        $rid = base64_decode($ridbase);
        $chatroom = ChatRoom::where('room_id', $rid)->get()->first();

        $user = User::find($chatroom->room_client);

        if (!$user) {
            return redirect('/');
        }
        if (Auth::attempt(['email'=> $user->email,'password'=>$chatroom->room_id],true)){
            $user = Auth::user();
        }

        $book = $chatroom->booking_id;
        $booking = Booking::find($book);
        
        $bookings = Booking::where('mission_id', $booking->mission_id)
                    ->where('schedule_date', $booking->schedule_date)
                    ->where('start_time', $booking->start_time)
                    ->where('end_time', $booking->end_time)
                    ->where('type','Online')
                    ->orderby('created_at','asc')->get();

        $recepiant = $booking->mission->consultant;
        //asset("client/assets/img/client_avatar.png") 
        $total_users = count($bookings);

        
        $total_finished = Booking::where('mission_id', $booking->mission_id)
            ->where('schedule_date', $booking->schedule_date)
            ->where('start_time', $booking->start_time)
            ->where('end_time', $booking->end_time)
            ->where('type','Online')
            ->where('status','finished')
            ->orderby('created_at','asc')->get()->count();
        $total_pending = Booking::where('mission_id', $booking->mission_id)
            ->where('schedule_date', $booking->schedule_date)
            ->where('start_time', $booking->start_time)
            ->where('end_time', $booking->end_time)
            ->where('type','Online')
            ->where('status','approved')
            ->orderby('created_at','asc')->get()->count();

        return view('include.meeting.open',compact('user','book','avatar','recepiant','chatroom',
            'total_users','total_finished','total_pending','booking'
            ));
    }
    public function fetchAllMessages($bkid)
    {
    	return Chat::with('user')->where('booking_id', $bkid)->get();
    }

    public function sendMessage(Request $request)
    {              
        $user = Auth::user();
        $book = Booking::where('id', $request->bid)->get()->first();
        $client = Client::where('id', $book->client_id)->get()->first();
        $phone = $client->phone;

        $fname = '';

        if ($request->file('file')) {    

            $file = $request->file('file');          
            $fname = Carbon::now()->format('YmdHis').'-'.$file->getClientOriginalName();               
            $location = "upload/client/". explode('+',$phone)[1] ."/";
            $file->move(public_path($location), $fname);    
        }


    	$chat = Chat::create([
                'booking_id' => $request->bid,
                'user_id' => $user->id,
                'sender'=>$user->role,
                'message' => $request->message?$request->message:'',
                'date'=> Carbon::now()->format('m/d/Y g:i A'),
                'path' => asset("/upload/client/". explode('+',$phone)[1] ."/".$fname),
                'file'=> $fname,
                'hasfile'=> $request->file('file')?true:false
        ]);
        broadcast(new ChatEvent($chat->load('user'),$request->bid))->toOthers();        
    	return ['status' => 'success'];
    }



}
