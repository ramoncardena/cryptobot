<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
       // View Composers
        view()->composer(
            'partials.notifications', 'App\Http\ViewComposers\NavbarViewComposer'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('App\Settings', function() {

            if (Auth::check()) {
               return Auth::user()->settings();
            }
            else {
                throw new Exception ("You must be authenticated to get your settings");
            }
            

        });

        $this->app->singleton(BittrexServiceProvider::class, function ($app) {

            return new \App\Providers\BittrexServiceProvider;

        });

        $this->app->singleton(BitcoinServiceProvider::class, function ($app) {

            return new \App\Providers\BitcoinServiceProvider;

        });
    }
}
