<?php

namespace App\Channels;
use Illuminate\Notifications\Notification;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Twilio\Exceptions\TwilioException;


class WhatsappChannel
{
    public function send($notifiable, Notification $notification)
    {
        //send Whatsapp SMS to phone
        $msg = $notification->toWhatsapp($notifiable);
        //dd($msg);
        $from_phone = config('app.twilio')['TWILIO_WHATSAPP_FROM'];
        $this->send_single_Whatsapp($msg['to'],$from_phone,$msg['msg']);

    }

    public function send_single_Whatsapp($to, $from, $msg)
    {
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];

        $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $message = $client->messages
                    ->create("whatsapp:".$to, // to
                        [
                            "from" =>"whatsapp:".$from,
                            "body" => $msg
                        ]
                    );           
            return true;
        }
        catch (TwilioException  $e)
        {          
            return false;
        }       
    }
}