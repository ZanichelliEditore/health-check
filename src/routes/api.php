<?php


Route::group(['prefix' => 'api'], function () {
    Route::get('health', "HealthController@index");
});
