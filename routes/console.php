<?php

use App\Services\LameDb\M3u;
use App\Services\LameDb\ImageProcessor;

Artisan::command('tools:lame', function () {
    (new ImageProcessor)->convertImages();
})->describe('Convert images to picons');

Artisan::command('tools:m3u', function () {
    (new M3u)->createAll();
})->describe('Create .m3u files for all Channels');

