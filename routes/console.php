<?php

use App\Services\LameDb\ImageProcessor;
use App\Services\LameDb\LameDb;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('tools:lame', function () {
    (new ImageProcessor)->convertImages();
})->describe('Display an inspiring quote');

