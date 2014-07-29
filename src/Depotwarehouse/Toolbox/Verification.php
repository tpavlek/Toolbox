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

    static function array_filter_null(array $array) {
        return array_filter($array, "self::is_not_null");
    }

    private static function is_not_null($var) {
        return !is_null($var);
    }
} 