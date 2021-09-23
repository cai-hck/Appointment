<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('access_token', 'Main\VideoController@generate_token');
Route::post('call_request', 'Main\VideoController@sendCallRequest');
Route::post('end_request', 'Main\VideoController@endCallRequest');
Route::post('decline_request', 'Main\VideoController@declineCallRequest');

Route::get('internal_token', 'Main\VideoController@generate_internal_token');
Route::post('internal_call_request','Main\VideoController@call_request');
Route::post('decline_internal_request','Main\VideoController@decline_request');
Route::post('end_internal_request','Main\VideoController@end_request');