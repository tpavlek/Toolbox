<?php

namespace Depotwarehouse\Toolbox\Operations;
use Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException;

class Operations {

    /**
     * Returns a pair of the operation and it's right-hand-side value, based on a string passed in.
     *
     * For example, given the string "<=9" the return would be [ 'op' => '<=', 'value' => 9 ].
     * Supported operations are
     * # <
     * # >
     * # <=
     * # >=
     * # =
     * The value section is required to be alphanumeric only.
     * @param string $string Arbitrary string to parse for operations and values
     * @throws InvalidArgumentException
     * @return array Contains operations an values eg. [ 'op' => '=', 'value' => 12 ]
     */
    public static function getOpValuePair($string) {
        $operation = array();
        $value = array();
        $allowedValues = "[A-Za-z0-9]"; // The allowed characters in a value string

        if (preg_match("/^([<>]((?={$allowedValues})|[^<>])|[=]((?={$allowedValues})|[^=<>]))/", $string, $operation)) {
            if (preg_match("/{$allowedValues}+$/", $string, $value)) {
                return [ 'op' => $operation[0], 'value' => $value[0] ];
            }
            throw new InvalidArgumentException("String must end with [A-Za-z0-9], given: " . $string);
        }
        throw new InvalidArgumentException("String must start with [<>=], given: " . $string);
    }

    /**
     * Returns an array of Operations from an array of filters.
     *
     * The filters array takes the form of:
     * ```.language-php
     * [
     *      'some_key' => '<9'
     * ]
     * ```
     *
     * The key value may optionally have a colon-delimited list of relationships to include along the way eg.
     * 'some_obj:some_key'. The left-hand-side is anything parseable by getOpValuePair above.
     *
     * @param array $filters
     * @return Operation[]
     * @throws InvalidArgumentException
     */
    public static function getOperationsFromArrayOfFilters(array $filters) {
        $operations = [];

        foreach ($filters as $key => $value) {
            $op_pair = static::getOpValuePair($value);
            $operation = new Operation($key, $op_pair['op'], $op_pair['value']);
            $operations[] = $operation;
        }
        return $operations;
    }

}