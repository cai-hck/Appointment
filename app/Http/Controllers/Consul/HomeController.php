<?php

namespace App\Http\Controllers\Consul;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\MissionSetting;
use App\MissionNews;
use App\Consultant;
use App\Secretary;
use App\Booking;
use App\Client;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Notification;
use App\Notifications\AccountCreated;
use App\Events\MissionNotify;
class HomeController extends Controller
{
    public $file_store_path;
    public function __construct()
    {
        $this->file_store_path = public_path();
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
        $page_title = __('Dashboard');        
        $sub_consuls = Consultant::where('type','sub')->where('mission_id', $user->consultant->mission_id)->where('user_id', '!=', $user->id)->get();
        if ($user->consultant->type!='sub')
            $sub_secrets = Secretary::where('consultant_id', $user->consultant->id)->get();
        else
            $sub_secrets = Secretary::where('mission_id', $user->consultant->mission->id)->get();

        $today_bookings = Booking::where('mission_id', $user->consultant->mission->id)->where('schedule_date', date('Y-m-d'))->get();
        return view('consul.dahsboard',compact('user','page_title','sub_consuls','sub_secrets','today_bookings'));

    }

    public function profile()
    {
        $user = Auth::user();
        $page_title = __('Profile');        
        $sub_consuls = Consultant::where('type','sub')->where('mission_id', $user->consultant->mission_id)->get();
        $sub_secrets = Secretary::where('consultant_id', $user->consultant->id)->get();
        return view('consul.profile',compact('user','page_title','sub_consuls','sub_secrets'));
    }

    public function add_account()
    {
        $user = Auth::user();
        $page_title = __('Add Sub Account');    
        $sub_consuls = Consultant::where('type','sub')->where('mission_id', $user->consultant->mission_id)->get()->count();
        $sub_secrets = Secretary::where('consultant_id', $user->consultant->id)->get()->count();
        $account_limit = (int)$user->consultant->number_of_subs - (int)$sub_consuls - (int)$sub_secrets;
        return view('consul.add_account',compact('user','page_title','account_limit'));
    }

    public function profile_subedit($id)
    {

        if (!$this->check_permission($id)) return back()->with('error',__('You do not have permission to get account profile'));

        $user = Auth::user();
        $page_title = __('Add Sub Account');    
        $sub_consuls = Consultant::where('type','sub')->where('mission_id', $user->consultant->mission_id)->get()->count();
        $sub_secrets = Secretary::where('consultant_id', $user->consultant->id)->get()->count();
        $account_limit = (int)$user->consultant->number_of_subs - (int)$sub_consuls - (int)$sub_secrets;

        $sub = User::find($id);


        return view('consul.edit_account',compact('user','page_title','account_limit','sub'));
    }

    public function check_permission($id)
    {
        $user = Auth::user();
        $compare = User::find($id);
        if ($compare->role == 'consul') {
            if ($compare->consultant->mission_id != $user->consultant->mission_id) return false;            
        }
        if ($compare->role == 'secret') {
            if ($compare->secretary->mission_id != $user->consultant->mission_id) return false;
        }
        return true;
    }
    public function profile_update(Request $request)
    {
        //dd($request['email_notify']);

        $user = Auth::user();
        $this->file_store_path = public_path();
        $pwd_code = $request['password']?$request['password']:$user->userinfo->pwd_code;
        User::where('id', $user->id)->update([
            'name'=>$request['username'],
            'role'=>'consul',
            'email'=>$request['email'],
            'password'=>bcrypt($pwd_code),
        ]);
        UserInfo::where('id', $user->userinfo->id)->update([
            'fname' => $request['fname'],
            'lname'=>$request['lname'],
            'mobile'=>str_replace('_','',$request['mobile']),
            'whatsapp'=>str_replace('_','',$request['whatsapp']),
            'address'=>$request['address'],
            'pwd_code'=>$pwd_code,
            'notify_email' => $request['email_notify']=='on'?true:false,
            'notify_phone' => $request['phone_notify']=='on'?true:false,
            'notify_whatsapp' => $request['whatsapp_notify']=='on'?true:false,
        ]);

        if ($request->hasFile('photo')) {
            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/");
            }
            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/thumb/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/thumb/");
            }            
            $location = "upload/user/". $user->id ."/";
            $thumb_location = "upload/user/". $user->id ."/thumb/";
            //get filename with extension
            $filenamewithextension = $request->file('photo')->getClientOriginalName();     
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('photo')->getClientOriginalExtension();      
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;     
            //small thumbnail name
            $smallthumbnail = $filename.'_small_'.time().'.'.$extension;     
            //medium thumbnail name
            $mediumthumbnail = $filename.'_medium_'.time().'.'.$extension;     
            //large thumbnail name
            $largethumbnail = $filename.'_large_'.time().'.'.$extension;     
            //Upload File
            $request->file('photo')->move(public_path($location), $filenametostore);
            //create small thumbnail
            $smallthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($smallthumbnailpath, 150, 93, $smallthumbnail, $thumb_location);
            //create medium thumbnail
            $mediumthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($mediumthumbnailpath, 300, 185,$mediumthumbnail,$thumb_location);
            //create large thumbnail
            $largethumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($largethumbnailpath, 550, 340,$largethumbnail, $thumb_location);


