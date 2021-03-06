<?php

namespace Tuhra\Uabpay;

use Illuminate\Support\ServiceProvider;

class UabPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/uab.php' => config_path('uab.php'),
        ], 'uab-config');

        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations'),
        ], 'uab-migration');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
