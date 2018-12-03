<?php

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
use Illuminate\Support\Facades\Cache;
Route::get('/', 'PagesController@root')->name('root');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');

    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    //
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');
});

//Route::get('/aaa', function (){
//
//   //dd( Cache::put('bargain_create_source_log','111111'));
//    Cache::put('bargain_create_source_log','111111',1);
//    dd(Cache::get('bargain_create_source_log'));
//});
//
//Route::get('bbb', function (){
//  dd(Cache::get('bargain_create_source_log'));
//});

Route::group(['middleware' => 'email_verified'], function() {
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
});