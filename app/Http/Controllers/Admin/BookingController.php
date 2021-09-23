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
use App\Booking;
use App\Client;


use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
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

        $bookings = Booking::orderby('created_at','desc')->get();
        return view('admin.booking.list', compact('user','bookings'));
    }
    public function clients()
    {
        $user = Auth::user();
        $clients = Client::all();

        return view('admin.booking.clients',compact('user','clients'));
    }
}