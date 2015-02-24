<?php

namespace Depotwarehouse\Toolbox\Verification;

/**
 * Requires that a set of attributes are present and set on an array
 *
 * @param array $array Array of items
 * @param array $attributes The attributes which must be set on the array
 * @throws ParameterRequiredException
 */
function require_set(array $array, array $attributes)
{
    foreach ($attributes as $attribute) {
        if (
            !array_key_exists($attribute, $array) || is_null($array[$attribute]) || (is_string($array[$attribute]) && $array[$attribute] == "")
        ) {
            throw new ParameterRequiredException($attribute);
        }
    }
}


/**
 * Filters an array based on its keys starting with a string
 *
 * @param array  $array   The array of key => values to be filtered
 * @param string $pattern A string representing the start of the desired key(s)
 * @return array The array filtered by key
 */
function array_filter_starts_with(array $array, $pattern)
{
    $results = array();
    array_walk($array, function ($value, $key) use ($pattern, &$results) {
        if (starts_with($key, $pattern)) {
            $results[$key] = $value;
        }
    });

    return $results;
}

/**
 * Filters an array by removing all of the key => value pairs for which the value is null.
 *
 * @param array $array
 * @return array
 */
function array_filter_null(array $array)
{
    return array_filter($array, '\Depotwarehouse\Toolbox\Verification\is_not_null');
}

/**
 * Checks that the value is not null.
 *
 * @param mixed $var
 * @return bool
 */
function is_not_null($var)
{
    return !is_null($var);
}
