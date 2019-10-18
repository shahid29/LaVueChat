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

//Route::get('/home', function () {
//    return view('welcome');
//});


Auth::routes();


Route::get('/', 'HomeController@redirect');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/userlist', 'MessageController@userlist');
Route::get('/usermessage/{id}', 'MessageController@usermessage');
Route::post('/sendmessage', 'MessageController@sendmessage');
Route::get('/deleteMessage/{id}', 'MessageController@deleteMessage');
Route::get('/deleteMessageAll/{id}', 'MessageController@deleteMessageAll');
