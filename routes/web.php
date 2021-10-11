<?php




Route::group(['namespace' => 'Tuhra\Uabpay\Http\Controllers'], function() {
    Route::get('/uab_callback', 'UabController@uabcallback');
});

	