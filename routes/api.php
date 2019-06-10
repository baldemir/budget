<?php

use Illuminate\Http\Request;




Route::middleware('auth:api')->group( function () {
    Route::resource('transactions', 'API\TransactionController');
    Route::post('addGarantiTransactions', 'API\TransactionController@addGarantiTransactions');
    Route::post('addCeptetebTransactions', 'API\TransactionController@addCeptetebTransactions');
    Route::post('extensionLogin', 'API\LoginController@extensionLogin');
});


