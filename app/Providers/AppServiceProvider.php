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

        // Custom validator rule
        \Validator::extend('invited', function($attribute, $value, $parameters, $validator){

            $invitationCode = $value;
            $invitationEmail = $parameters[0];

            $invitationStatus = \Invi::status($invitationCode, $invitationEmail);

            if($invitationStatus=='valid'){
                return true;
            }
            
            return false;
        });
        // \Validator::resolver(function($translator, $data, $rules, $messages)
        // {
        //     $messages['invite'] = 'Phone number format should be 954-555-1234';
        //     return new \Validator($translator, $data, $rules, $messages);
        // });
        \Validator::replacer('invited', function($message, $attribute, $rule, $parameters) {
            $message = 'Your invitation code is not valid';
            return $message;
        });
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
