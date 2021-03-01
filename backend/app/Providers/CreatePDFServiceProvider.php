<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CreatePDFServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CreatePDF',function($app){
            return new CreatePDFService();
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
