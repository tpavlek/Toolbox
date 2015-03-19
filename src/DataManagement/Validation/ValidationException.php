<?php

namespace Depotwarehouse\Toolbox\DataManagement\Validation;

use Exception;

class ValidationException extends Exception
{

    /** @var \Illuminate\Support\MessageBag */
    private $errors;

    /**
     * @param string|\Illuminate\Validation\Validator $container
     */
    public function __construct($container)
    {
        $this->errors = ($container instanceof \Illuminate\Validation\Validator) ? $container->errors() : $container;
        parent::__construct(null);
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function get()
    {
        return $this->errors;
    }
} 
