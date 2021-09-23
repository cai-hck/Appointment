<?php

namespace App\Http\Controllers\Secretary;

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
                if ($user->role == 'secret')
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
        $today_bookings = Booking::where('mission_id', $user->secretary->mission->id)->where('schedule_date', date('Y-m-d'))->get();
        return view('secret.dashboard',compact('user','page_title','today_bookings'));
    }
    public function profile()
    {
        $user = Auth::user();
        $page_title = __('Profile');        
        return view('secret.profile',compact('user','page_title'));
    }    

    public function profile_update(Request $request)
    {
        $user = Auth::user();
        $this->file_store_path = public_path();
        $pwd_code = $request['password']?$request['password']:$user->userinfo->pwd_code;
        
        User::where('id', $user->id)->update([
            'name'=>$request['username'],
            'role'=>'secret',
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

    
    public function news()
    {
        $user = Auth::user();
        $page_title = __('News');      
        $news = MissionNews::orderBy('created_at','desc')->get(); 

        return view('secret.news',compact('user','page_title','news')); 
    }
    public function save_news(Request $request)
    {
        $user = Auth::user();
        $mission_id = $user->secretary->mission->id;
        MissionNews::create([
            'mission_id' => $mission_id,
            'title_en' => $request['title_en'],
            'title_ar' => $request['title_ar'],
            'link'=> $request['link']
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