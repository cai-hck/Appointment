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
use App\Section;
use App\SectionInfo;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Notification;
use App\Notifications\SectionCreated;
use App\Events\MissionNotify;

use Illuminate\Support\Facades\Mail;
class SectionController extends Controller
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
        $page_title = __('Sections');  
        if ($user->role == 'consul')
            $sections = Section::where('mission_id', $user->consultant->mission->id)->get();
        if ($user->role == 'secret')
            $sections = Section::where('mission_id', $user->secretary->mission->id)->get();
        return view('consul.section.list',compact('user','page_title','sections'));
    }
    public function add()
    {
        $user = Auth::user();
        $page_title = __('Sections');  
        return view('consul.section.add',compact('user','page_title'));
    }
    public function edit($id)
    {
        $user = Auth::user();
        $page_title = __('Sections');
        $section = Section::find($id);  
        return view('consul.section.edit',compact('user','page_title','section'));
    }

    public function section_create(Request $request)
    {
        $user = Auth::user();

        //dd($request->all());

        if ($user->role == 'consul') $mission = $user->consultant->mission->id;
        if ($user->role == 'secret') $mission = $user->secretary->mission->id;

        $section = Section::create([
            'mission_id' => $mission,
            'en_name' => $request['en_name'],
            'ar_name' => $request['ar_name'],
            'en_about' => $request['en_about'],
            'ar_about' => $request['ar_about'],
            'status' => false,
            'role_by' => $user->role,
            'creator' => $user->id,
        ]);

        $doc_list = '';

        if (isset($request['chks'])) {
            $t_list = [];
            $en_chks = $request['en_chks'];
            $ar_chks = $request['ar_chks'];
            foreach ($en_chks as $key=>$one) {
                $temp = ['en'=>$one,'ar'=>$ar_chks[$key]];
                $t_list [] = $temp;
            }
            $doc_list = json_encode($t_list);
        }

        $section_info = SectionInfo::create([
            'section_id' => $section->id,
            'meetings' => $request['has_meeting_type'],
            'doc_list' => $doc_list
        ]);

        
        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en')
            $msg = 'New section created by Consultant ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'Section name(En): '. $request['en_name']."\r\n"
                . 'Section name(Ar): '. $request['ar_name']."\r\n";     
        else
            $msg = 'قسم جديد أنشأه المستشار' ."\r\n"
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'اسم القسم(En): '. $request['en_name']."\r\n"
                . 'اسم القسم(Ar): '. $request['ar_name']."\r\n";

        $consuls = Consultant::get_mission_users($mission);
        $secrets = Secretary::get_mission_users($mission);

        foreach ($consuls as $one)  $this->notification_open($one, $section, $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $section,$msg);

        $event_msg = '';
        if (app()->getLocale() == 'en')
            $event_msg = 'New section created by Consultant ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Name(En): '. $request['en_name']."<br>"
                . 'Name(Ar): '. $request['ar_name']."<br>";     
        else
            $event_msg = 'قسم جديد أنشأه المستشار ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'اسم(En): '. $request['en_name']."<br>"
                . 'اسم(Ar): '. $request['ar_name']."<br>";    

        event(new MissionNotify( $mission, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));
        
        return back()->with('success',__('New section created successfully'));
    }
    public function section_update(Request $request)
    {

        $user = Auth::user();
        if ($user->role == 'consul') $mission = $user->consultant->mission->id;
        if ($user->role == 'secret') $mission = $user->secretary->mission->id;
        $sid = $request['s_id'];

    
        Section::where('id', $sid)->update([
            'mission_id' => $mission,
            'en_name' => $request['en_name'],
            'ar_name' => $request['ar_name'],
            'en_about' => $request['en_about'],
            'ar_about' => $request['ar_about'],
            'status' => false,
            'role_by' => $user->role,
            'creator' => $user->id,
        ]);

        $sec = Section::find($sid);
        $doc_list = '';

        if (isset($request['chks'])) {
            $t_list = [];
            $en_chks = $request['en_chks'];
            $ar_chks = $request['ar_chks'];
            foreach ($en_chks as $key=>$one) {
                $temp = ['en'=>$one,'ar'=>$ar_chks[$key]];
                $t_list [] = $temp;
            }
            $doc_list = json_encode($t_list);
        }

        $exist = SectionInfo::where('section_id', $sid)->get()->first();
        if ($exist) {
            SectionInfo::where('section_id', $sid)->update([
                'section_id' => $sid,
                'meetings' => $request['has_meeting_type'],
                'doc_list' => $doc_list
            ]);
        } else {
            $section_info = SectionInfo::create([
                'section_id' => $sec->id,
                'meetings' => $request['has_meeting_type'],
                'doc_list' => $doc_list
            ]);
        }

   
        //notify to users
        $msg = '';
        if (app()->getLocale() == 'en')
            $msg = 'Section updated by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'Section name(En): '. $request['en_name']."\r\n"
                . 'Section name(Ar): '. $request['ar_name']."\r\n";     
        else
            $msg = 'تم تحديث القسم بواسطة  ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'اسم القسم(En): '. $request['en_name']."\r\n"
            . 'اسم القسم(Ar): '. $request['ar_name']."\r\n";
            
        $consuls = Consultant::get_mission_users($mission);
        $secrets = Secretary::get_mission_users($mission);

        foreach ($consuls as $one)  $this->notification_open($one, $sec,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sec,  $msg);

        $event_msg = '';
        if (app()->getLocale() == 'en')
            $event_msg = 'Section updated by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Name(En): '. $request['en_name']."<br>"
                . 'Name(Ar): '. $request['ar_name']."<br>";     
        else
            $event_msg = 'تم تحديث القسم بواسطة' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'اسم(En): '. $request['en_name']."<br>"
                . 'اسم(Ar): '. $request['ar_name']."<br>";     
        
        event(new MissionNotify( $mission, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));

        return back()->with('success',__('Section updated successfully'));
    }

    public function section_delete(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'consul') $mission = $user->consultant->mission->id;
        if ($user->role == 'secret') $mission = $user->secretary->mission->id;
        
        $sid = $request['u_id'];
        $sec = Section::find($sid);        
        //notify to users
        $msg = ''; $event_msg = '';

        if (app()->getLocale() == 'en') {
            $msg = 'Section deleted by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "\r\n"
                . 'Section name(En): '. $sec->en_name."\r\n"
                . 'Section name(Ar): '. $sec->ar_name."\r\n";     
            $event_msg = 'Section deleted by ' 
                . $user->userinfo->fname. ' '
                . $user->userinfo->lname. "<br>"
                . 'Name(En): '. $request['en_name']."<br>"
                . 'Name(Ar): '. $request['ar_name']."<br>";     
        } else {
            $msg = 'تم حذف القسم بواسطة  ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "\r\n"
            . 'اسم(En): '. $sec->en_name."\r\n"
            . 'اسم(Ar): '. $sec->ar_name."\r\n";     
            $event_msg = 'تم حذف القسم بواسطة ' 
            . $user->userinfo->fname. ' '
            . $user->userinfo->lname. "<br>"
            . 'اسم(En): '. $request['en_name']."<br>"
            . 'اسم(Ar): '. $request['ar_name']."<br>";     
        }
        $consuls = Consultant::get_mission_users($mission);
        $secrets = Secretary::get_mission_users($mission);

        foreach ($consuls as $one)  $this->notification_open($one, $sec,  $msg);
        foreach ($secrets as $one)  $this->notification_open($one, $sec,  $msg);

        Section::where('id', $sid)->delete();     
            
        event(new MissionNotify( $mission, $user->name ,asset(json_decode($user->userinfo->photo)->s) , $event_msg));


        return back()->with('success',__('Section deleted successfully'));
    }

    public function notification_open($receiver, $handler,   $msg)
    {
        if($receiver->notify_email)
            Notification::route('mail', $receiver->email)->notify( new SectionCreated('mail', $receiver->email, $msg));
        if($receiver->notify_phone)
            Notification::send( $handler, new SectionCreated('sms',$receiver->phone, $msg));            
        if($receiver->notify_whatsapp)
            Notification::send( $handler, new SectionCreated('whatsapp',$receiver->whatsapp,$msg));         
    }

}