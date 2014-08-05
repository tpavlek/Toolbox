<?php

namespace Depotwarehouse\Toolbox;


use DataAccess\Validators\ValidationException;
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


    public static function getOpValuePair($string) {
        $operation = array();
        $value = array();
        if (preg_match('/^[<>=]/', $string, $operation)) {
            if (preg_match('/[A-Za-z0-9]$/', $string, $value)) {
                return [ 'op' => $operation[0], 'value' => $value[0] ];
            }
            throw new ParameterRequiredException("String must end with [A-Za-z0-9], given: " . $string);
        }
        throw new ParameterRequiredException("String must start with [<>=], given: " . $string);
    }
} 