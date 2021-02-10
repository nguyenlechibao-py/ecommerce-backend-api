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
