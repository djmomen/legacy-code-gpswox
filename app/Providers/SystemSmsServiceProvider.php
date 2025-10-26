<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tobuli\Services\SystemSmsService;

class SystemSmsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SystemSmsService::class, function ($app) {
            return new SystemSmsService();
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SystemSmsService::class];
    }
}
