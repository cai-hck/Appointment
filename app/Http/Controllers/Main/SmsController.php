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

class SmsController extends Controller
{
    public function __construct()
    {
        /*
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
        */
    }
    public function sendSms(Request $request)
    {
        $phone = str_replace('_','',$request['phone']);
        $exist = ClientVerify::where('phone', $phone)->get()->first();
        if ($exist) {
            echo fail;
            //$exist->delete();
        } 

        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_PHONE_NUMBER']; 

        $client = new Client($accountSid, $authToken);
        try
        {
            $code = $this->generateCode($phone);           
            $client->messages->create(
               $phone, //$request['phone']
                array(
                 // A Twilio phone number you purchased at twilio.com/console
                 'from' => $appSid,
                 // the body of the text message you'd like to send
                 'body' => 'Your Verification Code of EAPP.ONLINE is !'. $code
                )
            );
                                
            $verifed = $this->register_client_verify($phone, $code);
            //echo 'fail';
            if ($verifed)
                echo 'success';
            else {
                ClientVerify::where('phone', $phone)->delete();
                echo 'fail';
            } 
         }
        catch (TwilioException  $e)
        {
            ClientVerify::where('phone', $phone)->delete();
            echo "fail";
        } 

    }

    public function check_verify_code(Request $request)
    {
        $phone = str_replace('_','',$request['phone']);
        $vcode = $request->vcode;
        $exist = ClientVerify::where('phone', $phone)->get()->first();
        if ($exist == null)  {
          echo 'fail';
        }
        else {
            if ($exist->digits == $vcode) 
                echo 'success';
            else 
                echo 'fail';
        }
    }
    public function register_client_verify($phone, $code) 
    {
        try {
            $veirfy = ClientVerify::create(
                [
                    'phone' => $phone,
                    'digits' => $code,
                    'verified'=>false,                
                ]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    public function generateCode($phone)
    {
        return $six_digit_random_number = mt_rand(100000, 999999);            
    }

    public function send_single_Sms(Request $request)
    {
        $to_phone = str_replace('_','',$request['to_phone']);
        $message = $request['message'];

        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_PHONE_NUMBER']; 


        $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
            // the number you'd like to send the message to
               $to_phone, //$request['phone']
                array(
                 // A Twilio phone number you purchased at twilio.com/console
                 'from' => $appSid,
                 // the body of the text message you'd like to send
                 'body' => $message
                )
            ); 

            return back()->with('success',__('Sent SMS message successfully'));
        }
        catch (TwilioException  $e)
        {
            return back()->with('error',__('Failed send SMS message'));
        }  
    }
}