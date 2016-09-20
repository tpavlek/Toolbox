<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel;

use Illuminate\Support\Collection;
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

        Validator::extend(
            'alpha_num_hyphen',
            function($attribute, $value) {
                return preg_match('/^[A-Za-z-]+/', $value);
            },
            ":attribute must only have alphanumeric characters and hyphens"
        );

        $this->registerCollectionMacros();
    }

    public static function registerCollectionMacros()
    {
        (new RegisterCollectionMacros())->__invoke();
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
