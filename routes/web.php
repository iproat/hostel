<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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
// Developer Routes

Route::get('/clear-all-cache', function () {
    $cache  = Artisan::call('cache:clear');
    $view   = Artisan::call('view:clear');
    $route  = Artisan::call('route:clear');
    $config = Artisan::call('config:cache');
    return redirect('/login'); //Return anything
});

// front page route

Route::get('/', 'Front\WebController@index');
Route::get('job/{id}/{slug?}', 'Front\WebController@jobDetails')->name('job.details');
Route::post('job-application', 'Front\WebController@jobApply')->name('job.application');

Route::get('testReport', 'View\ManualAttendanceReportController@attendance');

// front page route

Route::get('login', 'User\LoginController@index');
Route::post('login', 'User\LoginController@Auth');

Route::get('mail', 'User\HomeController@mail');

Route::group(['middleware' => ['preventbackbutton', 'auth']], function () {

    Route::get('expe', 'ExpeController@index')->name('expe');

    Route::get('sample/{employee_id}', 'SampleController@sample');
    Route::get('ot/{date}', 'OverTime\OverTimeController@samp');
    Route::get('dashboard', 'User\HomeController@index');
    Route::get('profile', 'User\HomeController@profile');
    Route::get('logout', 'User\LoginController@logout');
    Route::resource('user', 'User\UserController', ['parameters' => ['user' => 'user_id']]);
    Route::resource('userRole', 'User\RoleController', ['parameters' => ['userRole' => 'role_id']]);
    Route::resource('rolePermission', 'User\RolePermissionController', ['parameters' => ['rolePermission' => 'id']]);
    Route::post('rolePermission/get_all_menu', 'User\RolePermissionController@getAllMenu');
    Route::resource('changePassword', 'User\ChangePasswordController', ['parameters' => ['changePassword' => 'id']]);
});

Route::group(['prefix' => 'cronjob'], function () {
    Route::get('logrun', 'View\EmployeeAttendaceController@fetchRawLog');
    Route::get('attendance', 'View\EmployeeAttendaceController@attendance');
    Route::get('manualLogrun', 'View\ManualAttendanceReportController@fetchRawLog');
    // Route::get('manualLog', 'Employee\AccessController@log')->name('cronjob.manualLog');
    // Route::get('/manualLog', ['as' => 'cronjob.manualLog', 'uses' => 'Employee\AccessController@log']);
    Route::get('manualAttendance', 'View\ManualAttendanceReportController@attendance');
    Route::get('trainingEmployee', 'View\ManualAttendanceReportController@training');
    Route::get('newEmployees', 'View\EmployeeAttendaceController@newEmployee');
});

Route::get('pushtosqlserver', 'Employee\AccessController@push_into_sqlserver')->name('pushtosqlserver');
Route::get('testlog', 'Employee\DeviceController@testlog');
Route::post('testlog', 'Employee\DeviceController@testlog');
Route::get('instantlog', 'Employee\DeviceController@testlog');
Route::post('instantlog', 'Employee\DeviceController@testlog');


Route::get('local/{language}', function ($language) {

    session(['my_locale' => $language]);

    return redirect()->back();
});

Route::get('/table', function () {
    \set_time_limit(0);
    $ms_sql = DB::table('ms_sql')->get();
    foreach ($ms_sql as $key => $value) {
        // dd($value);

        \App\Model\EmployeeAttendance::create([
            'employee_attendance_id' => $value->primary_id,
            'finger_print_id' => $value->ID,
            'in_out_time' => $value->datetime,
            'type' => $value->type,
            'employee_id' => $value->employee,
            'device' => $value->device,
            'live_status' => $value->live_status,
            'status' => $value->status,
            'device_employee_id' => $value->device_employee_id,
        ]);
    }
});



Route::post('logs', 'Attendance\DeviceConfigurationController@logs');
Route::get('logs', 'Attendance\DeviceConfigurationController@logs');
Route::post('dashboard', 'User\HomeController@index')->name('dashboard');
