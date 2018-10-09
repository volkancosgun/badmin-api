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

    // Product Brand
    Route::get('product/brands', 'Product\ProductBrandController@index');
    Route::get('product/brand/my/{id}', 'Product\ProductBrandController@show');
    Route::post('product/brand/store', 'Product\ProductBrandController@store');

    // Product Model
    Route::get('product/models', 'Product\ProductModelController@index');
    Route::get('product/model/{id}', 'Product\ProductModelController@show');
    Route::get('product/model/brand/{id}', 'Product\ProductModelController@showBrand');
    Route::post('product/model/store', 'Product\ProductModelController@store');

    // Product Taxes
    Route::get('product/taxes', 'Product\ProductTaxController@index');
    Route::post('product/tax/store', 'Product\ProductTaxController@store');

    // Order Model
    // http://127.0.0.1:8000/v1/api/order/list
    Route::get('order/list', 'Order\OrderController@index');
    Route::get('order/my/{id}', 'Order\OrderController@show');
    Route::post('order/store', 'Order\OrderController@store');

    Route::get('order/item/list/{id}', 'Order\OrderItemController@show');
    

});
