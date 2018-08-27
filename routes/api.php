<?php

Route::group([
    'prefix' => 'v1',
    'middleware' => 'api',

], function ($router) {

    // Auth
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendPasswordResetLink', 'ResetPasswordController@sendEmail');
    Route::post('resetPassword', 'ChangePasswordController@process');

    // Customer Group
    Route::post('customer/group/create', 'CustomerGroupController@store');
    Route::post('customer/group/update/{id}', 'CustomerGroupController@update');
    Route::put('customer/group/updates/{id}', 'CustomerGroupController@update_status');
    Route::get('customer/group/list', 'CustomerGroupController@index');
    Route::get('customer/group/{id}', 'CustomerGroupController@show');
    Route::get('customer/group/delete/{id}', 'CustomerGroupController@destroy');

    Route::get('groups', function() {
        return App\CustomerGroup::paginate();
    });

    // Customer
    Route::post('customer/create', 'CustomerController@store');
    Route::post('customer/update/{id}', 'CustomerController@update');
    Route::get('customer/list', 'CustomerController@index');
    Route::get('customer/{id}', 'CustomerController@show');
    Route::get('customer/delete/{id}', 'CustomerController@destroy');
    
    // Customer Location
    Route::get('customer/location/delete/{id}', 'CustomerController@destroyLocation');
    Route::post('customer/location/create/{id}', 'CustomerController@storeLocation');
    Route::post('customer/location/update/{id}', 'CustomerController@updateLocation');
    Route::get('customer/location/{id}', 'CustomerController@showLocation');
    Route::get('customer/location/my/{id}', 'CustomerController@findLocations');

    // Product
    Route::get('product/list', 'Product\ProductController@index');
    Route::get('product/my/{id}', 'Product\ProductController@show');
    Route::post('product/store', 'Product\ProductController@store');

    // Product Image
    Route::post('product/uploadImage', 'Product\ProductPhotoController@store');
    Route::get('product/photos/{id}', 'Product\ProductPhotoController@show');
    Route::get('product/photos/delete/{id}', 'Product\ProductPhotoController@destroy');

    // Product Category
    Route::get('product/categories', 'Product\ProductCategoryController@index');
    Route::get('product/category/{id}', 'Product\ProductCategoryController@show');
    Route::post('product/category/store', 'Product\ProductCategoryController@store');
    

});
