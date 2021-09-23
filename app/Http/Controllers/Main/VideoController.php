<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;


use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/* Video Twilio */
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;

use App\Events\CallEvent;
use App\Events\InternalCallEvent;
class VideoController extends Controller
{
    public function generate_token(Request $request)
    {
        // Substitute your Twilio Account SID and API Key details
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $apiKeySid  = config('app.twilio')['TWILIO_API_VIDEO_SID'];
        $apiKeySecret     = config('app.twilio')['TWILIO_API_VIDEO_SECRET'];

        $identity = uniqid();
        $room_name = 'vidroom'.$request->bk;
        // Create an Access Token
        $token = new AccessToken(
            $accountSid,
            $apiKeySid,
            $apiKeySecret,
            3600,
            $identity, // unique participiant ID
            $room_name // unique room_name            
        ); 

        // Grant access to Video
        $grant = new VideoGrant();
        //$grant->setRoom('cool room');
        $token->addGrant($grant);

        // Serialize the token as a JWT
        echo $token->toJWT();
    }

    public function sendCallRequest(Request $request)
    {
        broadcast(new CallEvent($request->room))->toOthers();        
        return ['status'=>'success'];
    }
    public function endCallRequest(Request $request)
    {
        // Substitute your Twilio Account SID and API Key details
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $twilio = new Client($accountSid, $authToken);
        $room = $twilio->video->v1->rooms($request->rId)->update("completed");


        broadcast(new CallEvent($request->bkId, 1))->toOthers();      //1 means end video call
        echo json_encode(['status'=>'success']);
    }
    public function declineCallRequest(Request $request)
    {
        broadcast(new CallEvent($request->bkId, 2))->toOthers();      //2 means decline video call
        echo json_encode(['status'=>'success']);
    }



    /* Internal Video */

    public function generate_internal_token(Request $request)
    {
        // Substitute your Twilio Account SID and API Key details
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $apiKeySid  = config('app.twilio')['TWILIO_API_VIDEO_SID'];
        $apiKeySecret     = config('app.twilio')['TWILIO_API_VIDEO_SECRET'];

        $identity = uniqid();
        $room_name = 'introom-'.$request->introom;
        // Create an Access Token
        $token = new AccessToken(
            $accountSid,
            $apiKeySid,
            $apiKeySecret,
            3600,
            $identity, // unique participiant ID
            $room_name // unique room_name            
        ); 

        // Grant access to Video
        $grant = new VideoGrant();
        //$grant->setRoom('cool room');
        $token->addGrant($grant);

        // Serialize the token as a JWT
        echo $token->toJWT();
    }
    public function call_request(Request $request)
    {
        broadcast(new InternalCallEvent($request->introom))->toOthers();        
        return ['status'=>'success'];
    }

    public function decline_request(Request $request)
    {
        broadcast(new InternalCallEvent($request->introom, 2))->toOthers();      //2 means decline video call
        echo json_encode(['status'=>'success']);
    }

    public function end_request(Request $request)
    {
        // Substitute your Twilio Account SID and API Key details
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $twilio = new Client($accountSid, $authToken);
        $room = $twilio->video->v1->rooms($request->rId)->update("completed");


        broadcast(new InternalCallEvent($request->introom , 1))->toOthers();      //1 means end video call
        echo json_encode(['status'=>'success']);
    }
}