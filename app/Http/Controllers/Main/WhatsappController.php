<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use App\ClientVerify;

use Twilio\Exceptions\TwilioException;

class WhatsappController extends Controller
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

    public function send_whatsapp_Sms(Request $request)
    {
        $to_phone = str_replace('_','',$request['to_phone']);
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_PHONE_NUMBER']; 
        $whatsapp   = config('app.twilio')['TWILIO_WHATSAPP_FROM'];

        $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $message = $client->messages
                    ->create("whatsapp:". $to_phone, // to
                        [
                            "from" =>"whatsapp:". $whatsapp,
                            "body" => $request['message']
                        ]
                    );           
            return back()->with('success',__('Sent Message to whatsapp successfully'));
        }
        catch (TwilioException  $e)
        {
            return back()->with('error',__('Failed sending message to whatsapp'));
        }       
    }
    
}