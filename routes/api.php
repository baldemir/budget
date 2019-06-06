<?php

use Illuminate\Http\Request;




Route::group(['middleware' => 'auth:api'], function() {
    Route::get('getTransactions', 'API\TransactionController@index');
    Route::get('setTransaction', 'API\TransactionController@store');
});