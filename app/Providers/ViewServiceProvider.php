<?php

namespace App\Providers;

use App\Http\View\Composers\ProfileComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


use App\SiteSetting;
use App;
use Config;
use App\Mission;
use App\MissionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using closure based composers...
        View::composer('*', function ($view) {
            //
            $config['facebook'] = SiteSetting::get_value('facebook_url');
            $config['instagram'] = SiteSetting::get_value('instagram_url');
            $config['linkedin'] = SiteSetting::get_value('linkedin_url');
            $config['youtube'] = SiteSetting::get_value('youtube_url');
            $config['twitter'] = SiteSetting::get_value('twitter_url');
            $config['dribble'] = SiteSetting::get_value('dribble_url');

            if (App::getLocale() == 'ar') {
                $config['website_name'] = SiteSetting::get_value('ar_website_name');
                $config['address'] = SiteSetting::get_value('ar_address');
                $config['contact_number'] = SiteSetting::get_value('ar_contact_number');
                $config['contact_email'] = SiteSetting::get_value('ar_contact_email');
                $config['description'] = SiteSetting::get_value('ar_description');
                $config['logo'] = SiteSetting::get_value('ar_logo');
                $config['icon'] = SiteSetting::get_value('ar_icon');
           }         
           if (App::getLocale() == 'en') {
                $config['website_name'] = SiteSetting::get_value('en_website_name');
                $config['address'] = SiteSetting::get_value('en_address');
                $config['contact_number'] = SiteSetting::get_value('en_contact_number');
                $config['contact_email'] = SiteSetting::get_value('en_contact_email');
                $config['description'] = SiteSetting::get_value('en_description');
                $config['logo'] = SiteSetting::get_value('en_logo');
                $config['icon'] = SiteSetting::get_value('en_icon');           
           }         

            if (session()->has('mslug') && session()->get('mslug')!='' ) {
                $mslug = session()->get('mslug');
                $m_setting = MissionSetting::where('slug', $mslug)->get()->first();
                $m = Mission::where('id', $m_setting->mission_id)->get()->first();
                if (App::getLocale() == 'ar') {
                    $config['description'] =  $m_setting->description_ar;
                    $config['email_subject'] = $m_setting->email_subject_ar;
                    $config['website_name'] = $m->name_ar;
                }
                if (App::getLocale() == 'en') {
                    $config['description'] =  $m_setting->description_en;
                    $config['email_subject'] = $m_setting->email_subject_en;
                    $config['website_name'] = $m->name;
                }

                $config['address'] = $m_setting->contact_address;
                $config['contact_number'] =  $m_setting->contact_no;
                $config['contact_email'] =  $m_setting->contact_email;
                $config['logo'] =  $m_setting->logo;
            }
           $view->with('config', $config);
        });
    }
}