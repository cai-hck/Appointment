<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\ExtraSetting;
use App\Mission;
use App\Section;
use App\Client;
use App\Consultant;
use App\Secretary;
use App\Holiday;
use App\Schedule;
use App\ScheduleTiming;
use App\Transaction;
use App\Booking;
use App\AddLink;
use App\BookingFie;


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

        $clients = Client::all()->count();
        $consultants = Consultant::all()->count();
        $secretary = Secretary::all()->count();
        $mission = Mission::all()->count();        
        $bookings = Booking::all()->count();
        $finished_bookings = Booking::where('status','finished')->get()->count();
        $declined_bookings = Booking::where('status','declined')->get()->count();
        $upcoming_bookings = Booking::where('status','approved')->get()->count();
        $today_bookings = Booking::where('schedule_date', date('Y-m-d'))->get()->count();
        $transactions = Transaction::all()->count();
        $earn = Transaction::all()->sum('amount');

        return view('admin.dashboard',compact('user','clients', 'consultants','secretary','mission','bookings','finished_bookings','declined_bookings','upcoming_bookings','today_bookings','transactions','earn'));

    }

    public function setting()
    {
        $user = Auth::user();

        //english
        $setting['en_contact_number'] = SiteSetting::get_value('en_contact_number');
        $setting['en_contact_email'] = SiteSetting::get_value('en_contact_email');
        $setting['en_address'] = SiteSetting::get_value('en_address');
        $setting['en_logo'] = SiteSetting::get_value('en_logo');
        $setting['en_description'] = SiteSetting::get_value('en_description');        
        $setting['en_website_name'] = SiteSetting::get_value('en_website_name');
        $setting['en_icon'] = SiteSetting::get_value('en_icon');

        //arabic
        $setting['ar_contact_number'] = SiteSetting::get_value('ar_contact_number');
        $setting['ar_contact_email'] = SiteSetting::get_value('ar_contact_email');
        $setting['ar_address'] = SiteSetting::get_value('ar_address');
        $setting['ar_logo'] = SiteSetting::get_value('ar_logo');
        $setting['ar_description'] = SiteSetting::get_value('ar_description');        
        $setting['ar_website_name'] = SiteSetting::get_value('ar_website_name');
        $setting['ar_icon'] = SiteSetting::get_value('ar_icon');

        $setting['facebook'] = SiteSetting::get_value('facebook_url');
        $setting['linkedin'] = SiteSetting::get_value('linkedin_url');
        $setting['twitter'] = SiteSetting::get_value('twitter_url');
        $setting['instagram'] = SiteSetting::get_value('instagram_url');
        $setting['dribble'] = SiteSetting::get_value('dribble_url');
        $setting['youtube'] = SiteSetting::get_value('youtube_url');



        return view('admin.setting',compact('user','setting'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile',compact('user'));
    }



    /* POST functions */
    public function profile_update(Request $request )
    {  
        $aid = $request['admin_id'];
        $user = Auth::user();       
        User::select()->where('id', $aid)->update(['name'=>$request['username'],'email'=>$request['email']]);

        if ($user->userinfo) {
            UserInfo::select()->where('user_id', $aid)->update(['fname'=>$request['fname'],'lname'=>$request['lname'],'mobile'=>$request['mobile'],'whatsapp'=>$request['whatsapp'],'address'=>$request['address']]);
        } else {
            UserInfo::create(['user_id'=>$aid,'photo'=>'','fname'=>$request['fname'],'lname'=>$request['lname'],'mobile'=>$request['mobile'],'whatsapp'=>$request['whatsapp'],'address'=>$request['address']]);
        }

        return back()->with('success','Updated Profile information successfully');
    }
    public function pwd_update(Request $request)
    {
        if ($request['new'] != $request['confirm']) {
            return back()->with('error','Does not match new Password and Confirmation Password');
        } else {
            $user = Auth::user();
            User::select()->where('id', $user->id)->update(['password'=>bcrypt($request['new'])]);
            if ($user->userinfo) {
                UserInfo::select()->where('user_id', $user->id)->update(['pwd_code'=>$request['new']]);
            }
            return back()->with('success','Password updated successfully');
        }       
    }
    public function pic_upload(Request $request)
    {
        $user = Auth::user();    
        $this->file_store_path = public_path();
        if($request->hasFile('photo')) {

            if(!is_dir(  $this->file_store_path ."/upload/user/". $user->id ."/")) {
                mkdir( $this->file_store_path ."/upload/user/". $user->id ."/");
            }

            if(!is_dir( $this->file_store_path ."/upload/user/". $user->id ."/thumb/")) {
                mkdir(  $this->file_store_path ."/upload/user/". $user->id ."/thumb/");
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
                  
            return back()->with('success', "Photo uploaded successfully.");
        } else{
            return back()->with('error', "Photo required.");
        }
    }
    
    public function createThumbnail($path, $width, $height, $medium, $thumb_location)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(public_path($thumb_location).$medium);
    }

    public function update_site_setting($lang, Request $request)
    {
        $this->file_store_path = public_path();
        if ($lang == 'en')
        {            
            $this->save_setting('en_contact_number', $request['contact_number']);
            $this->save_setting('en_contact_email', $request['contact_email']);
            $this->save_setting('en_address', $request['address']);
            $this->save_setting('en_website_name', $request['website_name']);
            if ($request->hasFile('logo')) {
                if(!is_dir($this->file_store_path ."/upload/logo/")) mkdir($this->file_store_path ."/upload/logo"); 
                $location = "upload/logo/";
                //get filename with extension
                $filenamewithextension = $request->file('logo')->getClientOriginalName();        
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
                //get file extension
                $extension = $request->file('logo')->getClientOriginalExtension();        
                //filename to store
                $filenametostore = $filename.'_en_'.time().'.'.$extension;       
                //medium thumbnail name
                $mediumthumbnail = $filename.'_en_logo_'.time().'.'.$extension;        
                //Upload File
                $request->file('logo')->move(public_path($location), $filenametostore);
                //create medium thumbnail
                $mediumthumbnailpath = public_path($location.$filenametostore);
                $this->createThumbnail($mediumthumbnailpath, 201, 52,$mediumthumbnail,$location);
                $this->save_setting('en_logo', $location.$mediumthumbnail);
            }
            $this->save_setting('en_description', $request['description']);
            if ($request->hasFile('icon')) {
                if(!is_dir($this->file_store_path ."/upload/logo/")) mkdir($this->file_store_path ."/upload/logo"); 
                $location = "upload/logo/";
                //get filename with extension
                $filenamewithextension = $request->file('icon')->getClientOriginalName();        
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
                //get file extension
                $extension = $request->file('icon')->getClientOriginalExtension();        
                //filename to store
                $filenametostore = $filename.'_ar_'.time().'.'.$extension;       
                //medium thumbnail name
                $mediumthumbnail = $filename.'_ar_logo_'.time().'.'.$extension;        
                //Upload File
                $request->file('icon')->move(public_path($location), $filenametostore);
                //create medium thumbnail
                $mediumthumbnailpath = public_path($location.$filenametostore);
                $this->createThumbnail($mediumthumbnailpath, 201, 52,$mediumthumbnail,$location);
                $this->save_setting('en_icon', $location.$mediumthumbnail);
            }
            return back()->with('success','Site setting English version has been updated successfully');
        }
        if ($lang == 'ar') 
        {
           
            $this->save_setting('ar_contact_number', $request['contact_number']);
            $this->save_setting('ar_contact_email', $request['contact_email']);
            $this->save_setting('ar_address', $request['address']);
            $this->save_setting('ar_website_name', $request['website_name']);
            if ($request->hasFile('logo')) {
                if(!is_dir($this->file_store_path ."/upload/logo/")) mkdir($this->file_store_path ."/upload/logo"); 
                $location = "upload/logo/";
                //get filename with extension
                $filenamewithextension = $request->file('logo')->getClientOriginalName();        
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
                //get file extension
                $extension = $request->file('logo')->getClientOriginalExtension();        
                //filename to store
                $filenametostore = $filename.'_en_'.time().'.'.$extension;       
                //medium thumbnail name
                $mediumthumbnail = $filename.'_en_logo_'.time().'.'.$extension;        
                //Upload File
                $request->file('logo')->move(public_path($location), $filenametostore);
                //create medium thumbnail
                $mediumthumbnailpath = public_path($location.$filenametostore);
                $this->createThumbnail($mediumthumbnailpath, 201, 52,$mediumthumbnail,$location);
                $this->save_setting('ar_logo', $location.$mediumthumbnail);
            }
            $this->save_setting('ar_description', $request['description']);
            if ($request->hasFile('icon')) {
                if(!is_dir($this->file_store_path ."/upload/logo/")) mkdir($this->file_store_path ."/upload/logo"); 
                $location = "upload/logo/";
                //get filename with extension
                $filenamewithextension = $request->file('icon')->getClientOriginalName();        
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
                //get file extension
                $extension = $request->file('icon')->getClientOriginalExtension();        
                //filename to store
                $filenametostore = $filename.'_ar_'.time().'.'.$extension;       
                //medium thumbnail name
                $mediumthumbnail = $filename.'_ar_logo_'.time().'.'.$extension;        
                //Upload File
                $request->file('icon')->move(public_path($location), $filenametostore);
                //create medium thumbnail
                $mediumthumbnailpath = public_path($location.$filenametostore);
                $this->createThumbnail($mediumthumbnailpath, 201, 52,$mediumthumbnail,$location);
                $this->save_setting('ar_icon', $location.$mediumthumbnail);
            }
            return back()->with('success','Site setting Arabic version has been updated successfully');
        }
        
        if ($lang == 'social')
        {
            $this->save_setting('facebook_url', $request['facebook']);
            $this->save_setting('twitter_url', $request['twitter']);
            $this->save_setting('linkedin_url', $request['linkedin']);
            $this->save_setting('youtube_url', $request['youtube']);
            $this->save_setting('instagram_url', $request['instagram']);
            $this->save_setting('dribble_url', $request['dribble']);

            return back()->with('success','Site social media setting has been updated successfully');
        }
    }

    
    public function save_setting($name, $value)
    {
        $exist = SiteSetting::select()->where('name', $name)->get()->first();
        if ($value != '' && $name != '') {
            if ($exist) {
                SiteSetting::select()->where('name', $name)->update(['value'=>$value]);
            } else { 
                SiteSetting::create(['name'=>$name,'value'=>$value]);
            }
        }
    }

    public function save_extra_setting($name, $value)
    {
        $exist = ExtraSetting::select()->where('name', $name)->get()->first();
        if ($value != '' && $name != '') {
            if ($exist) {
                ExtraSetting::select()->where('name', $name)->update(['value'=>$value]);
            } else { 
                ExtraSetting::create(['name'=>$name,'value'=>$value]);
            }
        }
    }

    public function reports()
    {
        $user = Auth::user();
        $start = isset($_GET['date_start'])?$_GET['date_start']: '';
        $end = isset($_GET['date_end'])?$_GET['date_end']: '';

        $mission = Mission::all()->count();        

        // All info
        $mission = [];
        $all_missions = Mission::all();
        
        foreach ($all_missions as $one) {
            $tp = [];
            $tp['name'] = [$one->name,$one->name_ar];
            $tp['no_consultant'] = Consultant::where('mission_id', $one->id)->get()->count();
            $tp['no_secretary'] = Secretary::where('mission_id', $one->id)->get()->count();
            $tp['no_bookings'] = Booking::where('mission_id', $one->id)->get()->count();
            $mission[] = $tp;
        }

        $bookings = [];
        foreach ($all_missions as $one) {
            $tp = [];
            $tp['name'] = [$one->name, $one->name_ar];
            $tp['all'] = Booking::where('mission_id', $one->id)->get()->count();
            $tp['finished'] = Booking::where('status','finished')->where('mission_id', $one->id)->get()->count();
            $tp['upcoming'] = Booking::where('status','approved')->where('mission_id', $one->id)->get()->count();
            $tp['declined'] = Booking::where('status','declined')->where('mission_id', $one->id)->get()->count();
            $bookings[] = $tp;
        }
        $today_bookings = [];
        foreach ($all_missions as $one) {
            $tp = [];
            $tp['name'] = [$one->name, $one->name_ar];
            $tp['all'] = Booking::where('mission_id', $one->id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $tp['finished'] = Booking::where('status','finished')->where('mission_id', $one->id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $tp['upcoming'] = Booking::where('status','approved')->where('mission_id', $one->id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $tp['declined'] = Booking::where('status','declined')->where('mission_id', $one->id)->where('schedule_date', date('Y-m-d'))->get()->count();
            $today_bookings[] = $tp;
        }

        $clients = [];
        foreach ($all_missions as $one) {
            $tp = [];
            $tp['name'] = [$one->name, $one->name_ar];
            $tp['all'] = Client::where('mission_id', $one->id)->get()->count();      
            $clients[] = $tp;
        }
           
        $transactions = [];
        $total_earn = 0;
        foreach ($all_missions as $one) {
            $tp = [];
            $tp['name'] = [$one->name, $one->name_ar];
            $tp['earn'] = Transaction::where('consultant_id', $one->consultant->id)->get()->sum('amount');      
            $total_earn+=$tp['earn'];
            $transactions[] = $tp;
        }
       
        return view('admin.report',compact('user','start','end','mission','bookings','today_bookings','clients','transactions','total_earn'));
    }

    public function terms_page()
    {
        $user = Auth::user();
        $setting['en_term'] = ExtraSetting::get_value('en_term');
        $setting['ar_term'] = ExtraSetting::get_value('ar_term');
        return view('admin.terms',compact('user','setting'));
    }

    public function policy_page()
    {
        $user = Auth::user();
        $setting['en_policy'] = ExtraSetting::get_value('en_policy');
        $setting['ar_policy'] = ExtraSetting::get_value('ar_policy');

        return view('admin.policy',compact('user','setting'));
    }    
    public function update_term_setting($lang, Request $request)
    {
        if ($lang == 'en')
        {            
            $this->save_extra_setting('en_term', $request['en_about']);
            return back()->with('success','English version has been updated successfully');
        }
        if ($lang == 'ar') 
        {
           
            $this->save_extra_setting('ar_term', $request['ar_about']);
            return back()->with('success','Arabic version has been updated successfully');
        }
    }
    public function update_policy_setting($lang, Request $request)
    {
        if ($lang == 'en')
        {            
            $this->save_extra_setting('en_policy', $request['en_about']);
            return back()->with('success','English version has been updated successfully');
        }
        if ($lang == 'ar') 
        {
           
            $this->save_extra_setting('ar_policy', $request['ar_about']);
            return back()->with('success','Arabic version has been updated successfully');
        }
    }    
}
?>
