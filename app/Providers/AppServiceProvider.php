<?php

namespace App\Providers;

use App\Observers\UserActionsObserver;
use App\User_request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        User_request::observe(UserActionsObserver::class);

    }
}
