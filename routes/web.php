<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/test_email',function(){ return view('emails.contact-us');});
Route::get('/notify_email',function(){ 
    $text = 'aaaaaaa';
    $qrcode = asset('upload/client/447389870701__/qrcode.png');
    return view('emails.test_iraq',compact('text','qrcode'));
});

Route::get('/','LoginController@index' );
Route::get('m/{m_slug}/','LoginController@mission_start');
Route::get('m/{m_slug}/contact-us','LoginController@contact_us');
Route::post('/setlang','Controller@setLang');
Route::post('/contact-us/submit','LoginController@submit_contactus');

Route::get('/login', 'LoginController@showLoginPage');
Route::get('/logout','LoginController@logout');
Route::get('/bookings','BookingController@index');
Route::get('/booking/{base}','BookingController@booking');
Route::get('/booking/{mbase}/appointment/{sbase}','BookingController@booking_appointment');
Route::get('/bookinginfo', 'BookingController@booking_final');
Route::get('/bookingsuccess','BookingController@booking_success');
Route::get('/extra/{bid}/booking/{cid}/{mid}/{sid}','BookingController@extra_upload_page');
Route::get('/my-booking/{baseid}','BookingController@show_my_BookAppointment');
Route::get('/terms-conditions','ExtraController@terms_page');
Route::get('/privacy-policy','ExtraController@privacy_page');

Route::post('/login','LoginController@login_action');
Route::post('/booksubmit','BookingController@booking_final_post');
Route::post('/extrasubmit','BookingController@booking_extra_post');
Route::post('/verifyphone','Main\SmsController@sendSms');

/* new design routes */
Route::get('/getdata','BookingController@ajax_retrive_data');
Route::get('/getschs','BookingController@ajax_retrive_sch_data');
Route::get('/chkphone','Main\SmsController@sendSms');
Route::get('/rechkphone','Main\SmsController@sendSms');
Route::get('/chkverify','Main\SmsController@check_verify_code');
Route::get('/chkslot','BookingController@ajax_retrive_chk_slot');

Route::post('/finalsubmit','BookingController@final_post');

/* Timing Slots &&  Schedule && Holiday && Calendar Routes */
Route::get('timingslots','Main\ScheduleController@timingslots');    
Route::post('timingslots/addslot','Main\ScheduleController@timingslots_add_weekday');
Route::post('timingslots/delete','Main\ScheduleController@timingslots_delete');
Route::post('timingslots/setholiday','Main\ScheduleController@timingslots_holiday');
Route::post('timingslots/removeholiday','Main\ScheduleController@timingslots_remove_holiday');
Route::get('holidays','Main\ScheduleController@holidays');
Route::get('holidays/delete/{id}','Main\ScheduleController@holidays_delete');
Route::post('holidays/add','Main\ScheduleController@holidays_add');
Route::get('schedules','Main\ScheduleController@index');    
Route::get('schedules/addsingle','Main\ScheduleController@schedule_addsingle');
Route::get('schedules/addrange','Main\ScheduleController@schedule_addrange');
Route::get('schedules/reschedulesingle','Main\ScheduleController@schedule_reschedule');
Route::get('schedules/edit','Main\ScheduleController@edit');
Route::post('/saveschedule','Main\ScheduleController@schedule_save');
Route::post('/updateschedule','Main\ScheduleController@schedule_update'); 
Route::post('/schedules/delete','Main\ScheduleController@schedule_delete'); 
Route::post('/schedules/removeschedule','Main\ScheduleController@schedule_easy_delete');
//Calendar Routes
Route::get('/calendars','Main\CalendarController@index');
//Consul & Scretary Booking Routes
Route::get('/appointments','Main\AppointmentController@index');
Route::get('/appointments/viewbooking/{id}','Main\AppointmentController@view_booking_detail');
Route::post('/appointments/askfile','Main\AppointmentController@ask_booking_file');
Route::post('/appointments/decline','Main\AppointmentController@booking_decline');
Route::post('/appointments/finishmeeting','Main\AppointmentController@finish_meeting');
//SMS, Whasapp, Email Action Routes
Route::post('/sendsinglesms','Main\SmsController@send_single_Sms');
Route::post('/sendwhatsappsms','Main\WhatsappController@send_whatsapp_Sms');
Route::post('/sendsingleemail','Main\EmailController@send_single_email');
//Generate Meeting URL
Route::post('/appointment/generate_meetin_url','Main\MeetingController@generate_meeting_url');
//Client Management
Route::get('/clients','Main\ClientController@index');
Route::get('/clients/view/{id}','Main\ClientController@client_view');
//Meeting Management Routes
Route::get('/meetings','Main\MeetingController@index');
Route::get('/meetings/room/{base}','Main\MeetingController@room_meeting');
Route::get('/meetings/single/{base}','Main\MeetingController@single_meeting');
/*Real Time Chat room routes*/
Route::get('/open-meeting/{rid}','Main\ChatController@openMeeting');
Route::get('/room/{id}','Main\ChatController@clientRoomMeeting');
Route::get('/messages/{bkid}', 'Main\ChatController@fetchAllMessages');
Route::post('/messages', 'Main\ChatController@sendMessage');
Route::post('/endmeeting','Main\MeetingController@finish_meeting');
Route::post('/leave','Main\MeetingController@leave_meeting');

