<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', 'PagesController@root')->name('root')->middleware('verified');

Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group([
    'middleware' => ['auth', 'verified'],
    'namespace' => 'User'
], function() {
    Route::get('addresses', 'AddressesController@index')->name('addresses.index');
    Route::get('addresses/create', 'AddressesController@create')->name('addresses.create');
    Route::post('addresses', 'AddressesController@store')->name('addresses.store');
});
