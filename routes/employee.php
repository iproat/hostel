<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['preventbackbutton', 'auth']], function () {

    Route::group(['prefix' => 'department'], function () {
        Route::get('/', ['as' => 'department.index', 'uses' => 'Employee\DepartmentController@index']);
        Route::get('/create', ['as' => 'department.create', 'uses' => 'Employee\DepartmentController@create']);
        Route::post('/store', ['as' => 'department.store', 'uses' => 'Employee\DepartmentController@store']);
        Route::get('/{department}/edit', ['as' => 'department.edit', 'uses' => 'Employee\DepartmentController@edit']);
        Route::put('/{department}', ['as' => 'department.update', 'uses' => 'Employee\DepartmentController@update']);
        Route::delete('/{department}/delete', ['as' => 'department.delete', 'uses' => 'Employee\DepartmentController@destroy']);
    });

    Route::group(['prefix' => 'designation'], function () {
        Route::get('/', ['as' => 'designation.index', 'uses' => 'Employee\DesignationController@index']);
        Route::get('/create', ['as' => 'designation.create', 'uses' => 'Employee\DesignationController@create']);
        Route::post('/store', ['as' => 'designation.store', 'uses' => 'Employee\DesignationController@store']);
        Route::get('/{designation}/edit', ['as' => 'designation.edit', 'uses' => 'Employee\DesignationController@edit']);
        Route::put('/{designation}', ['as' => 'designation.update', 'uses' => 'Employee\DesignationController@update']);
        Route::delete('/{designation}/delete', ['as' => 'designation.delete', 'uses' => 'Employee\DesignationController@destroy']);
    });

    Route::group(['prefix' => 'photo'], function () {
        Route::get('/import', ['as' => 'photo.import', 'uses' => 'Employee\EmployeeController@photoimport']);
        Route::post('/importstore', ['as' => 'photo.importstore', 'uses' => 'Employee\EmployeeController@photoimportstore']);
        Route::get('/importstore', ['as' => 'photo.importstore', 'uses' => 'Employee\EmployeeController@photoimportstore']);
        Route::get('/details', ['as' => 'photo.details', 'uses' => 'Employee\EmployeeController@photodetails']);
        Route::get('/delete', ['as' => 'photo.delete', 'uses' => 'Employee\EmployeeController@photodelete']);
    });
    Route::group(['prefix' => 'branch'], function () {
        Route::get('/', ['as' => 'branch.index', 'uses' => 'Employee\BranchController@index']);
        Route::get('/create', ['as' => 'branch.create', 'uses' => 'Employee\BranchController@create']);
        Route::post('/store', ['as' => 'branch.store', 'uses' => 'Employee\BranchController@store']);
        Route::get('/{branch}/edit', ['as' => 'branch.edit', 'uses' => 'Employee\BranchController@edit']);
        Route::put('/{branch}', ['as' => 'branch.update', 'uses' => 'Employee\BranchController@update']);
        Route::delete('/{branch}/delete', ['as' => 'branch.delete', 'uses' => 'Employee\BranchController@destroy']);
    });

    Route::group(['prefix' => 'employee'], function () {
        Route::get('/', ['as' => 'employee.index', 'uses' => 'Employee\EmployeeController@index']);
        Route::post('/import', ['as' => 'employee.import', 'uses' => 'Employee\EmployeeImportController@import']);
        Route::get('/create', ['as' => 'employee.create', 'uses' => 'Employee\EmployeeController@create']);
        Route::post('/store', ['as' => 'employee.store', 'uses' => 'Employee\EmployeeController@store']);
        Route::get('/{employee}/edit', ['as' => 'employee.edit', 'uses' => 'Employee\EmployeeController@edit']);
        Route::get('/{employee}', ['as' => 'employee.show', 'uses' => 'Employee\EmployeeController@show']);
        Route::put('/{employee}', ['as' => 'employee.update', 'uses' => 'Employee\EmployeeController@update']);
        Route::delete('/{employee}/delete', ['as' => 'employee.delete', 'uses' => 'Employee\EmployeeController@destroy']);
    });

    Route::group(['prefix' => 'access'], function () {
        Route::get('/', ['as' => 'access.index', 'uses' => 'Employee\AccessController@index']);
        Route::get('/{employee}/edit', ['as' => 'access.edit', 'uses' => 'Employee\AccessController@edit']);
        Route::post('/store', ['as' => 'access.store', 'uses' => 'Employee\AccessController@store']);
        Route::get('/log', ['as' => 'access.log', 'uses' => 'Employee\AccessController@log']);
        Route::get('/{id}/cloneform', ['as' => 'access.cloneform', 'uses' => 'Employee\AccessController@cloneform']);
        Route::get('/clone', ['as' => 'access.clone', 'uses' => 'Employee\AccessController@clone']);
        Route::post('/clone', ['as' => 'access.clone', 'uses' => 'Employee\AccessController@clone']);
        Route::get('/manualsync', ['as' => 'access.manualsync', 'uses' => 'Employee\AccessController@manualsync']);
    });

    Route::group(['prefix' => 'device'], function () {
        Route::get('/importemployee', ['as' => 'device.importemployee', 'uses' => 'Employee\DeviceController@importemployee']);
    });

    Route::get('/downloadFile', ['as' => 'employee.downloadFile', 'uses' => 'Employee\EmployeeController@downloadFile']);
    Route::get('/exportEmployeeInfo', ['as' => 'employee.exportEmployeeInfo', 'uses' => 'Employee\EmployeeController@export']);
    // Route::post('uploadEmployeeDetails/import', ['as' => 'uploadEmployeeDetails.import', 'uses' => 'Employee\EmployeeImportController@import']);

    Route::group(['prefix' => 'warning'], function () {
        Route::get('/', ['as' => 'warning.index', 'uses' => 'Employee\WarningController@index']);
        Route::get('/create', ['as' => 'warning.create', 'uses' => 'Employee\WarningController@create']);
        Route::post('/store', ['as' => 'warning.store', 'uses' => 'Employee\WarningController@store']);
        Route::get('/{warning}/edit', ['as' => 'warning.edit', 'uses' => 'Employee\WarningController@edit']);
        Route::get('/{warning}', ['as' => 'warning.show', 'uses' => 'Employee\WarningController@show']);
        Route::get('/{warning}', ['as' => 'warning.show', 'uses' => 'Employee\WarningController@show']);
        Route::put('/{warning}', ['as' => 'warning.update', 'uses' => 'Employee\WarningController@update']);
        Route::delete('/{warning}/delete', ['as' => 'warning.delete', 'uses' => 'Employee\WarningController@destroy']);
    });

    Route::group(['prefix' => 'termination'], function () {
        Route::get('/', ['as' => 'termination.index', 'uses' => 'Employee\TerminationController@index']);
        Route::get('/create', ['as' => 'termination.create', 'uses' => 'Employee\TerminationController@create']);
        Route::post('/store', ['as' => 'termination.store', 'uses' => 'Employee\TerminationController@store']);
        Route::get('/{termination}/edit', ['as' => 'termination.edit', 'uses' => 'Employee\TerminationController@edit']);
        Route::get('/{termination}', ['as' => 'termination.show', 'uses' => 'Employee\TerminationController@show']);
        Route::get('/{termination}', ['as' => 'termination.show', 'uses' => 'Employee\TerminationController@show']);
        Route::put('/{termination}', ['as' => 'termination.update', 'uses' => 'Employee\TerminationController@update']);
        Route::delete('/{termination}/delete', ['as' => 'termination.delete', 'uses' => 'Employee\TerminationController@destroy']);
    });

    Route::group(['prefix' => 'permanent'], function () {
        Route::get('/', ['as' => 'permanent.index', 'uses' => 'Employee\EmployeePermanentController@index']);
        Route::get('/updatePermanent', 'Employee\EmployeePermanentController@updatePermanent');
    });
    Route::get('manualsynchronization', ['as' => 'access.manualsynchronization', 'uses' => 'Employee\AccessController@manual']);
    Route::get('cronstatus', ['as' => 'access.cronstatus', 'uses' => 'Employee\AccessController@cronstatus']);
});
