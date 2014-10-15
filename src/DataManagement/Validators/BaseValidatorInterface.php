<?php

namespace Depotwarehouse\Toolbox\DataManagement\Validators;

interface BaseValidatorInterface {

    /**
     * Validates the input based on creating a new object
     * @param array $input Key-value array of keys and their inputs
     * @return void
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public static function validate(array $input);


    /**
     * Validates the input based on updating an existing object
     * @param array $input Key-value array of keys and their inputs
     * @param mixed $current_id The ID of the current model being updated.
     * @return void
     */
    public static function updateValidate(array $input, $current_id);
} 