<?php

namespace App\Providers;

use Validator;
use App\Services\LameDb\LameDb;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('lamedb', function ($attribute, $value, $parameters, $validator) {
            $valid = true;

            try {
                LameDb::factoryFromFile($file = $value->getPathName());
            } catch (\Exception $exception) {
                $valid = false;
            }

            return $valid;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
