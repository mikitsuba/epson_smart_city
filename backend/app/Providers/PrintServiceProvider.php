<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Authenticate;
use App\Services\CreateJob;
use App\Services\UploadFile;
use App\Services\ExecutePrint;
use App\Services\PrintService;

class PrintServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('PrintService',function($app){
            return new PrintService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