            if ($user->userinfo) {
                UserInfo::select()->where('user_id', $user->id)->update([
                    'photo' => json_encode(
                        [
                            'c'=> $location.$filenametostore,
                            's'=>$thumb_location.$smallthumbnail,
                            'm'=>$thumb_location.$mediumthumbnail,
                            'l'=>$thumb_location.$largethumbnail,
                        ]
                    )
                ]);
            }            
        } 


        return back()->with('success',__('Profile updated successfully'));
    }


    public function account_create(Request $request)
    {
        $this->file_store_path = public_path();
        if ($request->hasFile('photo')) {

            $user = User::create([
                'name'=>$request['username'],
                'password'=>bcrypt($request['password']),
                'email'=>$request['email'],
                'role'=>$request['role'],    
            ]);

            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/");
            }
            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/thumb/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/thumb/");
            }            
            $location = "upload/user/". $user->id ."/";
            $thumb_location = "upload/user/". $user->id ."/thumb/";
            //get filename with extension
            $filenamewithextension = $request->file('photo')->getClientOriginalName();     
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('photo')->getClientOriginalExtension();      
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;     
            //small thumbnail name
            $smallthumbnail = $filename.'_small_'.time().'.'.$extension;     
            //medium thumbnail name
            $mediumthumbnail = $filename.'_medium_'.time().'.'.$extension;     
            //large thumbnail name
            $largethumbnail = $filename.'_large_'.time().'.'.$extension;     
            //Upload File
            $request->file('photo')->move(public_path($location), $filenametostore);
            //create small thumbnail
            $smallthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($smallthumbnailpath, 150, 93, $smallthumbnail, $thumb_location);
            //create medium thumbnail
            $mediumthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($mediumthumbnailpath, 300, 185,$mediumthumbnail,$thumb_location);
            //create large thumbnail
            $largethumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($largethumbnailpath, 550, 340,$largethumbnail, $thumb_location);

            UserInfo::create([
                'user_id' =>$user->id,
                'fname' => $request['fname'],
                'lname'=>$request['lname'],
                'mobile'=>str_replace('_','',$request['mobile']),
                'whatsapp'=>str_replace('_','',$request['whatsapp']),
                'address'=>$request['address'],
                'pwd_code'=>$request['password'],
                'photo'=> json_encode(
                    [
                        'c'=> $location.$filenametostore,
                        's'=>$thumb_location.$smallthumbnail,
                        'm'=>$thumb_location.$mediumthumbnail,
                        'l'=>$thumb_location.$largethumbnail,
                    ]
                )
            ]);
            $main_user = Auth::user();
            if ($request['role'] == 'consul') {
                 //Create Consultant                     
                if ($main_user->consultant->revised) {
                    Consultant::create([
                        'type' => 'sub',
                        'user_id' => $user->id,
                        'number_of_subs' => 0,
                        'mission_id' => $main_user->consultant->mission_id,
                        'active_date'=> date('Y-m-d'),
                        'expire_date' =>  date('Y-m-d', strtotime('+1 month')),
                        'status' => true
                    ]); 
                } else {
                              
                    Consultant::create([
                        'type' => 'sub',
                        'user_id' => $user->id,
                        'number_of_subs' => 0,
                        'mission_id' => $main_user->consultant->mission_id,
                        'active_date'=> $main_user->consultant->active_date,
                        'expire_date' =>  $main_user->consultant->expire_date,
                        'status' =>  $main_user->consultant->status
                    ]); 
                }
              
            }

            if ($request['role'] == 'secret') {
                //Create Secretary
                if ($main_user->consultant->revised) {

                } else {
                    Secretary::create([
                        'user_id' => $user->id,
                        'consultant_id' => $main_user->consultant->id,
                        'mission_id' => $main_user->consultant->mission_id,
                        'active_date'=> date('Y-m-d'),
                        'expire_date' =>  date('Y-m-d', strtotime('+1 month')),
                        'status' =>  $main_user->consultant->status
                    ]); 
                }
            }

            $msg = 'Your account created by Consultant ' 
                . $main_user->userinfo->fname. ' '
                .$main_user->userinfo->lname. "\r\n"
                .'username:'. $user->name."\r\n"
                .'password:'. $user->userinfo->pwd_code;

            $this->notification_open($user, $msg);

            return back()->with('success',__('Created sub account successfully'));
        } else {
            return back()->with('error',__('Profile photo is required field.'));
        }
      
    }   

    public function account_update(Request $request)
    {
        $m_user = Auth::user();

        $user = User::find($request['u_id']);
        $this->file_store_path = public_path();
        if ($request->hasFile('photo')) {
            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/");
            }
            if(!is_dir($this->file_store_path ."/upload/user/". $user->id ."/thumb/")) {
                mkdir($this->file_store_path ."/upload/user/". $user->id ."/thumb/");
            }            
            $location = "upload/user/". $user->id ."/";
            $thumb_location = "upload/user/". $user->id ."/thumb/";
            //get filename with extension
            $filenamewithextension = $request->file('photo')->getClientOriginalName();     
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('photo')->getClientOriginalExtension();      
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;     
            //small thumbnail name
            $smallthumbnail = $filename.'_small_'.time().'.'.$extension;     
            //medium thumbnail name
            $mediumthumbnail = $filename.'_medium_'.time().'.'.$extension;     
            //large thumbnail name
            $largethumbnail = $filename.'_large_'.time().'.'.$extension;     
            //Upload File
            $request->file('photo')->move(public_path($location), $filenametostore);
            //create small thumbnail
            $smallthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($smallthumbnailpath, 150, 93, $smallthumbnail, $thumb_location);
            //create medium thumbnail
            $mediumthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($mediumthumbnailpath, 300, 185,$mediumthumbnail,$thumb_location);
            //create large thumbnail
            $largethumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($largethumbnailpath, 550, 340,$largethumbnail, $thumb_location);

            UserInfo::where('user_id', $user->id)->update([
                'photo'=> json_encode(
                    [
                        'c'=> $location.$filenametostore,
                        's'=>$thumb_location.$smallthumbnail,
                        'm'=>$thumb_location.$mediumthumbnail,
                        'l'=>$thumb_location.$largethumbnail,
                    ]
                )
            ]);
        } 
        User::where('id',$user->id)->update([
            'name'=>$request['username'],
            'password'=>bcrypt($request['password']),
            'email'=>$request['email'],
        ]);
        UserInfo::where('id',$user->userinfo->id)->update([
            'user_id' =>$user->id,
            'fname' => $request['fname'],
            'lname'=>$request['lname'],
            'mobile'=>str_replace('_','',$request['mobile']),
            'whatsapp'=>str_replace('_','',$request['whatsapp']),
            'address'=>$request['address'],
            'pwd_code'=>$request['password'],
        ]);

        $user = User::find($request['u_id']);

        $msg = 'Your account updated by Consultant ' 
                . $m_user->userinfo->fname. ' '
                .$m_user->userinfo->lname. "\r\n"
                .'username:'. $user->name."\r\n"
                .'password:'. $user->userinfo->pwd_code;

        $event_msg = $user->userinfo->fname.' '. $user->userinfo->lname .
                $user->role=='consul'?'Consultant':'Secretary'.
                ' account updated by ' 
                . $m_user->userinfo->fname. ' '
                . $m_user->userinfo->lname;

        $this->notification_open($user, $msg);
        
        event(new MissionNotify( $m_user->consultant->mission_id, $m_user->name ,asset(json_decode($m_user->userinfo->photo)->s) , $event_msg));


        return back()->with('success',__('Account information updated successfully'));
    }

    public function account_delete(Request $request)
    {
        $user = User::find($request['u_id']);
        $m_user = Auth::user();
        if ($user->role == 'consul') {
            Consultant::find($user->consultant->id)->delete();
        }
        if ($user->role == 'secret') {
            Secretary::find($user->secretary->id)->delete();
        }
        $msg = 'Your account deleted by ' 
                    .$m_user->userinfo->fname. ' '
                    .$m_user->userinfo->lname. "\r\n";
        $this->notification_open($user, $msg);

        UserInfo::find($user->userinfo->id)->delete();        
        $user->delete();
        return back()->with('success',__('Account deleted successfully'));
    }
    public function createThumbnail($path, $width, $height, $medium, $thumb_location)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(public_path($thumb_location).$medium);
    }

    public function notification_open($receiver,  $msg)
    {
        if($receiver->userinfo->notify_email)
            Notification::send( $receiver, new AccountCreated('mail', $receiver->email, $msg));
        if($receiver->userinfo->notify_phone)
            Notification::send( $receiver, new AccountCreated('sms',$receiver->userinfo->mobile, $msg));            
        if($receiver->userinfo->notify_whatsapp)
            Notification::send( $receiver, new AccountCreated('whatsapp',$receiver->userinfo->whatsapp,$msg));         
    }

    public function news()
    {
        $user = Auth::user();
        $page_title = __('News');      
        $news = MissionNews::orderBy('created_at','desc')->get();  
        return view('consul.news',compact('user','page_title','news')); 
    }
    public function save_news(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->consultant->mission->id;        
        $this->file_store_path = public_path();

        $filename = '';
        if ($request->hasFile('file')) {
       
            if(!is_dir($this->file_store_path ."/upload/news/". $mission_id ."/")) {
                mkdir($this->file_store_path ."/upload/news/". $mission_id ."/");
            }
            if(!is_dir($this->file_store_path ."/upload/news/". $mission_id ."/thumb/")) {
                mkdir($this->file_store_path ."/upload/news/". $mission_id ."/thumb/");
            }          

            $location = "upload/news/".$mission_id ."/";
            $thumb_location = "upload/news/".$mission_id ."/thumb/";
            //get filename with extension
            $filenamewithextension = $request->file('file')->getClientOriginalName();     
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);      
            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();      
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;     
            //small thumbnail name
            $smallthumbnail = $filename.'_small_'.time().'.'.$extension;     
            //medium thumbnail name
            $mediumthumbnail = $filename.'_medium_'.time().'.'.$extension;     
            //large thumbnail name
            $largethumbnail = $filename.'_large_'.time().'.'.$extension;     
            //Upload File
            $request->file('file')->move(public_path($location), $filenametostore);
            //create small thumbnail
            $smallthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($smallthumbnailpath, 150, 93, $smallthumbnail, $thumb_location);
            //create medium thumbnail
            $mediumthumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($mediumthumbnailpath, 300, 185,$mediumthumbnail,$thumb_location);
            //create large thumbnail
            $largethumbnailpath = public_path($location.$filenametostore);
            $this->createThumbnail($largethumbnailpath, 550, 340,$largethumbnail, $thumb_location);

            $filename = json_encode(
                [
                    'c'=> $location.$filenametostore,
                    's'=>$thumb_location.$smallthumbnail,
                    'm'=>$thumb_location.$mediumthumbnail,
                    'l'=>$thumb_location.$largethumbnail,
                ]
            );
        }

        MissionNews::create([
            'mission_id' => $mission_id,
            'title_en' => $request['title_en'],
            'title_ar' => $request['title_ar'],
            'link'=> $request['link'],
            'f_link' => $request['f_link'],
            'file' => $filename,
        ]);        
        return back()->with('success',__('Added new Mission News Link successfully'));
    }
    public function delete_news(Request $request)
    {
        $nid = $request['u_id'];
        MissionNews::find($nid)->delete();
        return back()->with('success',__('Deleted Mission News Link successfully'));
    }

}
