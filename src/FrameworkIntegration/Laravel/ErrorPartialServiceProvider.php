<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel;

use Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers\ErrorPartialComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory;

class ErrorPartialServiceProvider extends ServiceProvider
{

    public function boot(Factory $view)
    {

        $this->loadViewsFrom(__DIR__ . '/Views', 'toolbox');

        $this->publishes([
            __DIR__ . '/Views' => base_path('resources/views/vendor/toolbox')
        ]);

        // We need to register our view composer for errorPartial to properly set all variables
        $view->composer('vendor.toolbox.errors.errorPartial', ErrorPartialComposer::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
