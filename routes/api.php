<?php

Route::group([
    'prefix' => 'v1',
    'middleware' => 'api',

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendPasswordResetLink', 'ResetPasswordController@sendEmail');
    Route::post('resetPassword', 'ChangePasswordController@process');

    // Customer Group Routes

    Route::post('customer/group/create', 'CustomerGroupController@store');
    Route::post('customer/group/update/{id}', 'CustomerGroupController@update');
    Route::get('customer/group/list', 'CustomerGroupController@index');
    Route::get('customer/group/{id}', 'CustomerGroupController@show');
    Route::get('customer/group/delete/{id}', 'CustomerGroupController@destroy');

    // Customer Routes
    Route::post('customer/create', 'CustomerController@store');
    Route::post('customer/update/{id}', 'CustomerController@update');
    Route::get('customer/list', 'CustomerController@index');
    Route::get('customer/{id}', 'CustomerController@show');
    Route::get('customer/delete/{id}', 'CustomerController@destroy');

});
