<?php
/**
 * Created by PhpStorm.
 * User: ebon
 * Date: 7/29/14
 * Time: 4:21 AM
 */

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
     * @return void
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public static function updateValidate(array $input);
} 