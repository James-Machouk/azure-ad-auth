<?php

namespace JamesMachouk\azureAdAuth;

use Illuminate\Support\ServiceProvider;

class AzureAdAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('JamesMachouk\azureAdAuth\AzureAdAuthController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->publishes([
            __DIR__.'/azureAdAuth.php' => config_path('azureAdAuth.php'),
        ]);
    }
}
