<?php


Route::group(['prefix' => 'api'], function () {
    Route::get('healthcheck', "HealthController@index");
});
