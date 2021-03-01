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
Route::group(['prefix' => 'user'], function() {
    Route::post('/login', 'UserController@login');
    Route::post('/register', 'UserController@register');
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:users']], function () {
    Route::post('/logout', 'UserController@logout');
    Route::post('/profile', 'UserController@profile');
});

Route::group(['middleware' => ['auth:users']], function() {
    Route::get('/orders', 'OrderController@index');
    Route::get('/orders/{id}', 'OrderController@show');
    Route::post('/orders', 'OrderController@store');
    Route::delete('/orders/{id}', 'OrderController@destroy');
});