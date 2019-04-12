<?php

use Illuminate\Http\Request;

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

Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');
Route::get('products/{id}', 'ProductController@show');
Route::get('products-list', 'ProductController@listProducts');
Route::get('categories', 'CategoriesController@index');
Route::get('categories/{id}', 'CategoriesController@show');
Route::get('products/category/{id}', 'CategoriesController@productsByCategories');
Route::get('products/search/{search}', 'ProductController@search');
 
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'ApiController@logout');
    Route::get('user', 'ApiController@getAuthUser');
   
    Route::put('categories/{id}', 'CategoriesController@update');
    Route::post('categories', 'CategoriesController@store');
    Route::delete('categories/{id}', 'CategoriesController@destroy');
    
    Route::get('products', 'ProductController@index');  //lista de productos del usuario logueado
    Route::post('products', 'ProductController@store');
    Route::post('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');

    

    Route::get('shopping', 'ShoppingCarController@index');
    Route::post('shopping', 'ShoppingCarController@store');
    Route::delete('shopping/{id}', 'ShoppingCarController@destroy');
    Route::get('shopping/search/{status}', 'ShoppingCarController@shoppingCarStatus');

    Route::get('checkout', 'ShoppingCarController@checkoutCar');
    Route::post('checkout', 'ShoppingCarController@checkoutList');
});