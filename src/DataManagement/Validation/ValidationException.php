<?php

namespace Depotwarehouse\Toolbox\DataManagement\Validation;

use Exception;
use Illuminate\Validation\Validator;

class ValidationException extends Exception
{

    /** @var \Illuminate\Support\MessageBag */
    private $errors;

    /**
     * @param string|Validator $container
     */
    public function __construct($container)
    {
        $this->errors = ($container instanceof Validator) ? $container->errors() : $container;
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
