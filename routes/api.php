<?php

use Illuminate\Http\Request;




Route::group(['middleware' => 'auth:api'], function() {
    Route::get('set/transaction', 'API\TransactionController@index');
});