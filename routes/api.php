<?php

use Illuminate\Http\Request;

//
Route::group(['middleware' => 'auth:api'], function() {
	Route::post('set/transaction', 'ApiController@setTransaction');
});


Route::middleware('auth:api')->group( function () {
    Route::resource('set/transaction', 'API\ProductController');
});