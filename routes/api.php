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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::post('register', 'Auth\AuthController@register');
    Route::post('verify', 'Auth\AuthController@verifyOtp');
    Route::get('countries', 'Auth\CountryController@countries');
    Route::get('states/{countryId}', 'Auth\StateController@states');
    Route::get('cities/{stateId}', 'Auth\CityController@cities');
    /*Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'Auth\AuthController@logout');
        Route::get('user', 'Auth\AuthController@user');
        Route::get('customers', 'Auth\AuthController@customers');
    });*/
    Route::middleware('auth:api')->group(function () {
        Route::post('createCustomer', 'Auth\AuthController@register');
        Route::get('customers', 'Auth\AuthController@customers');
        Route::get('customersByType/{user_type}', 'Auth\AuthController@customerByType');
        Route::get('customer/{customerId}', 'Auth\AuthController@customer');
        Route::patch('uploadImage/{customerId}', 'Auth\AuthController@uploadImage');
        Route::patch('updateCustomer/{id}', 'Auth\AuthController@updateUser');
        Route::delete('deleteCustomer/{id}', 'Auth\AuthController@deleteUser');
        Route::get('items', 'Auth\ItemController@index');
        Route::post('createItem', 'Auth\ItemController@store');
        Route::get('item/{itemId}/{columnName?}', 'Auth\ItemController@item');
        Route::get('itemsTime', 'Auth\ItemController@itemsTime');
        Route::patch('updateItem/{id}', 'Auth\ItemController@updateitem');
        Route::delete('deleteItem/{id}', 'Auth\ItemController@deleteitem');
        Route::get('itemcategory/{id}', 'Auth\ItemController@itemByCategory');
        Route::post('createPrice', 'Auth\PriceController@store');
        Route::get('getPrice/{userId}/{itemId?}', 'Auth\PriceController@getPrice');
        Route::get('orders/count', 'Auth\OrderController@orderCount');
        Route::get('orders/{offset?}', 'Auth\OrderController@index');
        Route::post('createOrder', 'Auth\OrderController@store');
        Route::get('orderByUser/{orderId?}', 'Auth\OrderController@orderByUser');
        Route::delete('deleteOrder/{id}', 'Auth\OrderController@deleteOrder');
        Route::get('orderTotal', 'Auth\OrderController@totalOrderPrice');
        Route::post('createLucky', 'Auth\LuckypriceController@store');
        Route::get('luckyPrice', 'Auth\LuckypriceController@index');
        Route::get('luckyPrice/{id}', 'Auth\LuckypriceController@show');
        Route::put('luckyPrice/{id}', 'Auth\LuckypriceController@update');
        Route::delete('luckyPrice/{id}', 'Auth\LuckypriceController@delete');
        Route::post('winner', 'Auth\WinnerController@store');
        Route::get('winner', 'Auth\WinnerController@index');
        Route::post('userResult', 'Auth\ResultController@index');
        Route::get('category','Auth\CategoryController@index');
        Route::get('category/{id}','Auth\CategoryController@show');
        Route::post('category','Auth\CategoryController@store');
        Route::patch('category/{id}', 'Auth\CategoryController@update');
        Route::delete('category/{id}', 'Auth\CategoryController@destroy');
    });
});