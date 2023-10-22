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

use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', 'PaymentController@index')->name('index');
Route::get('/home', 'PaymentController@index')->name('home');
Route::post('/', 'PaymentController@load')->name('load');
Route::get('/delete/{id}', 'PaymentController@delete')->name('delete');
Route::get('/sendmoney/{message}', 'PaymentController@sendMoneyToArtist');
