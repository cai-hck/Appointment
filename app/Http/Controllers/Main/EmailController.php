<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use App\ClientVerify;
use App\Mission;
use App\MissionSetting;


use Twilio\Exceptions\TwilioException;
use Illuminate\Notifications\Messages\MailMessage;

use Mail;
class EmailController extends Controller
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
    
    public function send_single_email(Request $request)
    {

        $mslug = session()->get('mslug');
        $mid = session()->get('curmId');
        $m_setting = MissionSetting::where('slug', $mslug)->get()->first();
        $mission = null;
        if ($mid)
            $mission = Mission::find($mid);
        else
            $mission = Mission::find($m_setting->mission_id);
        
        $sub = $request['subject'];
        $to_email = $request['to_email'];
        $content = $request['about'];

        Mail::send('emails.notify', ['text'=>$content,'mission'=>$mission], function ($message) use ($sub, $to_email, $content){       
            $message->subject($sub);
            $message->to($to_email);
        });

        return back()->with('success',__('Sent email successfully'));
    }
    
}