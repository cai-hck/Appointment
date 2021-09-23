<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App;
use App\SiteSetting;
use View;

use App\Channels\SmsChannel;
use App\Channels\WhatsappChannel;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Notification::extend('sms', function ($app) {
            return new SmsChannel();
        });
        Notification::extend('whatsapp', function ($app) {
            return new WhatsappChannel();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
