<?php

Route::get('/', 'Home@index');

Route::group(['prefix' => 'tools'], function () {
    Route::get('/', 'Tools@index');
});

Route::group(['prefix' => 'lamedb'], function () {
    Route::post('/export', 'LameDb@export');
});

