<?php

namespace Depotwarehouse\Toolbox\Verification;

use Exception;

class ParameterRequiredException extends Exception
{

    /**
     * The name of the parameter that was required, but not present.
     *
     * @var string
     */
    protected $required_parameter;

    public function __construct($parameter)
    {
        $this->required_parameter = $parameter;
        $message = "A piece of data was not properly passed. Check the parameter: {$parameter}";
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getRequiredParameter()
    {
        return $this->required_parameter;
    }

} 
