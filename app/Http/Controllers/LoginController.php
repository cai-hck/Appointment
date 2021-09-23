<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Mission;
use App\MissionSetting;
use App\MissionNews;
use App\Section;
use App\Consultant;
use App\Secretary;
use App\ContactMail;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SentContactus;

class LoginController extends Controller
{

    public function __construtor() 
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
    }

    public function index() {
        $user = Auth::user();
        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
        //$missions = Mission::where('status', true)->get();
        $missions = Mission::select('missions.*')->leftjoin('consultants','consultants.id','=','missions.consultant_id')
                ->where('consultants.status', true)
                ->where('missions.status', true)->get();
        session()->forget('mslug');
        return view('welcome',compact('missions'));
    }

    public function mission_start($mslug) 
    {
        $user = Auth::user();
        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
        
        $mission = MissionSetting::where('slug', $mslug)->get()->first();
        $mid = $mission->mission_id;
        $main_mission = Mission::find($mid);
        
        $sections = Section::select('en_name','ar_name','id')->where('mission_id', $mid)->get();
        session()->put('mslug',$mslug);
        return view('welcome-start',compact('main_mission','mission','sections'));        
        
    }

    public function contact_us($mslug)
    {
        $user = Auth::user();
        $user = Auth::user();
        if ($user) {
            if ($user->role == 'admin')  return redirect('/admin/dashboard');
            if ($user->role == 'consul') return redirect('/consul/dashboard');
            if ($user->role == 'secret') return redirect('/secret/dashboard');
        }
        
        $m_setting = MissionSetting::where('slug', $mslug)->get()->first();
        $mission = Mission::where('id', $m_setting->mission_id)->get()->first();
        
        return view('contact-us',compact('mission'));
    }
    public function submit_contactus(Request $request)
    {        
        $mission = Mission::find($request['m_id']);
        $contact = ContactMail::create([
            'mission_id' => $request['m_id'],
            'fname' => $request['fname'],
            'lname' => $request['lname'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'subject' => $request['subject'],            
            'message' => $request['message'],            
        ]);        

        Notification::route('mail', $mission->setting->email)->notify( new SentContactus($contact));
        return back()->with('success','Sent message successfully');
    }

    public function showLoginPage()
    {     
        if (Auth::check())
        {
            $user = Auth::user();
            if ($user->role == 'admin')
            {
                return redirect('admin/dashboard');
            }
            if ($user->type == 'consul')
            {
                
            }   
            if ($user->role == 'secret')
            {
                
            }     
        }        
        return view('login');
    }

    public function logout()
    {
        session()->forget('mslug');
        session()->forget('curmId');
        Auth::logout();
        return redirect('login');
    }

    public function login_action(Request $request)
    {
        $uname = $request->input('username');
        $password = $request->input('password');
            
        if (Auth::attempt(['name' => $uname, 'password' => $password]))
        {
            $user = Auth::user();          
            if ($user->role == 'admin') {
                return redirect('admin/dashboard');
            }
            if ($user->role == 'consul') {
                $mid = $user->consultant->mission->id;
                $mission = Mission::find($mid);
                if ($mission->setting) {
                    session()->put('mslug',$mission->setting->slug);
                    session()->put('curmId',$mid);
                }
                return redirect('consul/dashboard');
            }
            if ($user->role == 'secret') {
                $mid = $user->secretary->mission->id;
                $mission = Mission::find($mid);
                if ($mission->setting) {
                    session()->put('mslug',$mission->setting->slug);
                    session()->put('curmId',$mid);
                }
                return redirect('secret/dashboard');
            }
        }
        else
        {
            return redirect('/login')->with('error',__('Invalid Username or Password !'));
        }
    }


}
?>