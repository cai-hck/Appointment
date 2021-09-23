<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\SiteSetting;
use View;
use App;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function setLang(Request $requset){
          $locale = $requset['locale'];               
          App::setlocale($locale);
          session()->put('locale', $locale);             
          return redirect()->back();          
    }
}

