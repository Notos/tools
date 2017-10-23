<?php

Route::get('/lame', function () {
    Artisan::call('tools:lame');
});

Route::get('/m3u', function () {
    Artisan::call('tools:m3u');
});
