<?php

Route::get('/', 'Home@index');

Route::group(['prefix' => 'tools'], function () {
    Route::get('/', 'Tools@index');
});

Route::group(['prefix' => 'm3u'], function () {
    Route::post('/lamedb2m38', 'M3u@lamedb2m38');

    Route::post('/lamedb2csv', 'M3u@lamedb2csv');
});
