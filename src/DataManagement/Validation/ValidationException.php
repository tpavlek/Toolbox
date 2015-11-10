<?php

namespace Depotwarehouse\Toolbox\DataManagement\Validation;

use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

class ValidationException extends Exception
{

    /** @var \Illuminate\Support\MessageBag */
    public $errors;

    public $input_data;

    public function __construct(MessageBag $errors, array $inputData = [ ])
    {
        $this->errors = $errors;
        $this->input_data = $inputData;
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function get()
    {
        return $this->errors;
    }

    public function inputData()
    {
        return $this->input_data;
    }

    public static function fromValidator(Validator $validator)
    {
        return new self($validator->errors(), $validator->getData());
    }

    public static function fromMessageBag(MessageBag $errors, array $inputData = [])
    {
        return new self($errors, $inputData);
    }
}
