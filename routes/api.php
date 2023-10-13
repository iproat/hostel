<?php

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

Route::group(['middleware' => 'api', 'prefix' => 'mobile'], function () {
    Route::get('api-test', 'Api\ApiController@index');
    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');

    Route::group(['prefix' => 'attendance'], function () {
        Route::get('index','Api\ReportController@index');
    });

    Route::group(['prefix' => 'leave'], function () {
        Route::get('index', 'Api\LeaveController@index');
        Route::get('store', 'Api\LeaveController@store');
    });
    Route::group(['prefix' => 'permission'], function () {
        Route::get('index', 'Api\LatePermissionController@index');
        Route::get('store','Api\LatePermissionController@store');
    });
});
