<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\Bitcoin\Bitcoin;

class BitcoinServiceProvider extends ServiceProvider
{
    protected $client;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('bitcoin', function () {
            return new Bitcoin();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Bitcoin::class];
    }

   
}