// Report Management
Route::get('/reports','Main\ReportController@index');
// Internal Chats between secretaries & consultants each other
Route::get('internal-chat','Main\InternalchatController@index');
Route::get('/internal-chat/open/{id}','Main\InternalchatController@open_chat');
Route::get('/internal-chat/messages','Main\InternalchatController@fetch_messages');
Route::post('/internal-chat/messages/','Main\InternalchatController@send_message');

/* Admin Routes */
Route::group(['prefix' => 'admin'], function(){
    /* GET */
    Route::get('dashboard', 'Admin\HomeController@index');
    Route::get('setting', 'Admin\HomeController@setting');
    Route::get('profile', 'Admin\HomeController@profile');
    Route::get('mission','Admin\MissionController@index');
    Route::get('mission/add','Admin\MissionController@add');
    Route::get('mission/edit/{id}','Admin\MissionController@edit');
    Route::get('consultant','Admin\ConsultantController@index');
    Route::get('consultant/add','Admin\ConsultantController@add');
    Route::get('consultant/edit/{id}','Admin\ConsultantController@edit');
    Route::get('consultant/edit/sub/{id}','Admin\ConsultantController@edit_subaccount');
    Route::get('secretary','Admin\ConsultantController@secretaries');
    Route::get('transactions','Admin\PaymentController@index');
    Route::get('appointments','Admin\BookingController@index');
    Route::get('clients','Admin\BookingController@clients');
    Route::get('reports','Admin\HomeController@reports');
    Route::get('terms','Admin\HomeController@terms_page');
    Route::get('policy','Admin\HomeController@policy_page');
    /* POST */

    Route::post('profile','Admin\HomeController@profile_update');
    Route::post('pwdchange','Admin\HomeController@pwd_update');
    Route::post('picupload','Admin\HomeController@pic_upload');
    Route::post('setting/{lang}','Admin\HomeController@update_site_setting');
    Route::post('extraterm/{lang}','Admin\HomeController@update_term_setting');
    Route::post('extrapolicy/{lang}','Admin\HomeController@update_policy_setting');
    Route::post('savemission','Admin\MissionController@mission_create');
    Route::post('updatemission','Admin\MissionController@mission_update');
    Route::post('deletemission','Admin\MissionController@mission_delete');
    Route::post('saveconsultant','Admin\ConsultantController@consultant_create');
    Route::post('updateconsultant','Admin\ConsultantController@consultant_update');
    Route::post('updatesubconsultant','Admin\ConsultantController@consultant_subupdate');
    Route::post('deleteconsultant','Admin\MissionController@consultant_delete');
    Route::post('paymentconfirmaction','Admin\PaymentController@payment_confirm');
});

/* Consul Routes */

Route::group(['prefix' => 'consul'], function(){
    /* GET */
    Route::get('dashboard', 'Consul\HomeController@index');
    Route::get('profile', 'Consul\HomeController@profile');
    Route::get('profile/{id}', 'Consul\HomeController@profile_subedit');
    Route::get('addaccount', 'Consul\HomeController@add_account');
    Route::get('payments','Consul\PaymentController@index');
    Route::get('payments/success','Consul\PaymentController@pay_success');
    Route::get('payments/fail','Consul\PaymentController@pay_fail');
    Route::get('sections','Consul\SectionController@index');
    Route::get('sections/add','Consul\SectionController@add');
    Route::get('sedit/{id}','Consul\SectionController@edit');  
    Route::get('msetting','Consul\SettingController@index');
    Route::get('news','Consul\HomeController@news');


    /* POST */
    Route::post('profile','Consul\HomeController@profile_update');
    Route::post('addaccount','Consul\HomeController@account_create');
    Route::post('updateaccount','Consul\HomeController@account_update');
    Route::post('deleteaccount','Consul\HomeController@account_delete');
    Route::post('pay','Consul\PaymentController@pay_action');
    Route::post('sections/add','Consul\SectionController@section_create');
    Route::post('sections/update','Consul\SectionController@section_update');
    Route::post('deletesection','Consul\SectionController@section_delete');
    Route::post('msetting/save','Consul\SettingController@save_setting');

    Route::post('news/save','Consul\HomeController@save_news');
    Route::post('deletenews','Consul\HomeController@delete_news');
});

/* Secretary Routes */
Route::group(['prefix' => 'secret'], function(){
    /* GET */
    Route::get('dashboard', 'Secretary\HomeController@index');
    Route::get('profile', 'Secretary\HomeController@profile');
    Route::get('sections','Secretary\SectionController@index');
    Route::get('sections/add','Secretary\SectionController@add');
    Route::get('sedit/{id}','Secretary\SectionController@edit');
    Route::get('news','Secretary\HomeController@news');
    /* POST */
    Route::post('profile','Secretary\HomeController@profile_update');
    Route::post('updateaccount','Secretary\HomeController@account_update');
    Route::post('sections/add','Secretary\SectionController@section_create');
    Route::post('sections/update','Secretary\SectionController@section_update');
    Route::post('deletesection','Secretary\SectionController@section_delete');
    Route::post('news/save','Secretary\HomeController@save_news');
    Route::post('deletenews','Secretary\HomeController@delete_news');
});
