<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\ExtraSetting;

use Carbon\Carbon;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExtraController extends Controller
{
    public function terms_page()
    {
        $user = Auth::user();
        $setting['en_term'] = ExtraSetting::get_value('en_term');
        $setting['ar_term'] = ExtraSetting::get_value('ar_term');
        $page_title = __('Terms and Conditions');
        return view('terms',compact('user','page_title','setting'));
    }
    public function privacy_page()
    {
        $user = Auth::user();
        $setting['en_policy'] = ExtraSetting::get_value('en_policy');
        $setting['ar_policy'] = ExtraSetting::get_value('ar_policy');
        $page_title = __('Privacy Policy');
        return view('policy',compact('user','page_title','setting'));
    }
}