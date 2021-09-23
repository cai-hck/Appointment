<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\UserInfo;
use App\SiteSetting;
use App\Mission;
use App\Consultant;
use App\Secretary;

use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

class ConsultantController extends Controller
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
        $consuls =  Consultant::select()->where('type','main')->get();
        return view('admin.consul.list',compact('user','consuls'));
    }

    public function secretaries()
    {
        $user = Auth::user();
        $secrets =  Secretary::all();
        return view('admin.consul.list_screts',compact('user','secrets'));
    }

    public function add()
    {
        $user = Auth::user();
        return view('admin.consul.add',compact('user'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $consul = Consultant::find($id);
        return view('admin.consul.edit',compact('user','consul'));
    }

    public function edit_subaccount($id)
    {
        $user = Auth::user();
        $edit_user = User::find($id);
        if ($edit_user->role == 'consul')
            $consul = Consultant::find($edit_user->consultant->id);
        if ($edit_user->role == 'secret')        
            $consul = Secretary::find($edit_user->secretary->id);

        return view('admin.consul.sub_edit',compact('user','consul'));
    }

    public function consultant_create(Request $request)
    {

        $this->file_store_path = public_path();
        //dd($request->all());
        if($request->hasFile('photo')) {

            $user = User::create([
                'name' => $request['username'],
                'email' => $request['email'],
                'password'=>bcrypt($request['password']),
                'role' => 'consul'
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
                'user_id'=>$user->id,
                'fname' => $request['fname'],
                'lname' => $request['lname'],
                'mobile'=> $request['mobile'],
                'whatsapp'=> $request['whatsapp'],
                'address'=> $request['address'],
                'photo' => json_encode(
                    [
                        'c'=> $location.$filenametostore,
                        's'=>$thumb_location.$smallthumbnail,
                        'm'=>$thumb_location.$mediumthumbnail,
                        'l'=>$thumb_location.$largethumbnail,
                    ]),
                'pwd_code' => $request['password']
            ]);
        
            Consultant::create([
                'user_id'=> $user->id,
                'mission_id' => 0,
                'type'=>'main',
                'number_of_subs' => $request['users_cnt'],
            ]);
                  
            return back()->with('success', "Main Consultant created successfully. Please assign main consultant to Mission.");
        } else{
            return back()->with('error', "Photo required.");
        }
    }


    public function consultant_update(Request $request)
    {
        $c_id = $request['c_id'];
        $consul = Consultant::find($c_id);
        $user = $consul->user;

        $this->file_store_path = public_path();
        
        if($request->hasFile('photo')) {
            User::where('id',$consul->user_id)->update([
                'name' => $request['username'],
                'email' => $request['email'],
                'password'=>bcrypt($request['password']),
                'role' => 'consul'
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
            UserInfo::where('id',$user->userinfo->id)->update([
                'user_id'=>$user->id,
                'fname' => $request['fname'],
                'lname' => $request['lname'],
                'mobile'=> $request['mobile'],
                'whatsapp'=> $request['whatsapp'],
                'address'=> $request['address'],
                'photo' => json_encode(
                    [
                        'c'=> $location.$filenametostore,
                        's'=>$thumb_location.$smallthumbnail,
                        'm'=>$thumb_location.$mediumthumbnail,
                        'l'=>$thumb_location.$largethumbnail,
                    ]),
                'pwd_code' => $request['password']
            ]);
        
            Consultant::where('id',$c_id)->update([
                'user_id'=> $user->id,
                'type'=>'main',
                'number_of_subs' => $request['users_cnt'],
            ]);
            if ($consul->mission_id == 0)
                return back()->with('success', "Main Consultant updated successfully. Please assign main consultant to Mission.");
            else
                return back()->with('success', "Main Consultant updated successfully.");
        } else {

            User::where('id',$consul->user_id)->update([
                'name' => $request['username'],
                'email' => $request['email'],
                'password'=>bcrypt($request['password']),
                'role' => 'consul'
            ]);
            UserInfo::where('id',$user->userinfo->id)->update([
                'user_id'=>$user->id,
                'fname' => $request['fname'],
                'lname' => $request['lname'],
                'mobile'=> $request['mobile'],
                'whatsapp'=> $request['whatsapp'],
                'address'=> $request['address'],               
                'pwd_code' => $request['password']
            ]);
        
            Consultant::where('id',$c_id)->update([
                'user_id'=> $user->id,
                'type'=>'main',
                'number_of_subs' => $request['users_cnt'],
            ]);

            if ($consul->mission_id == 0)
                return back()->with('success', "Main Consultant updated successfully. Please assign main consultant to Mission.");
            else
                return back()->with('success', "Main Consultant updated successfully.");
        }
    }

    public function consultant_subupdate(Request $request)
    {
        $c_id = $request['c_id'];
        $u_id = $request['u_id'];
        $edit_user = User::find($u_id);

        if ($edit_user->role == 'consul')
            $consul = Consultant::find($edit_user->consultant->id);
        if ($edit_user->role == 'secret')        
            $consul = Secretary::where('user_id', $edit_user->id)->get()->first();

        User::where('id',$consul->user_id)->update([
            'name' => $request['username'],
            'email' => $request['email'],
            'password'=>bcrypt($request['password']),
            'role' => $edit_user->role
        ]);
        UserInfo::where('id',$edit_user->userinfo->id)->update([
            'user_id'=>$edit_user->id,
            'fname' => $request['fname'],
            'lname' => $request['lname'],
            'mobile'=> $request['mobile'],
            'whatsapp'=> $request['whatsapp'],
            'address'=> $request['address'],               
            'pwd_code' => $request['password']
        ]);

        return back()->with('success','Updated sub account successfully');
    }
    public function createThumbnail($path, $width, $height, $medium, $thumb_location)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(public_path($thumb_location).$medium);
    }


    public function consultant_delete(Request $request)
    {
        //dd($request['m_id']);
        $consul = Consultant::find($request['c_id']);
        //Remove all related scretary , unlink Mission, 

        return back()->with('success','Consultant deleted successfully');
    }

}