<?php

namespace Depotwarehouse\Toolbox\DataManagement\Validators;

class BaseValidator implements BaseValidatorInterface {

    /**
     * Validates the input based on creating a new object
     * @param array $input Key-value array of keys and their inputs
     * @return void
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public static function validate(array $input)
    {
    }

    /**
     * Validates the input based on updating an existing object
     * @param array $input Key-value array of keys and their inputs
     * @return void
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public static function updateValidate(array $input)
    {
    }
}