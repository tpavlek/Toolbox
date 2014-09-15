<?php

namespace Depotwarehouse\Toolbox;


use DataAccess\Validators\ValidationException;
use Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException;

class Verification {

    /**
     * Requires that a set of attributes are present and set on an array
     * @param array $array Array of items
     * @param array $attributes The attributes which must be set on the array
     * @throws Exceptions\ParameterRequiredException
     */
    static function require_set(array $array, array $attributes) {
        foreach ($attributes as $attribute) {
            if (!array_key_exists($attribute, $array) || is_null($array[$attribute]) || (is_string($array[$attribute])  && $array[$attribute] == "")) {
                throw new ParameterRequiredException($attribute);
            }
        }
    }


    /**
     * Filters an array based on its keys starting with a string
     * @param array $array The array of key => values to be filtered
     * @param string $pattern A string representing the start of the desired key(s)
     * @return array The array filtered by key
     */
    static function array_filter_starts_with(array $array, $pattern) {
        $results = array();
        array_walk($array, function ($value, $key) use ($pattern, &$results) {
            if (starts_with($key, $pattern)) {
                $results[$key] = $value;
            }
        });

        return $results;
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