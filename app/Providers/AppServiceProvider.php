<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('App\Settings', function() {

            // CHANGE TO AUTH USER
            return User::first()->settings();

        });

        $this->app->singleton(BittrexServiceProvider::class, function ($app) {

            return new \App\Providers\BittrexServiceProvider;

        });

        $this->app->singleton(BitcoinServiceProvider::class, function ($app) {

            return new \App\Providers\BitcoinServiceProvider;

        });
    }
}
