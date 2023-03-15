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
        $this->app->register(SSOProvider::class);
        $this->app->register(MailServiceProvider::class);
        $this->app->register(OrganizationProvider::class);
    }
}
