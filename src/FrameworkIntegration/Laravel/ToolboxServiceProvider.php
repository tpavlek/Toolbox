<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel;

use Illuminate\Support\ServiceProvider;
use Validator;

class ToolboxServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Validator::extend(
            'alpha_spaces',
            function($attribute, $value) {
                return preg_match('/^[\pL\s]+$/u', $value);
            },
            ":attribute must only have alphabetical characters and spaces"
        );
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
