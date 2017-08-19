<?php

use App\Services\LameDb\LameDb;

Route::get('/', function () {
    Artisan::call('tools:lame');
});
