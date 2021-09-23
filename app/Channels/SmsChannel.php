<?php

namespace App\Channels;
use Illuminate\Notifications\Notification;


use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Twilio\Exceptions\TwilioException;


class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        //send SMS to phone
        $msg = $notification->toSms($notifiable);
        $from_phone = config('app.twilio')['TWILIO_PHONE_NUMBER'];
        $this->send_single_Sms($msg['to'],$from_phone,$msg['msg']);

    }

    public function send_single_Sms($to, $from, $message)
    {
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
            // the number you'd like to send the message to
               $to, //$request['phone']
                array(
                 // A Twilio phone number you purchased at twilio.com/console
                 'from' => $from,
                 // the body of the text message you'd like to send
                 'body' => $message
                )
            );
           return true;
        }
        catch (TwilioException  $e)
        {
            return false;
        }  
    }
}