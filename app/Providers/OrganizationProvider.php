<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class OrganizationProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'App\Library\Repository\OrganizationServiceInterface',
            'App\Library\OrganizationService'
        );
    }
}
