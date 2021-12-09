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

// Route::get('/', 'PagesController@root')->name('root')->middleware('verified');

Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');

Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group([
    'middleware' => ['auth', 'verified']
], function() {
    // 商品收藏
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

    /** 用户模块 */
    // 收货地址
    Route::group(['namespace' => 'User'], function () {
        Route::get('addresses', 'AddressesController@index')->name('addresses.index');
        Route::get('addresses/create', 'AddressesController@create')->name('addresses.create');
        Route::post('addresses', 'AddressesController@store')->name('addresses.store');
        Route::get('addresses/{address}', 'AddressesController@edit')->name('addresses.edit');
        Route::put('addresses/{address}', 'AddressesController@update')->name('addresses.update');
        Route::delete('addresses/{address}', 'AddressesController@destroy')->name('addresses.destroy');
    });

    /** 订单模块 */
    Route::group(['namespace' => 'Order'], function () {
        Route::get('cart', 'CartController@index')->name('cart.index');
        Route::post('cart', 'CartController@add')->name('cart.add');
        Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

        Route::post('orders', 'OrdersController@store')->name('orders.store');
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
        Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
        Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
        Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
    });

    /** 支付模块 */
    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
    Route::get('payment/{order}/wechat', 'PaymentController@payByWechat')->name('payment.wechat');
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');
Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');

// 跟 products/favorites 冲突, 因此调到最后
Route::get('products/{product}', 'ProductsController@show')->name('products.show');
