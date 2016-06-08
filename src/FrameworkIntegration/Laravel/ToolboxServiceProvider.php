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
        Collection::macro('toAssoc', function () {
            return $this->reduce(function ($items, $pair) {
                list($key, $value) = $pair;
                return $items->put($key, $value);
            }, new static);
        });

        Collection::macro('combinations', function($combineWith, $keys = [0, 1]) {
            return $this->reduce(function ($combinations, $originalItem) use ($combineWith, $keys) {
                return $combinations->push($combineWith->map(function ($otherItem) use ($originalItem, $keys) {
                    return [ $keys[0] => $originalItem, $keys[1] => $otherItem ];
                }));
            }, new static)
                ->flatten(1);
        });

        Collection::macro('pipe', function ($callback) {
            return $callback($this);
        });

        Collection::macro('head', function($headCallback, $tailCallback = null) {
            if ($tailCallback === null) {
                $tailCallback = function($arg) { return $arg; };
            }

            return $this->slice(0, 1)->map($headCallback)->merge($this->slice(1)->map($tailCallback));
        });
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
