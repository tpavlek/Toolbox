<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel;

use Illuminate\Support\ServiceProvider;

class ErrorPartialServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $this->loadViewsFrom(__DIR__ . '/Views', 'toolbox');

        $this->publishes([
            __DIR__ . '/Views' => base_path('resources/views/vendor/toolbox')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}
