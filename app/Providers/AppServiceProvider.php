<?php

namespace App\Providers;

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
        // helper
        $appPath = __DIR__.'/../';
        foreach (glob($appPath.'Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }
}
