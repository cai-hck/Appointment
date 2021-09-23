<?php

namespace App\Http\Controllers\Consul;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\MissionSetting;
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
class SettingController extends Controller
{
    public $file_store_path;

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
        $page_title = __('Mission Settings');  
        $mission = Mission::find($user->consultant->mission->id);

        $mission_setting = MissionSetting::where('mission_id', $mission->id)->get()->first();

        return view('consul.setting',compact('user','page_title','sections','mission','mission_setting'));
    }

    public function save_setting(Request $request)
    {
        $this->file_store_path = public_path();

        //dd($this->file_store_path);

        //dd($request->all());
        $user = Auth::user();
        $current_mission = Mission::find($user->consultant->mission->id);

        $logo_filename = '';
        if ($request->hasFile('logo')) {
            if(!is_dir($this->file_store_path ."/upload/mission/".$current_mission->id)) mkdir($this->file_store_path ."/upload/mission".$current_mission->id); 

            $location = "upload/mission/".$current_mission->id.'/';
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
            $logo_filename = $location.$mediumthumbnail;
        }
        //Mission Names Update
        $current_mission->name = $request['mname_en'];
        $current_mission->name_ar = $request['mname_ar'];
        $current_mission->save();

        //Mission Setting update
        $exist = MissionSetting::where('mission_id', $current_mission->id)->get()->first();
        if ($exist) {
            if ($logo_filename != '') $logo_filename = $exist->logo;
            MissionSetting::where('id', $exist->id)->update([
                'mission_id' => $current_mission->id,
                'logo' => $logo_filename,
                'contact_no' =>$request['contact_number'],
                'contact_email' => $request['contact_email'],
                'contact_address' => $request['contact_address'],
                'description_en' => $request['description_en'],
                'description_ar' => $request['description_ar'],
                'email_subject_en' => $request['email_subject_en'],
                'email_subject_ar' => $request['email_subject_ar'],
                'slug' => $request['slug']
            ]);
        } else {
            MissionSetting::create([
                'mission_id' => $current_mission->id,
                'logo' => $logo_filename,
                'contact_no' =>$request['contact_number'],
                'contact_email' => $request['contact_email'],
                'contact_address' => $request['contact_address'],
                'description_en' => $request['description_en'],
                'description_ar' => $request['description_ar'],
                'email_subject_en' => $request['email_subject_en'],
                'email_subject_ar' => $request['email_subject_ar'],
                'slug' => $request['slug']
            ]);
        }

        return back()->with('success','Updated Mission Setting successfully');
    }

        
    public function createThumbnail($path, $width, $height, $medium, $thumb_location)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(public_path($thumb_location).$medium);
    }

}