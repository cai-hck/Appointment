<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\Consultant;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

class MissionController extends Controller
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
        $consuls = Mission::all();        
        return view('admin.mission.list',compact('user','consuls'));
    }
    public function add()
    {
        $user = Auth::user();
        $consuls = Consultant::where('type','main')->where('status',false)->where('mission_id',0)->get();
        return view('admin.mission.add',compact('user','consuls'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $mission = Mission::find($id);
        $consuls = Consultant::where('type','main')->get();
        return view('admin.mission.edit',compact('user','mission','consuls'));
    }

    public function mission_create(Request $request)
    {
        $this->file_store_path = public_path();
        if($request->hasFile('cover')) {
            if(!is_dir($this->file_store_path ."/upload/mission/")) {
                mkdir($this->file_store_path ."/upload/mission/");
            }
            $location = "upload/mission/";
            //get filename with extension
            $filenamewithextension = $request->file('cover')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
            //get file extension
            $extension = $request->file('cover')->getClientOriginalExtension();        
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;
            //Upload File
            $request->file('cover')->move(public_path($location), $filenametostore);
            
            $mission = Mission::create([
                'name'=>$request['name'],
                'name_ar' => $request['name_ar'],
                'consultant_id'=>$request['consul']==null? 0 :$request['consul'],
                'number_of_users'=>$request['users_cnt'],
                'cost_per_user'=>$request['cost'],
                'description'=>$request['about']==null?'':$request['about'],
                'cover_image'=>$location.$filenametostore,
                'active_date'=>'',
                'expire_date'=>'',
                'status'=>$request['consul']==null?false:true,
            ]);
            
            if ($request['consul'] != null)
                Consultant::where('id', $request['consul'])->update(['mission_id'=>$mission->id]);

            if ($request['consul'] == '')
                return back()->with('success','New mission created! By editing the mission,  Please assign the main Consultant.');
            else
                return back()->with('success','Created New Mission successfully!');  
        } 
        
    }

    public function mission_update(Request $request)
    {
        $mid = $request['m_id'];

        if($request->hasFile('cover')) {
            if(!is_dir("./upload/mission/")) {
                mkdir("./upload/mission/");
            }
            $location = "upload/mission/";
            //get filename with extension
            $filenamewithextension = $request->file('cover')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);        
            //get file extension
            $extension = $request->file('cover')->getClientOriginalExtension();        
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;
            //Upload File
            $request->file('cover')->move(public_path($location), $filenametostore);
            
            Mission::where('id', $mid)->update([
                'name'=>$request['name'],
                'name_ar' => $request['name_ar'],
                'consultant_id'=>$request['consul']==null? 0 :$request['consul'],
                'number_of_users'=>$request['users_cnt'],
                'cost_per_user'=>$request['cost'],
                'description'=>$request['about'],
                'cover_image'=>$location.$filenametostore,
                'active_date'=>'',
                'expire_date'=>'',
                'status'=>$request['consul']==null?false:true,
            ]);

            if ($request['consul'] == '')
                return back()->with('success','Mission updated! By editing the mission,  Please assign the main Consultant.');
            else
                return back()->with('success','Updated  Mission successfully!');  
        } else {
            Mission::where('id', $mid)->update([
                'name'=>$request['name'],
                'name_ar' => $request['name_ar'],
                'consultant_id'=>$request['consul']==null? 0 :$request['consul'],
                'number_of_users'=>$request['users_cnt'],
                'cost_per_user'=>$request['cost'],
                'description'=>$request['about'],
                'active_date'=>'',
                'expire_date'=>'',
                'status'=>$request['consul']==null?false:true,
            ]);
            if ($request['consul'] == '')
                return back()->with('success','Mission updated! By editing the mission,  Please assign the main Consultant.');
            else {
                Consultant::where('id', $request['consul'])->update(['mission_id'=>$mid]);
                return back()->with('success','Updated  Mission successfully!');  
            }
        }
    }

    public function mission_delete(Request $request)
    {
        //dd($request['m_id']);
        $mission = Mission::find($request['m_id']);
        if ($mission->consultant) {
            Consultant::where('id', $mission->consultant_id)->update(['mission_id'=>0,'active_date'=>'','expire_date'=>'','status'=>false]);
            $mission->delete();                        
        }
        return back()->with('success','Mission deleted successfully');
    }


}