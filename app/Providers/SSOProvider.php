<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class SSOProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'App\Library\Repository\SSOServiceInterface',
            'App\Library\SSOService'
        );
    }
}
