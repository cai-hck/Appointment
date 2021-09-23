<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Consultant;
use App\Transaction;
use App\Secretary;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Notification;
use App\Notifications\TransactionCreated;
use App\Events\MasterNotify;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Auth::check())
            {
                $user = Auth::user();
                if ($user->role == 'admin')
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
        $payments = Transaction::all();        

    
        if (isset($_GET['start_date']) && $_GET['start_date']!='') {
            $start = $_GET['start_date'] ;
            $end = $_GET['end_date'];
            $date_range = $start. ' ~ ' .$end;

            $all_earned = Transaction::whereBetween('created_at', [Carbon::parse($start.' 00:00:00'), Carbon::parse($end.' 23:59:59')])->sum('amount');                    
            $approved_earned = Transaction::where('status',true)->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)])->sum('amount');
        } else  {
            $all_earned = Transaction::all()->sum('amount');
            $approved_earned = Transaction::where('status',true)->sum('amount');
            $start = date('Y-m-d');
            $end = date('Y-m-d');
            $date_range = 'Till today';
        }
        
        $monthly = Transaction::where('status',true)->where( 'created_at', '>=', Carbon::now()->startOfMonth()->toDateString() )->sum('amount');
        $weekly = Transaction::where('status',true)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
        $today = Transaction::where('status',true)->where( 'created_at', '>=', Carbon::today() )->sum('amount');

        $money = [
            'total' => $all_earned,
            'approved' => $approved_earned,
            'month' => $monthly,
            'week' => $weekly,
            'today' => $today
        ];
    
        return view('admin.payment.list',compact('user','payments','money','date_range','start', 'end'));
    }

    public function payment_confirm(Request $request)
    {
        $tid = $request['t_id'];
        $transaction = Transaction::where('id',$tid)->get()->first();
        //dd($transaction);
        $amount = $transaction->amount;
        $mission = $transaction->consultant->mission;        
        $mission_cost = $mission->cost_per_user;


        if ($transaction->type == 'activate') {
            Consultant::where('mission_id', $mission->id)->where('active_date','')->update([
                    'active_date' => date('Y-m-d'),
                    'expire_date' => date('Y-m-d', strtotime('+1 month')),
                    'status' => true,
            ]);
            Secretary::where('consultant_id', $transaction->consultant->id)
                ->where('mission_id', $mission->id)->where('active_date','')->update([
                    'active_date' => date('Y-m-d'),
                    'expire_date' => date('Y-m-d', strtotime('+1 month')),
                    'status' => true,
            ]);
            $msg = 'Master Admin accepted your payment.'. "\r\n"
                .'All accounts have been activated now'."\r\n"
                .'Expired date: ' . date('Y-m-d', strtotime('+1 month'));
        }
        if ($transaction->type == 'extend') {
            Consultant::where('mission_id', $mission->id)->where('expired',true)->update([
                'expire_date' => date('Y-m-d', strtotime('+1 month')),
                'status' => true,
            ]);
            Secretary::where('consultant_id', $transaction->consultant->id)
                ->where('mission_id', $mission->id)->where('expired',true)->update([
                    'expire_date' => date('Y-m-d', strtotime('+1 month')),
                    'status' => true,
            ]);
            $msg = 'Master Admin accepted your payment.'. "\r\n"
                .'All accounts have been activated now'."\r\n"
                .'New expired date: ' . date('Y-m-d', strtotime('+1 month'));
        }
        if ($transaction->type == 'add') {
            $current_limit = $transaction->consultant->number_of_subs;
            $transaction->consultant->update([
                'number_of_subs' => $current_limit + intval($amount/$mission_cost),
                'revised' => true
            ]);
            $msg = 'Master Admin accepted your payment.'. "\r\n"
                    .'You can create '. intval($amount/$mission_cost). ' accounts'."\r\n";
        }

        $transaction->update([
            'accept_date'=> date('Y-m-d H:i:s'),
            'status'=>true
        ]);

        //Send message to admin whatsapp , sms, email      
        $consul = Consultant::where('id', $transaction->consultant_id)->get()->first();        
        $this->notification_open($consul->user, $transaction,  $msg);
        $admin = Auth::user();
        event(new MasterNotify( $consul->id, $admin->name ,asset(json_decode($admin->userinfo->photo)->s) , $msg));
        
        return back()->with('succss','Transaction Accepted successfully');
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