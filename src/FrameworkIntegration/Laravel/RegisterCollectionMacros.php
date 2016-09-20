<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel;

use Illuminate\Support\Collection;

class RegisterCollectionMacros
{

    public function __invoke()
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

}
