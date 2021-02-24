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
// AUTH ROUTES
Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', 'Admin\AuthController@login');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admins']], function () {
    Route::post('/logout', 'Admin\AuthController@logout');
    Route::post('/profile', 'Admin\AuthController@profile');
    Route::post('/register', 'Admin\AuthController@register');
});

// CATEGORIES ROUTES
Route::prefix('category')->middleware('auth:admins')->group(function () {
    Route::post('/add-new-category', 'CategoryController@store');
});

Route::get('/categories/{id}', 'CategoryController@show');
Route::get('/categories', 'CategoryController@index');

Route::group(['prefix' => 'categories', 'middleware' => ['auth:admins']], function () {
    Route::post('/add', 'CategoryController@store');
    Route::put('/update/{id}', 'CategoryController@update');
    Route::delete('/delete/{id}', 'CategoryController@destroy');
});

// MEDIA ROUTES
Route::middleware(['api'])->prefix('media')->namespace('Admin')->group(function () {
    Route::post('/upload-new-media', 'MediaController@store');
    Route::put('/update/{id}', 'MediaController@update');
    Route::delete('/delete/{id}', 'MediaController@destroy');
});

Route::get('/media', 'Admin\MediaController@index');
Route::get('/media/{id}', 'Admin\MediaController@show');

// TAG ROUTES
Route::get('/tags/{id}', 'Admin\TagController@show');
Route::get('/tags', 'Admin\TagController@index');

Route::group(['prefix' => 'tags', 'middleware' => ['auth:admins'], 'namespace' => 'Admin'], function () {
    Route::post('/add', 'TagController@store');
    Route::put('/update/{id}', 'TagController@update');
    Route::delete('/delete/{id}', 'TagController@destroy');
});

// PRODUCT ROUTES
Route::get('/products/{id}', 'Admin\ProductController@show');
Route::get('/products', 'Admin\ProductController@index');

Route::group(['prefix' => 'products', 'middleware' => ['auth:admins'], 'namespace' => 'Admin'], function () {
    Route::post('/add', 'ProductController@store');
    Route::put('/update/{id}', 'ProductController@update');
    Route::delete('/delete/{id}', 'ProductController@destroy');
});

// ORDER ROUTES
Route::get('/orders/{id}', 'Admin\OrderController@show');
Route::get('/orders', 'Admin\OrderController@index');

Route::group(['prefix' => 'orders', 'middleware' => ['auth:admins'], 'namespace' => 'Admin'], function () {
    Route::post('/add', 'OrderController@store');
    Route::put('/update/{id}', 'OrderController@update');
    Route::delete('/delete/{id}', 'OrderController@destroy');
});