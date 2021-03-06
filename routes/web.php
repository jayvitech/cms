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

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', 'AuthController@index');
Route::post('post-login', 'AuthController@postLogin');
Route::get('register', 'AuthController@register');
Route::post('post-register', 'AuthController@postRegister');

Route::get('dashboard', 'AuthController@dashboard');
Route::get('users', 'AuthController@dashboard');
Route::get('logout', 'AuthController@logout')->name('logout');

/* handle user request */
Route::get('change-request-status/{user_id}/{request_status}', 'AuthController@changeRequestStatus');

/* handle filter */
Route::get('call-filter/{value}', 'AuthController@callFilter');
Route::get('call-filter-hobby/{value}', 'AuthController@callFilterHobby');

/* user history */
Route::get('user-history', 'UserController@userHistory');
