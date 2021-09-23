<?php

namespace App\Http\Controllers\Consul;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\Consultant;
use App\Secretary;
use App\Transaction;

use Stripe;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Notification;
use App\Notifications\TransactionCreated;


class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Auth::check())
            {
                $user = Auth::user();
                if ($user->role == 'consul')
                {
                    return $next($request);
                }
            }
            return redirect('login');
        });
    }

    public function index() 
    {
        $user = Auth::user();
        $page_title = __('Payments');      
        $transactions  = Transaction::where('consultant_id', $user->consultant->id)->orderBy('date','desc')->get();
        $total_spent =  Transaction::where('consultant_id', $user->consultant->id)->sum('amount');
        
        $total_users = Consultant::where('mission_id', $user->consultant->mission_id)->where('status', true) ->get()->count() +
                       Secretary::where('mission_id', $user->consultant->mission_id)->where('status', true) ->get()->count();
                       
        $active_users = Consultant::where('status',true)->where('mission_id', $user->consultant->mission_id)->get()->count() 
                        + Secretary::where('status',true)->where('mission_id', $user->consultant->mission_id)->get()->count();
                        
        $pending_users = Consultant::where('active_date','')->where('status',false)->where('mission_id', $user->consultant->mission_id)->get()->count() 
                        + Secretary::where('active_date','')->where('status',false)->where('mission_id', $user->consultant->mission_id)->get()->count();
                        
        //$expired_users = $total_users - $active_users - $pending_users;
        
        $expired_users = Consultant::where('status',true)->where('mission_id', $user->consultant->mission_id)->where('expired',true)->get()->count() +
                    Secretary::where('status',true)->where('mission_id', $user->consultant->mission_id)->where('expired',true)->get()->count();        

        $user_anal = ['total'=>$user->consultant->number_of_subs + 1,'active'=>$total_users ,'pending'=>$pending_users,'expire'=>$expired_users];


        return view('consul.payment.list',compact('user','page_title','transactions','user_anal','total_spent'));
    }

    public function pay_success()
    {
        $user = Auth::user();
        $page_title = __('Payments');      
        return view('consul.payment.success',compact('user','page_title'));
    }

    public function pay_fail()
    {
        $user = Auth::user();
        $page_title = __('Payments');      
        return view('consul.payment.fail',compact('user','page_title'));
    }

    public function pay_action(Request $request){        
        $val =$this->check_pay_rule($request['type'],$request['amount_user']);
        if (!$val['return']) 
            return redirect('/consul/payments/fail')->with('error',$val['message']);

        $user = Auth::user();
        if ($request['type'] == 'activate')
            $price = number_format(floatval(($user->consultant->number_of_subs+1)*$user->consultant->mission->cost_per_user), 2) * 100;
        else
            $price = number_format(floatval($request['amount_user']*$user->consultant->mission->cost_per_user), 2) * 100;

        $stripe_token = $request['stripeToken'];
        
        $stripe_key = config('app.stripe')['STRIPE_TEST_SK'];
        if (config('app.stripe')['STRIPE_TESTLIVE'] )  $stripe_key = config('app.stripe')['STRIPE_LIVE_SK'];        
    
        \Stripe\Stripe::setApiKey($stripe_key); 
        try {
            $payment = Stripe\Charge::create ([
                "amount" => $price,
                "currency" => "usd",
                "source" => $stripe_token,
                "description" => $request['about']
            ]);

            // Create Transaction            
            $trans = Transaction::create([
                'trans_id' => '#TRANS'.time(),
                'consultant_id' => $user->consultant->id,
                'date'=> date('Y-m-d H:i:s'),
                'status'=>false,
                'amount'=> $price/100,
                'about'=> $request['about']==null?'':$request['about'],
                'type'=>$request['type']
            ]);

            //Send message to admin whatsapp , sms, email
            $msg = '';
            if (app()->getLocale() == 'en')
                $msg = 'Transaction created.'. "\r\n"
                       .'Amount: $'. $price/100;
            else
                $msg = 'تم إنشاء المعاملة' . "\r\n"
                        .'$'.$price/100 .' : كمية';

            $admin = User::find(1); // $admin user
            $this->notification_open($admin, $trans,  $msg);

            return redirect('/consul/payments/success')->with('success',__('You have sent $'). ($price/100) . __(' successfully'));


        } catch (Exception $e) {
            //dd($e);
            return redirect('/consul/payments/fail')->with('message',__('Payment failed with unkown issues.'));
        } 
    }
    public function check_pay_rule($type, $cnt) 
    {
        $user = Auth::user();
        $total_users = Consultant::where('mission_id', $user->consultant->mission_id)->get()->count() + Secretary::where('mission_id', $user->consultant->mission_id)->get()->count();
        $active_users = Consultant::where('status',true)->where('mission_id', $user->consultant->mission_id)->get()->count() 
                        + Secretary::where('status',true)->where('mission_id', $user->consultant->mission_id)->get()->count();
        $pending_users = Consultant::where('active_date','')->where('status',false)->where('mission_id', $user->consultant->mission_id)->get()->count() 
                        + Secretary::where('active_date','')->where('status',false)->where('mission_id', $user->consultant->mission_id)->get()->count();
        //$expired_users = $total_users - $active_users - $pending_users;
        $expired_users = Consultant::where('status',true)->where('mission_id', $user->consultant->mission_id)->where('expire_date','>',date('Y-m-d'))->get()->count() +
                    Secretary::where('status',true)->where('mission_id', $user->consultant->mission_id)->where('expire_date','>',date('Y-m-d'))->get()->count();        

        if ($type == 'activate') {
            //if ($cnt != $pending_users) return ['return'=>false, 'message'=>__('Available number of accounts is incorrect')];
            return ['return'=>true, 'message'=>''];            
        } 
        if ($type == 'extend') {
            if ($cnt != $expired_users) return ['return'=>false, 'message'=>__('Available number of accounts is incorrect')];
            return ['return'=>true, 'message'=>''];   
        }
        if ($type == 'add') {
            if ($cnt == 0) return ['return'=>false, 'message'=>__('Available number of accounts is incorrect')];
            return ['return'=>true, 'message'=>''];   
        }

    }

    public function notification_open($receiver,  $hander, $msg)
    {
        if($receiver->userinfo->notify_email)
            Notification::route('mail', $receiver->email)->notify( new TransactionCreated('mail', $receiver->email, $msg));
        if($receiver->userinfo->notify_phone)
            Notification::send( $hander, new TransactionCreated('sms',$receiver->userinfo->mobile, $msg));            
        if($receiver->userinfo->notify_whatsapp)
            Notification::send( $hander, new TransactionCreated('whatsapp',$receiver->userinfo->whatsapp,$msg));         
    }
}