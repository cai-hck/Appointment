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
use App\InternalChat;



use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Events\ChatEvent;
use App\Events\InternalChatEvent;
use App\Events\CallEvent;
use App\Events\InternalCallEvent;

/* Video Twilio */
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;


class InternalchatController extends Controller
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
        $page_title = __('Internal Chat');

        $consuls = Consultant::where('mission_id', $mission_id)->where('user_id','!=',$user->id)->get();
        $secrets = Secretary::where('mission_id', $mission_id)->where('user_id','!=',$user->id)->get();

        if (count($consuls)>0) {
            $uid = $consuls->first()->user_id;
            return redirect('/internal-chat/open/'. $uid);
        }
        if (count($secrets)>0) {
            $uid = $secrets->first()->user_id;
            return redirect('/internal-chat/open/'. $uid);
        }

        return view('include.internal.index',compact('user','page_title','consuls','secrets'));
    }

    public function open_chat($id)
    {
        $selected_member = $id;
        $user  = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $page_title = __('Internal Chat');

        $consuls = Consultant::select('consultants.user_id as user_id','users.id as uid','users.role as role','user_infos.*')
                            ->leftjoin('users','users.id','=','consultants.user_id')
                            ->leftjoin('user_infos','user_infos.user_id','=','users.id')
                            ->where('consultants.mission_id', $mission_id)
                            ->where('consultants.user_id','!=',$user->id)->get();
        $secrets = Secretary::select('secretaries.user_id as user_id','users.id as uid','users.role as role','user_infos.*')
                            ->leftjoin('users','users.id','=','secretaries.user_id')
                            ->leftjoin('user_infos','user_infos.user_id','=','users.id')
                            ->where('secretaries.mission_id', $mission_id)
                            ->where('secretaries.user_id','!=',$user->id)->get();

        $selected_user = User::select('users.id as uid','user_infos.*')->leftjoin('user_infos','user_infos.user_id','=','users.id')->where('users.id', $id)->get()->first();

        //check exist
        $current_user = User::select('users.id as id','user_infos.fname as fname','user_infos.lname as lname')->leftjoin('user_infos','user_infos.user_id','=','users.id')->where('users.id', $user->id)->get()->first();
        $internalroom = 'internal-'.$mission_id;
        /* $internalroom = $user->id. '-'.$selected_user->uid; */
        return view('include.internal.open_chat',compact('selected_member','current_user','user','page_title','consuls','secrets','selected_user','mission_id','internalroom')); 
    }

    public function fetch_messages(Request $request)
    {
        $chats = InternalChat::where('from', $request->u1)->where('to',$request->u2)
                                ->orWhere('to', $request->u1)->where('from',$request->u2)
                               ->orderby('created_at','desc')
                               ->take(100)
                               ->get();
        if ($chats == null) return [];
        else return $chats;
    }

    public function send_message(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->role=='consul'?$user->consultant->mission->id:$user->secretary->mission->id;
        $chat = InternalChat::create([
            'room_id' => $request->room_id,
            'user_id' => $user->id,
            'from' => $user->id,
            'to' => $request->to,
            'message'=>$request->message,
            'sender'=>$user->role,
            'date'=>date('Y-m-d H:i:s'),            
        ]);
        broadcast(new InternalChatEvent($chat,$mission_id))->toOthers();        
        return ['status' => 'success'];
    }

  
}