<?php

namespace Depotwarehouse\Toolbox\Exceptions;

class TypeMismatchException extends \Exception {

    public function __construct($argument, $parameter, $expected_type) {
        $given_type = gettype($parameter);
        if ($given_type = "object") {
            $given_type = get_class($parameter);
        }

        $message = "Type mismatch exception: {$argument} expected type {$expected_type}, received {$given_type}";
        parent::__construct($message);
    }

} 