<?php

use Illuminate\Http\Request;




Route::middleware('auth:api')->group( function () {
    Route::resource('transactions', 'API\TransactionController');
    Route::post('addGarantiTransactions', 'API\TransactionController@addGarantiTransactions');
    Route::post('addCeptetebTransactions', 'API\TransactionController@addCeptetebTransactions');
    Route::post('addZiraatTransactions', 'API\TransactionController@addZiraatTransactions');

    Route::get('getDailyTransactions', 'API\TransactionController@getDailyTransactions');
    Route::get('getMonthlyTransactions', 'API\TransactionController@getMonthlyTransactions');
    Route::get('getMonthlyEarnings', 'API\TransactionController@getMonthlyEarnings');
    Route::get('getMonthlySpendings', 'API\TransactionController@getMonthlySpendings');

    Route::get('getMonthlySummary', 'API\TransactionController@getMonthlySummary');
    Route::get('getMonthlyCategories', 'API\TransactionController@getMonthlyCategories');
    Route::get('getUserCategories', 'API\TransactionController@getUserTags');
    Route::get('getUser', 'API\TransactionController@getUser');
    Route::post('setUserImage', 'API\TransactionController@setUserImage');
    Route::post('saveTransaction', 'API\TransactionController@saveTransaction');

    Route::get('getMonthlyTransactionsByCategory', 'API\TransactionController@getMonthlyTransactionsByCategory');

    Route::post('/updateSpending/{spending}', 'API\TransactionController@updateSpending');
    Route::post('/updateEarning/{earning}', 'API\TransactionController@updateEarning');

});


Route::post('extensionLogin', 'API\LoginController@extensionLogin');
Route::post('loginWithFacebookToken', 'API\LoginController@loginWithFacebookAccessToken');