<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['preventbackbutton', 'auth']], function () {

    Route::group(['prefix' => 'OfficeManagement'], function () {
        
        Route::get('/', ['as' => 'reminder.index', 'uses' => 'Reminder\ReminderController@index']);
        Route::get('expired', ['as' => 'reminder.expired', 'uses' => 'Reminder\ReminderController@expired']);
        Route::get('/create', ['as' => 'reminder.create', 'uses' => 'Reminder\ReminderController@create']);
        Route::post('/store', ['as' => 'reminder.store', 'uses' => 'Reminder\ReminderController@store']);
        Route::get('/{reminder}/edit', ['as' => 'reminder.edit', 'uses' => 'Reminder\ReminderController@edit']);
        Route::put('/{reminder}', ['as' => 'reminder.update', 'uses' => 'Reminder\ReminderController@update']);
        Route::delete('/{reminder}/delete', ['as' => 'reminder.delete', 'uses' => 'Reminder\ReminderController@destroy']);
        Route::get('download', ['as' => 'reminder.documentdownload', 'uses' => 'Reminder\ReminderController@download']);


         Route::get('settings','Reminder\GeneralSettingsController@settings')->name('reminder.settings');
        Route::post('settings','Reminder\GeneralSettingsController@settingsstore')->name('reminder.settings.store');


    });
    
});
