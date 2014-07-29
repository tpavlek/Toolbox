<?php

namespace Depotwarehouse\Toolbox;


use Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException;

class Verification {

    static function require_set(array $array, array $attributes) {
        foreach ($attributes as $attribute) {
            if (!array_key_exists($attribute, $array) || $array[$attribute] === null) {
                throw new ParameterRequiredException($attribute);
            }
        }
    }

} 